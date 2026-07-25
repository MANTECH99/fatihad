<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Shop;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Shop $shop)
    {

        // ✅ Récupérer le plan (sécurisé avec ?? 'free')
        //  $userPlan = auth()->user()->plan ?? 'free';

        // ✅ Vérification : accès refusé SEULEMENT si le plan est 'free'
        //   if ($userPlan === 'free') {
            // Afficher le bloc Premium au lieu de la page des ventes
        //   return view('merchant.partials.premium-block');
        //  }

        $this->authorize('view', $shop);

        // Ventes physiques
        $physicalSales = Sale::where('shop_id', $shop->id)
            ->where('channel', 'physical')
            ->latest()
            ->get();

        // Ventes en ligne (commandes payées)
        $onlineOrders = Order::where('shop_id', $shop->id)
            ->where('payment_status', 'paid')
            ->with('items')
            ->latest()
            ->get();

        // Stats
        $totalPhysical = Sale::where('shop_id', $shop->id)->where('channel', 'physical')->sum('total');
        $totalOnline = Order::where('shop_id', $shop->id)->where('payment_status', 'paid')->sum('total');
        $totalSales = $totalPhysical + $totalOnline;
        $nbPhysical = Sale::where('shop_id', $shop->id)->where('channel', 'physical')->count();
        $nbOnline = Order::where('shop_id', $shop->id)->where('payment_status', 'paid')->count();
        $totalTransactions = $nbPhysical + $nbOnline;

        // Toutes les ventes récentes (physiques + en ligne)
        $recentSales = $this->getRecentSales($shop);

        $products = Product::where('shop_id', $shop->id)
            ->where('is_available', true)
            ->get();

// Calcul du bénéfice sur les ventes physiques ET en ligne
        $totalProfit = 0;

// Bénéfice physique
        $physicalSales = Sale::where('shop_id', $shop->id)
            ->where('channel', 'physical')
            ->with('product')
            ->get();

        foreach ($physicalSales as $sale) {
            if ($sale->product && $sale->product->cost_price) {
                $totalProfit += ($sale->price - $sale->product->cost_price) * $sale->quantity;
            }
        }

// Bénéfice en ligne
        $onlineOrders = Order::where('shop_id', $shop->id)
            ->where('payment_status', 'paid')
            ->with('items.product')
            ->get();

        foreach ($onlineOrders as $order) {
            foreach ($order->items as $item) {
                if ($item->product && $item->product->cost_price) {
                    $totalProfit += ($item->price - $item->product->cost_price) * $item->quantity;
                }
            }
        }

        return view('merchant.sales.index', compact(
            'shop', 'physicalSales', 'onlineOrders', 'recentSales',
            'totalPhysical', 'totalOnline', 'totalSales',
            'nbPhysical', 'nbOnline', 'totalTransactions', 'products','totalProfit'
        ));
    }

    public function store(Request $request, Shop $shop)
    {
        $this->authorize('update', $shop);

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'customer_name' => 'nullable|string|max:255',   // ← AJOUTER
            'customer_phone' => 'nullable|string|max:20',    // ← AJOUTER
        ]);

        $product = Product::findOrFail($validated['product_id']);

        $sale = Sale::create([
            'shop_id' => $shop->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'price' => $product->current_price,
            'quantity' => $validated['quantity'],
            'total' => $product->current_price * $validated['quantity'],
            'channel' => 'physical',
            'customer_name' => $validated['customer_name'] ?? null,   // ← AJOUTER
            'customer_phone' => $validated['customer_phone'] ?? null, // ← AJOUTER
        ]);

        // Décrémenter le stock
        if ($product->track_inventory) {
            $product->decrement('stock', $validated['quantity']);

            // Enregistrer le mouvement de sortie
            \App\Models\StockMovement::create([
                'shop_id' => $shop->id,
                'product_id' => $product->id,
                'type' => 'sortie',
                'quantity' => $validated['quantity'],
                'reason' => 'Vente physique #' . $sale->id,
            ]);
        }

        return redirect()->back()->with('success', 'Vente enregistrée avec succès !');
    }

    public function destroy(Shop $shop, Sale $sale)
    {
        $this->authorize('delete', $shop);

        // Restaurer le stock
        $product = $sale->product;
        if ($product && $product->track_inventory) {
            $product->increment('stock', $sale->quantity);

            // Enregistrer le mouvement de retour
            \App\Models\StockMovement::create([
                'shop_id' => $shop->id,
                'product_id' => $sale->product_id,
                'type' => 'return',
                'quantity' => $sale->quantity,
                'reason' => 'Annulation vente physique #' . $sale->id,
            ]);
        }

        $sale->delete();

        return redirect()->back()->with('success', 'Vente supprimée.');
    }
    private function getRecentSales($shop)
    {
        $physical = Sale::where('shop_id', $shop->id)
            ->where('channel', 'physical')
            ->with('product')
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($sale) {
                $costPrice = $sale->product->cost_price ?? null;
                $profit = 0;
                if ($costPrice) {
                    $profit = ($sale->price - $costPrice) * $sale->quantity;
                }
                return [
                    'name' => $sale->product_name,
                    'date' => $sale->created_at,
                    'channel' => 'Physique',
                    'amount' => $sale->total,
                    'cost_price' => $costPrice,  // ← AJOUTER
                    'profit' => $profit,
                    'type' => 'physical',
                    'id' => $sale->id,
                    'customer_name' => $sale->customer_name,   // ← AJOUTER
                    'customer_phone' => $sale->customer_phone,  // ← AJOUTER
                ];
            });

        $online = Order::where('shop_id', $shop->id)
            ->where('payment_status', 'paid')
            ->with('items.product')
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($order) {
                $profit = 0;
                $costPrice = null;
                foreach ($order->items as $item) {
                    if ($item->product && $item->product->cost_price) {
                        $costPrice = $item->product->cost_price;
                        $profit += ($item->price - $costPrice) * $item->quantity;
                    }
                }
                return [
                    'name' => $order->items->first()->product_name ?? 'Commande #' . $order->order_number,
                    'date' => $order->created_at,
                    'channel' => 'En ligne',
                    'amount' => $order->total,
                    'cost_price' => $costPrice,  // ← AJOUTER
                    'profit' => $profit,
                    'type' => 'online',
                    'id' => $order->id,
                    'customer_name' => $order->customer_name,    // ← AJOUTER
                    'customer_phone' => $order->customer_phone,  // ← AJOUTER
                ];
            });

        return collect($physical->toArray())->merge($online->toArray())->sortByDesc('date')->take(10);
    }
}
