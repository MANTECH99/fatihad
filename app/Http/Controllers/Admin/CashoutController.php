<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashoutLog;
use App\Services\DexpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CashoutController extends Controller
{
    protected DexpayService $dexpay;

    public function __construct(DexpayService $dexpay)
    {
        $this->dexpay = $dexpay;
    }

    public function index()
    {
        $logs = CashoutLog::latest()->paginate(20);
        $balance = $this->getBalance();

        return view('admin.cashout.index', compact('logs', 'balance'));
    }

    public function initiate(Request $request)
    {
        $request->validate([
            'phone'    => 'required|string',
            'amount'   => 'required|numeric|min:250',
            'operator' => 'required|in:wave_sn_payout,om_sn_payout',
            'code_2fa' => 'required|string|size:6',
        ]);

        $phone = $this->formatPhone($request->phone);

        if (!$phone) {
            return back()->with('error', 'Numéro de téléphone invalide.');
        }

        // Vérifier le code 2FA
        $user = auth()->user();
        $google2fa = app('pragmarx.google2fa');
        $valid = $google2fa->verifyKey($user->google2fa_secret, $request->code_2fa);

        if (!$valid) {
            return back()->with('error', 'Code 2FA invalide.');
        }

        // Vérifier le solde
        $soldeDisponible = $this->getBalance();

        if (($request->amount * 1.015) > $soldeDisponible) {
            return back()->with('error', 'Solde insuffisant. Votre solde disponible est de ' . number_format($soldeDisponible, 0) . ' FCFA.');
        }

        $operatorLabels = [
            'wave_sn_payout'           => 'Wave',
            'orange_money_sn_payout'   => 'Orange Money',
            'free_money_sn_payout'     => 'Free Money',
        ];

        $operatorName = $operatorLabels[$request->operator] ?? $request->operator;
        $recipientName = 'Retrait admin ' . $operatorName;

        try {
            $result = $this->dexpay->createPayout([
                'phone'          => $phone,
                'amount'         => (int) $request->amount,
                'currency'       => 'XOF',
                'operator'       => $request->operator,
                'countryISO'     => 'SN',
                'recipient_name' => $recipientName,
                'order_number'   => 'SENESHOP-CASHOUT-' . time(),
            ]);

            $externalId = $result['reference'] ?? $result['payout_id'] ?? ('MANUAL-' . time());

            CashoutLog::create([
                'admin_id'     => auth()->id(),
                'service_code' => $request->operator,
                'phone'        => $phone,
                'amount'       => $request->amount,
                'external_id'  => $externalId,
                'status'       => $result['success'] ? 'success' : 'failed',
                'response'     => json_encode($result),
            ]);

            if ($result['success']) {
                return back()->with('success', 'Envoi de ' . number_format($request->amount) . ' FCFA effectué avec succès.');
            }

            return back()->with('error', $result['message'] ?? 'Échec du cashout.');

        } catch (\Exception $e) {
            Log::error('Cashout Error:', ['message' => $e->getMessage()]);

            return back()->with('error', 'Erreur de connexion au service.');
        }
    }

    /**
     * Webhook callback pour les payouts
     */
    public function callback(Request $request)
    {
        Log::info('Payout Callback:', $request->all());

        $payload = $request->all();
        $event = $payload['event'] ?? null;
        $data  = $payload['data'] ?? $payload;

        // Trouver le log par reference ou payout_id
        $reference = $data['reference'] ?? $data['payout_id'] ?? null;

        if ($reference) {
            $log = CashoutLog::where('external_id', $reference)->first();

            if ($log) {
                $status = $data['status'] ?? 'PENDING';

                $successStatuses = ['completed', 'COMPLETED', 'success', 'SUCCESS'];
                $log->update([
                    'status'            => in_array($status, $successStatuses) ? 'success' : 'failed',
                    'callback_response' => json_encode($payload),
                ]);
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Liste des payouts (historique)
     */
    public function payouts()
    {
        $logs = CashoutLog::whereNotNull('admin_id')->latest()->paginate(20);
        return view('admin.cashout.payouts', compact('logs'));
    }

    private function formatPhone($phone)
    {
        $digits = preg_replace('/\D/', '', $phone);
        $last9 = substr($digits, -9);

        if (strlen($last9) === 9 && preg_match('/^(77|78|76|70|75|33)/', $last9)) {
            return $last9;
        }

        return null;
    }

    private function getBalance()
    {
        try {
            // Tenter de récupérer le solde depuis l'API Dexpay
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'x-api-key'    => config('services.dexpay.api_key'),
                'x-api-secret' => config('services.dexpay.api_secret'),
            ])->get(config('services.dexpay.api_url') . '/balances');

            if ($response->successful()) {
                $data = $response->json();
                $balances = $data['data'] ?? [];

                // Chercher le solde XOF (Sénégal)
                foreach ($balances as $balance) {
                    if (($balance['currency'] ?? '') === 'XOF') {
                        return $balance['available'] ?? $balance['balance'] ?? 0;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Get balance error:', ['error' => $e->getMessage()]);
        }

        // Fallback : calcul basé sur les logs
        try {
            $recharges = CashoutLog::whereIn('service_code', ['OM_SN_CASHOUT', 'WAVE_SN_CASHOUT', 'FM_SN_CASHOUT', 'WIZALL_SN_CASHOUT', 'wave_sn', 'orange_money_sn'])
                ->where('status', 'success')
                ->sum('amount');

            $retraits = CashoutLog::whereIn('service_code', ['OM_SN_CASHIN', 'WAVE_SN_CASHIN', 'FM_SN_CASHIN', 'WIZALL_SN_CASHIN', 'wave_sn_payout', 'orange_money_sn_payout', 'free_money_sn_payout'])
                ->where('status', 'success')
                ->sum('amount');

            return ($recharges * 0.985) - ($retraits * 1.015);
        } catch (\Exception $e) {
            return 0;
        }
    }
}
