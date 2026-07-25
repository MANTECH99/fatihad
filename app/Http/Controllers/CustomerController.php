<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Shop;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Shop $shop, Request $request)
    {
        $this->authorize('view', $shop);

        $query = Customer::where('shop_id', $shop->id);

// Filtres existants + nouveaux
        if ($request->filter === 'top') {
            $query->orderBy('total_orders', 'desc');
        } elseif ($request->filter === 'big_spenders') {
            $query->orderBy('total_spent', 'desc');
        } elseif ($request->filter === 'inactive') {
            $query->where('last_order_at', '<', now()->subMonths(3));
        } elseif ($request->filter === 'recent') {
            $query->orderBy('last_order_at', 'desc');
        } elseif ($request->filter === 'high_value') {
            $query->where('total_orders', '>', 0)->orderByRaw('total_spent / total_orders DESC');
        } elseif ($request->filter === 'frequent') {
            $query->where('total_orders', '>=', 5);
        } elseif ($request->filter === 'new') {
            $query->where('total_orders', 1);
        }

        $customers = $query->paginate(20);

        $stats = [
            'total_customers' => Customer::where('shop_id', $shop->id)->count(),
            'new_this_month' => Customer::where('shop_id', $shop->id)->whereMonth('created_at', now()->month)->count(),
            'avg_basket' => Customer::where('shop_id', $shop->id)->where('total_orders', '>', 0)->avg(\DB::raw('total_spent / total_orders')),
            'retention_rate' => Customer::where('shop_id', $shop->id)->where('total_orders', '>', 1)->count(),
        ];

// Taux de rétention
        $totalWithOrders = Customer::where('shop_id', $shop->id)->where('total_orders', '>', 0)->count();
        $stats['retention_rate'] = $totalWithOrders > 0 ? round(($stats['retention_rate'] / $totalWithOrders) * 100) : 0;

        return view('merchant.customers.index', compact('shop', 'customers', 'stats'));
    }



    public function show(Shop $shop, Customer $customer)
    {
        $this->authorize('view', $shop);

        $orders = Order::where('shop_id', $shop->id)
            ->where('customer_phone', 'like', '%' . $customer->phone . '%')
            ->with('items')
            ->latest()
            ->get();

        $topProducts = $orders->flatMap->items
            ->groupBy('product_name')
            ->map(fn($items) => $items->sum('quantity'))
            ->sortDesc()
            ->take(5);

        $firstOrder = $orders->last();

        return view('merchant.customers.show', compact('shop', 'customer', 'orders', 'topProducts', 'firstOrder'));
    }

    public function export(Shop $shop)
    {
        $this->authorize('view', $shop);

        $customers = Customer::where('shop_id', $shop->id)->get();

        $filename = 'clients_' . $shop->slug . '_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($customers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Nom', 'Téléphone', 'Email', 'Commandes', 'Total dépensé', 'Dernière commande']);
            foreach ($customers as $c) {
                fputcsv($file, [$c->name, $c->phone, $c->email, $c->total_orders, $c->total_spent, $c->last_order_at]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
