<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\AbandonedCart;
use Illuminate\Http\Request;

class CartReminderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Shop $shop)
    {
        $this->authorize('view', $shop);

        $carts = AbandonedCart::where('shop_id', $shop->id)
            ->latest()
            ->paginate(20);

        $stats = [
            'total' => AbandonedCart::where('shop_id', $shop->id)->count(),
            'recovered' => AbandonedCart::where('shop_id', $shop->id)->where('recovered', true)->count(),
            'reminded' => AbandonedCart::where('shop_id', $shop->id)->where('reminder_sent', true)->count(),
            'pending' => AbandonedCart::where('shop_id', $shop->id)->where('reminder_sent', false)->where('recovered', false)->count(),
        ];

        return view('merchant.carts.abandoned', compact('shop', 'carts', 'stats'));
    }
}
