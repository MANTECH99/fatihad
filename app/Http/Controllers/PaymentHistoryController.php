<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Shop;

class PaymentHistoryController extends Controller
{
    public function index(Shop $shop)
    {

        // ✅ Récupérer le plan (sécurisé avec ?? 'free')
        //   $userPlan = auth()->user()->plan ?? 'free';

        // ✅ Vérification : accès refusé SEULEMENT si le plan est 'free'
        //  if ($userPlan === 'free') {
            // Afficher le bloc Premium au lieu de la page des ventes
        //   return view('merchant.partials.premium-block');
        //  }
        // Total encaissé pour cette boutique
        $totalWave = Order::where('shop_id', $shop->id)
            ->where('payment_method', 'wave')
            ->where('payment_status', 'paid')
            ->sum('total');

        $totalOM = Order::where('shop_id', $shop->id)
            ->where('payment_method', 'orange_money')
            ->where('payment_status', 'paid')
            ->sum('total');

        $totalCOD = Order::where('shop_id', $shop->id)
            ->where('payment_method', 'cash_on_delivery')
            ->where('payment_status', 'paid')
            ->sum('total');

        $totalEncaissé = $totalWave + $totalOM + $totalCOD;

        // Historique
        $paiements = Order::where('shop_id', $shop->id)
            ->where('payment_status', 'paid')
            ->latest()
            ->paginate(7);

        return view('merchant.payments', compact('totalEncaissé', 'totalWave', 'totalOM', 'totalCOD', 'paiements', 'shop'));
    }
}
