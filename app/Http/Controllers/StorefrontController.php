<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Shop;
use App\Models\Product;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class StorefrontController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function show(Shop $shop)
    {
        // Vérifier si le propriétaire a un trial expiré
        if ($shop->user && $shop->user->trial_ends_at && $shop->user->trial_ends_at->isPast()) {
            $isOwner = auth()->check() && auth()->id() === $shop->user_id;
            return view('storefront.expired', compact('shop', 'isOwner'));
        }

        if (!$shop->is_active || $shop->status !== 'approved') {
            $isOwner = auth()->check() && auth()->id() === $shop->user_id;
            return view('storefront.pending', compact('shop', 'isOwner'));
        }

        $categories = $shop->categories()
            ->where('is_active', true)
            ->with(['products' => function($query) {
                $query->where('is_available', true)->orderBy('order');
            }])
            ->orderBy('order')
            ->get();

        $featuredProducts = $shop->products()
            ->where('is_available', true)
            ->where('is_featured', true)
            ->take(8)
            ->get();

        $reviews = $shop->reviews()
            ->where('is_approved', true)
            ->latest()
            ->take(10)
            ->get();

        return view('storefront.show', compact('shop', 'categories', 'featuredProducts', 'reviews'));
    }

    public function checkout(Shop $shop)
    {

        // Vérifier si le propriétaire a un trial expiré
        if ($shop->user && $shop->user->trial_ends_at && $shop->user->trial_ends_at->isPast()) {
            $isOwner = auth()->check() && auth()->id() === $shop->user_id;
            return view('storefront.expired', compact('shop', 'isOwner'));
        }

        $cart = Session::get('cart.' . $shop->id, []);

        if (empty($cart)) {
            return redirect()->route('storefront.show', $shop)
                ->with('error', 'Votre panier est vide.');
        }

        $cartItems = $this->getCartItems($shop, $cart);

        if ($cartItems->isEmpty()) {
            Session::forget('cart.' . $shop->id);
            return redirect()->route('storefront.show', $shop)
                ->with('error', 'Certains produits ne sont plus disponibles.');
        }

        $subtotal = $cartItems->sum(function($item) {
            return $item->current_price * $item->cart_quantity;
        });
/*
        // Sauvegarder le panier abandonné si le téléphone est connu
        $customerPhone = session('checkout_phone_' . $shop->id);
        if ($customerPhone && !empty($cart)) {
            \App\Models\AbandonedCart::updateOrCreate(
                [
                    'shop_id' => $shop->id,
                    'customer_phone' => $customerPhone,
                    'recovered' => false,
                    'reminder_sent' => false,
                ],
                [
                    'customer_name' => session('checkout_name_' . $shop->id),
                    'cart_items' => $cart,
                    'total' => $subtotal + $shop->delivery_fee,
                ]
            );
        }
*/
        $total = $subtotal + $shop->delivery_fee;

        // Estimation des frais si paiement mobile
        $estimatedPaymentFee = (int) round(($subtotal + $shop->delivery_fee) * 0.0303);

        $deliveryZones = $shop->delivery_zones ?? []; // ← AJOUTER

        return view('storefront.checkout', compact('shop', 'cartItems', 'subtotal', 'total', 'estimatedPaymentFee', 'deliveryZones'));
    }

    public function placeOrder(Request $request, Shop $shop)
    {
        $cart = Session::get('cart.' . $shop->id, []);

        if (empty($cart)) {
            return redirect()->route('storefront.show', $shop)
                ->with('error', 'Votre panier est vide.');
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email',
            'customer_address' => 'required|string',
            'customer_note' => 'nullable|string',
            'payment_method' => 'required|in:cash_on_delivery,wave,orange_money,card',
            'delivery_zone' => 'nullable|string',        // ← AJOUTER
            'delivery_fee_amount' => 'required|numeric', // ← AJOUTER
        ]);

        $cartItems = $this->getCartItems($shop, $cart);

        if ($cartItems->isEmpty()) {
            Session::forget('cart.' . $shop->id);
            return redirect()->route('storefront.show', $shop)
                ->with('error', 'Certains produits ne sont plus disponibles.');
        }

        $subtotal = $cartItems->sum(function($item) {
            return $item->current_price * $item->cart_quantity;
        });

        // Utiliser le delivery_fee_amount du formulaire
        $deliveryFee = $validated['delivery_fee_amount'];

        // Frais de paiement mobile
        $paymentFee = 0;
        if (in_array($validated['payment_method'], ['wave', 'orange_money'])) {
            $paymentFee = (int) round(($subtotal + $deliveryFee) * 0.0303); // ← $deliveryFee au lieu de $shop->delivery_fee
        }

        $total = $subtotal + $deliveryFee + $paymentFee; // ← $deliveryFee au lieu de $shop->delivery_fee

        // Vérifier le minimum de commande
        if ($shop->min_order > 0 && $subtotal < $shop->min_order) {
            return redirect()->back()->with('error',
                'Le montant minimum de commande est de ' . number_format($shop->min_order, 0, ',', ' ') . ' FCFA.');
        }

        try {
            DB::beginTransaction();

            $order = Order::create([
                'shop_id' => $shop->id,
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'customer_email' => $validated['customer_email'] ?? null,
                'customer_address' => $validated['customer_address'],
                'customer_note' => $validated['customer_note'] ?? null,
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,                          // ← Modifié
                'delivery_zone' => $validated['delivery_zone'] ?? null, // ← AJOUTER
                'payment_fee' => $paymentFee,
                'total' => $total,
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'pending',
                'order_status' => 'pending',
            ]);

            // Créer les lignes de commande
            foreach ($cartItems as $item) {
                $order->items()->create([
                    'product_id' => $item->id,
                    'product_name' => $item->name,
                    'price' => $item->current_price,
                    'quantity' => $item->cart_quantity,
                    'options' => $item->cart_options ?? null,
                    'subtotal' => $item->current_price * $item->cart_quantity,
                ]);
            }

            DB::commit();

            $this->syncCustomer($order); // ← AJOUTER

            // Marquer le panier abandonné comme récupéré
            \App\Models\AbandonedCart::where('shop_id', $shop->id)
                ->where('customer_phone', $validated['customer_phone'])
                ->where('recovered', false)
                ->update(['recovered' => true]);

// Nettoyer la session
            session()->forget(['checkout_phone_' . $shop->id, 'checkout_name_' . $shop->id]);


// Décrémenter le stock et enregistrer les mouvements
            foreach ($cartItems as $item) {
                $product = Product::find($item->id);
                if ($product && $product->track_inventory) {
                    // Vérifier si le stock est suffisant
                    if ($product->stock >= $item->cart_quantity) {
                        $product->decrement('stock', $item->cart_quantity);

                        // Enregistrer le mouvement de stock
                        \App\Models\StockMovement::create([
                            'shop_id' => $shop->id,
                            'product_id' => $product->id,
                            'type' => 'sortie',
                            'quantity' => $item->cart_quantity,
                            'reason' => 'Commande #' . $order->order_number . ' - Vente en ligne',
                        ]);
                    }
                }
            }

            // Vider le panier
            Session::forget('cart.' . $shop->id);





            // Envoyer notification WhatsApp
            // try {
            //     $this->whatsappService->sendOrderNotification($order);
            //   } catch (\Exception $e) {
                // Log error but continue
            // }


// Si paiement Wave ou Orange Money, rediriger vers Dexpay
            if (in_array($validated['payment_method'], ['wave', 'orange_money'])) {
                return redirect()->route('payment.init', [
                    'shop' => $shop->slug,
                    'order' => $order->order_number,
                ]);
            }



            return redirect()->route('storefront.order.confirmation', [
                'shop' => $shop->slug,
                'order' => $order->order_number
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la commande. Veuillez réessayer.')
                ->withInput();
        }
    }

    public function orderConfirmation(Shop $shop, $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('shop_id', $shop->id)
            ->with('items')
            ->firstOrFail();

        return view('storefront.confirmation', compact('shop', 'order'));
    }

    // API Cart
    public function addToCart(Request $request, Shop $shop)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:99',
            'options' => 'nullable',
        ]);

        $product = Product::where('id', $validated['product_id'])
            ->where('shop_id', $shop->id)
            ->where('is_available', true)
            ->first();

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Produit non disponible.'], 404);
        }

        // Décoder les options si c'est du JSON
        $options = $validated['options'] ?? null;
        if (is_string($options)) {
            $options = json_decode($options, true);
        }
        if (!is_array($options)) {
            $options = [];
        }

        $cart = Session::get('cart.' . $shop->id, []);
        $cartKey = $product->id . '_' . md5(json_encode($options));

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $validated['quantity'];
        } else {
            $cart[$cartKey] = [
                'product_id' => $product->id,
                'quantity' => $validated['quantity'],
                'options' => $options,
            ];
        }

        Session::put('cart.' . $shop->id, $cart);

        $cartCount = array_sum(array_column($cart, 'quantity'));

        return response()->json([
            'success' => true,
            'message' => 'Produit ajouté au panier !',
            'cart_count' => $cartCount,
        ]);
    }

    public function updateCart(Request $request, Shop $shop)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.key' => 'required|string',
            'items.*.quantity' => 'required|integer|min:0|max:99',
        ]);

        $cart = Session::get('cart.' . $shop->id, []);

        foreach ($validated['items'] as $item) {
            if ($item['quantity'] <= 0) {
                unset($cart[$item['key']]);
            } elseif (isset($cart[$item['key']])) {
                $cart[$item['key']]['quantity'] = $item['quantity'];
            }
        }

        Session::put('cart.' . $shop->id, $cart);

        return response()->json(['success' => true, 'cart' => $cart]);
    }

    public function removeFromCart(Request $request, Shop $shop)
    {
        $cart = Session::get('cart.' . $shop->id, []);
        unset($cart[$request->key]);
        Session::put('cart.' . $shop->id, $cart);

        return response()->json(['success' => true]);
    }

    public function getCart(Shop $shop)
    {
        $cart = Session::get('cart.' . $shop->id, []);
        $cartItems = $this->getCartItems($shop, $cart);

        $subtotal = $cartItems->sum(function($item) {
            return $item->current_price * $item->cart_quantity;
        });

        return response()->json([
            'items' => $cartItems,
            'count' => $cartItems->sum('cart_quantity'),
            'subtotal' => $subtotal,
            'total' => $subtotal + $shop->delivery_fee,
            'delivery_fee' => $shop->delivery_fee,
        ]);
    }

    protected function getCartItems($shop, $cart)
    {
        if (empty($cart)) {
            return collect();
        }

        $productIds = array_column($cart, 'product_id');

        $products = Product::whereIn('id', $productIds)
            ->where('shop_id', $shop->id)
            ->where('is_available', true)
            ->get()
            ->keyBy('id');

        $cartItems = collect();

        foreach ($cart as $key => $item) {
            if (isset($products[$item['product_id']])) {
                $product = clone $products[$item['product_id']];
                $product->cart_key = $key;
                $product->cart_quantity = $item['quantity'];
                $product->cart_options = $item['options'] ?? null;
                $cartItems->push($product);
            }
        }

        return $cartItems;
    }

    public function product(Shop $shop, Product $product)
    {

        // Vérifier si le propriétaire a un trial expiré
        if ($shop->user && $shop->user->trial_ends_at && $shop->user->trial_ends_at->isPast()) {
            $isOwner = auth()->check() && auth()->id() === $shop->user_id;
            return view('storefront.expired', compact('shop', 'isOwner'));
        }
        return view('storefront.product', compact('shop', 'product'));
    }




    private function syncCustomer($order)
    {
        $phone = preg_replace('/[^0-9]/', '', $order->customer_phone);

        $customer = Customer::firstOrNew(
            ['shop_id' => $order->shop_id, 'phone' => $phone],
            ['name' => $order->customer_name, 'email' => $order->customer_email]
        );

        $customer->total_orders = $customer->total_orders + 1;
        $customer->total_spent = $customer->total_spent + $order->total;
        $customer->last_order_at = now();

        // Tag automatique
        if ($customer->total_orders >= 10) {
            $customer->tag = 'VIP';
        } elseif ($customer->total_orders >= 3) {
            $customer->tag = 'Régulier';
        } else {
            $customer->tag = 'Nouveau';
        }

        $customer->save();
    }

    public function savePhone(Request $request, Shop $shop)
    {
        session(['checkout_phone_' . $shop->id => $request->phone]);
        session(['checkout_name_' . $shop->id => $request->name]);
        return response()->json(['success' => true]);
    }
}
