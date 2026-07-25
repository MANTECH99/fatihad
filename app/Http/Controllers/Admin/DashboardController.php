<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $stats = [
            'total_shops' => Shop::count(),
            'active_shops' => Shop::where('is_active', true)->count(),
            'pending_shops' => Shop::where('status', 'pending')->count(),
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'total_users' => User::count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total'),
            'orders_today' => Order::whereDate('created_at', today())->count(),
            'revenue_today' => Order::whereDate('created_at', today())
                ->where('payment_status', 'paid')
                ->sum('total'),
            'orders_this_month' => Order::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'revenue_this_month' => Order::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->where('payment_status', 'paid')
                ->sum('total'),
        ];

        // Commandes récentes
        $recentOrders = Order::with(['shop', 'items'])
            ->latest()
            ->take(10)
            ->get();

        // Top boutiques
        $topShops = Shop::withCount('orders')
            ->withSum(['orders as total_revenue' => function($query) {
                $query->where('payment_status', 'paid');
            }], 'total')
            ->orderByDesc('orders_count')
            ->take(10)
            ->get();

        // Graphique des commandes (7 derniers jours)
        $ordersChart = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total) as total')
        )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.dashboard', compact(
            'stats', 'recentOrders', 'topShops', 'ordersChart'
        ));
    }



    public function subscriptions()
    {
        $subscriptions = Subscription::with('user')
            ->latest()
            ->paginate(20);

        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    public function cancelSubscription(Subscription $subscription)
    {
        $subscription->update(['status' => 'cancelled']);

        $subscription->user->update([
            'plan' => 'free',
            'trial_ends_at' => null,
        ]);

        return back()->with('success', 'Abonnement annulé avec succès.');
    }
}
