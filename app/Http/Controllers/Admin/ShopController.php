<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $query = Shop::with('user')->withCount(['orders', 'products']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('city', 'like', "%{$request->search}%");
            });
        }

        $shops = $query->latest()->paginate(20);

        return view('admin.shops.index', compact('shops'));
    }

    public function show(Shop $shop)
    {
        $shop->load(['user', 'categories', 'products', 'orders' => function($query) {
            $query->latest()->take(10);
        }, 'reviews']);

        $shop->loadCount(['orders', 'products', 'categories']);

        return view('admin.shops.show', compact('shop'));
    }

    public function approve(Shop $shop)
    {
        $shop->update(['status' => 'approved']);
        return redirect()->back()->with('success', 'Boutique approuvée avec succès.');
    }

    public function reject(Request $request, Shop $shop)
    {
        $shop->update([
            'status' => 'rejected',
        ]);

        return redirect()->back()->with('success', 'Boutique rejetée.');
    }

    public function toggleActive(Shop $shop)
    {
        $shop->update(['is_active' => !$shop->is_active]);

        $status = $shop->is_active ? 'activée' : 'désactivée';
        return redirect()->back()->with('success', "Boutique {$status} avec succès.");
    }

    public function destroy(Shop $shop)
    {
        $shop->delete();
        return redirect()->route('admin.shops.index')
            ->with('success', 'Boutique supprimée avec succès.');
    }
}
