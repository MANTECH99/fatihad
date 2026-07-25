<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\StockMovement;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
        $this->middleware('auth');
    }

    public function index(Shop $shop, Request $request)
    {
        $this->authorize('view', $shop);

        $query = $shop->orders()->with('items');

        // Filtres
        if ($request->status) {
            $query->where('order_status', $request->status);
        }

        if ($request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->paginate(20)->appends($request->query());

        // Stats
        $stats = [
            'total' => $shop->orders()->count(),
            'pending' => $shop->orders()->where('order_status', 'pending')->count(),
            'processing' => $shop->orders()->whereIn('order_status', ['confirmed', 'preparing', 'ready'])->count(),
            'delivered' => $shop->orders()->where('order_status', 'delivered')->count(),
            'today' => $shop->orders()->whereDate('created_at', today())->count(),
            'revenue_today' => $shop->orders()
                ->whereDate('created_at', today())
                ->where('payment_status', 'paid')
                ->sum('total'),
        ];

        return view('merchant.orders.index', compact('shop', 'orders', 'stats'));
    }

    public function show(Shop $shop, Order $order)
    {
        $this->authorize('view', $shop);

        $order->load(['items.product', 'shop']);

        return view('merchant.orders.show', compact('shop', 'order'));
    }

    public function updateStatus(Request $request, Shop $shop, Order $order)
    {
        $this->authorize('update', $shop);

        $validated = $request->validate([
            'order_status' => 'required|in:pending,confirmed,preparing,ready,out_for_delivery,delivered,cancelled,rejected',
            'delivery_person' => 'nullable|string|max:255',
            'delivery_person_phone' => 'nullable|string|max:20',
            'note' => 'nullable|string',
        ]);

        $oldStatus = $order->order_status;

        $order->updateStatus(
            $validated['order_status'],
            $validated['note'] ?? null
        );

        if ($validated['delivery_person']) {
            $order->update([
                'delivery_person' => $validated['delivery_person'],
                'delivery_person_phone' => $validated['delivery_person_phone'] ?? null,
            ]);
        }

        // Gestion du stock : annulation ou rejet
        if (in_array($validated['order_status'], ['cancelled', 'rejected'])
            && !in_array($oldStatus, ['cancelled', 'rejected'])) {
            // Restaurer le stock pour chaque produit
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product && $product->track_inventory) {
                    $product->increment('stock', $item->quantity);

                    StockMovement::create([
                        'shop_id' => $shop->id,
                        'product_id' => $product->id,
                        'type' => 'return',
                        'quantity' => $item->quantity,
                        'reason' => 'Commande #' . $order->order_number . ' ' . $validated['order_status'],
                    ]);
                }
            }
        }

        // Gestion du stock : réactivation d'une commande annulée/rejetée
        if (!in_array($validated['order_status'], ['cancelled', 'rejected'])
            && in_array($oldStatus, ['cancelled', 'rejected'])) {
            // Re-décrémenter le stock
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product && $product->track_inventory) {
                    if ($product->stock >= $item->quantity) {
                        $product->decrement('stock', $item->quantity);

                        StockMovement::create([
                            'shop_id' => $shop->id,
                            'product_id' => $product->id,
                            'type' => 'sortie',
                            'quantity' => $item->quantity,
                            'reason' => 'Commande #' . $order->order_number . ' réactivée',
                        ]);
                    }
                }
            }
        }

        // Envoyer notification WhatsApp au client
        // try {
        //   $this->whatsappService->sendOrderStatusUpdate($order);
        //  } catch (\Exception $e) {
            // Log error but don't stop the process
        //  }

        return response()->json([
            'success' => true,
            'message' => 'Statut mis à jour avec succès.',
            'status' => $order->getStatusLabel(),
            'status_color' => $order->getStatusColor(),
        ]);
    }

    public function updatePayment(Request $request, Shop $shop, Order $order)
    {
        $this->authorize('update', $shop);

        $validated = $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded',
        ]);

        $order->update([
            'payment_status' => $validated['payment_status']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Statut de paiement mis à jour.',
        ]);
    }

    public function destroy(Shop $shop, Order $order)
    {
        $this->authorize('delete', $shop);

        $order->items()->delete();
        $order->delete();

        return redirect()->route('merchant.orders.index', $shop)
            ->with('success', 'Commande supprimée avec succès.');
    }

    public function export(Shop $shop, Request $request)
    {
        $this->authorize('view', $shop);

        $orders = $shop->orders()
            ->with('items')
            ->whereDate('created_at', '>=', $request->date_from ?? now()->subMonth())
            ->whereDate('created_at', '<=', $request->date_to ?? now())
            ->get();

        $filename = 'commandes_' . $shop->slug . '_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');

            // En-têtes
            fputcsv($file, [
                'N° Commande',
                'Date',
                'Client',
                'Téléphone',
                'Adresse',
                'Produits',
                'Total',
                'Paiement',
                'Statut',
            ]);

            foreach ($orders as $order) {
                $products = $order->items->map(function($item) {
                    return $item->quantity . 'x ' . $item->product_name;
                })->implode(' | ');

                fputcsv($file, [
                    $order->order_number,
                    $order->created_at->format('d/m/Y H:i'),
                    $order->customer_name,
                    $order->customer_phone,
                    $order->customer_address,
                    $products,
                    number_format($order->total, 0, ',', ' ') . ' FCFA',
                    $order->getPaymentMethodLabel(),
                    $order->getStatusLabel(),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }


    public function invoice(Shop $shop, Order $order)
    {
        $this->authorize('view', $shop);
        $order->load('items');
        return view('merchant.orders.invoice', compact('shop', 'order'));
    }
}
