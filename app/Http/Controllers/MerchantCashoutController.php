<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CashoutLog;
use App\Models\Shop;
use Illuminate\Http\Request;

class MerchantCashoutController extends Controller
{
    public function index(Shop $shop)
    {
        if ($shop->user_id !== auth()->id()) {
            abort(403);
        }

        $waveNumber = preg_replace('/\D/', '', $shop->wave_number ?? '');
        $omNumber = preg_replace('/\D/', '', $shop->orange_money_number ?? '');
        $whatsappPhone = preg_replace('/\D/', '', $shop->whatsapp_phone ?? '');

        $waveLast9 = substr($waveNumber, -9);
        $omLast9 = substr($omNumber, -9);
        $whatsappLast9 = substr($whatsappPhone, -9);

        // Récupérer tous les cashouts vers ce shop
        $allPayments = CashoutLog::whereIn('service_code', [
            'wave_sn_payout',
            'om_sn_payout',
            'free_money_sn_payout'
        ])
            ->where('status', 'success')
            ->where(function ($query) use ($waveNumber, $waveLast9, $omNumber, $omLast9, $whatsappPhone, $whatsappLast9) {
                if ($waveNumber) $query->orWhere('phone', 'LIKE', '%' . $waveLast9);
                if ($omNumber) $query->orWhere('phone', 'LIKE', '%' . $omLast9);
                if ($whatsappPhone) $query->orWhere('phone', 'LIKE', '%' . $whatsappLast9);
            })
            ->latest()
            ->get();

        // Pour chaque cashout, retrouver le client via le log entrant
        $allPayments->transform(function ($payment) use ($shop) {
            // Chercher un log de paiement entrant proche en date (5 min avant)
            $logEntrant = CashoutLog::where('shop_id', $shop->id)
                ->whereNull('admin_id')
                ->whereNotIn('service_code', ['wave_sn_payout', 'om_sn_payout', 'free_money_sn_payout'])
                ->where('created_at', '<=', $payment->created_at)
                ->where('created_at', '>=', $payment->created_at->subMinutes(5))
                ->latest()
                ->first();

            $payment->client_phone = $logEntrant ? $logEntrant->phone : 'Client';
            $payment->montant_recu = $payment->amount; // Déjà net, pas de frais à retirer

            return $payment;
        });

        $totalRecu = $allPayments->sum('amount');
        $nombreTransactions = $allPayments->count();
        $balance = $totalRecu;

        return view('merchant.cashout.index', compact(
            'shop',
            'allPayments',
            'totalRecu',
            'nombreTransactions',
            'balance'
        ));
    }
}
