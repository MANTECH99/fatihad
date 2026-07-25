<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MerchantDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $allShops = $user->shops;
        $currentShopId = $request->shop_id;

        // Premier chargement sans paramètre : rediriger vers la première boutique
        if ($currentShopId === null) {
            $firstShop = $allShops->first();
            if ($firstShop) {
                session(['current_shop_id' => $firstShop->id]);
                return redirect()->route('merchant.dashboard', ['shop_id' => $firstShop->id]);
            }
        }

        if ($currentShopId && $currentShopId !== 'all') {
            session(['current_shop_id' => $currentShopId]);
            $shops = $allShops->where('id', $currentShopId);
        } else {
            session(['current_shop_id' => 'all']);
            $shops = $allShops;
        }



        $shopIds = $shops->pluck('id');
        $currentShop = $currentShopId ? $allShops->find($currentShopId) : null;

        $totalOrders = Order::whereIn('shop_id', $shopIds)->count();
        $totalRevenue = Order::whereIn('shop_id', $shopIds)->where('payment_status', 'paid')->sum('total');
        $pendingOrders = Order::whereIn('shop_id', $shopIds)->where('order_status', 'pending')->count();

        // Données graphiques
        $ordersChartLabels = [];
        $ordersChartData = [];
        $revenueChartLabels = [];
        $revenueChartData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $ordersChartLabels[] = $date->format('d/m');
            $revenueChartLabels[] = $date->format('d/m');

            $ordersChartData[] = Order::whereIn('shop_id', $shopIds)
                ->whereDate('created_at', $date)->count();

            $revenueChartData[] = Order::whereIn('shop_id', $shopIds)
                ->whereDate('created_at', $date)
                ->where('payment_status', 'paid')->sum('total');
        }

        // Statuts
        $statusLabels = ['En attente', 'Confirmée', 'En livraison', 'Livrée', 'Annulée'];
        $statusData = [
            Order::whereIn('shop_id', $shopIds)->where('order_status', 'pending')->count(),
            Order::whereIn('shop_id', $shopIds)->where('order_status', 'confirmed')->count(),
            Order::whereIn('shop_id', $shopIds)->where('order_status', 'delivering')->count(),
            Order::whereIn('shop_id', $shopIds)->where('order_status', 'delivered')->count(),
            Order::whereIn('shop_id', $shopIds)->where('order_status', 'cancelled')->count(),
        ];

        // Par boutique
        $shopsChartLabels = $shops->pluck('name')->toArray();
        $shopsChartData = [];
        foreach ($shops as $s) {
            $shopsChartData[] = Order::where('shop_id', $s->id)
                ->where('payment_status', 'paid')->sum('total');
        }

        return view('merchant.dashboard', compact(
            'shops',              // ← C'est $shops (filtré ou toutes)
            'allShops',           // ← Gardé pour le compteur
            'currentShop',
            'currentShopId',  // ← AJOUTER
            'totalOrders', 'totalRevenue', 'pendingOrders',
            'ordersChartLabels', 'ordersChartData',
            'revenueChartLabels', 'revenueChartData',
            'statusLabels', 'statusData',
            'shopsChartLabels', 'shopsChartData'
        ));
    }
}
