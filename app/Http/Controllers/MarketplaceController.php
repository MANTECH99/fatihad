<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceSubscription;
use App\Models\CashoutLog;
use App\Services\DexpayService;
use App\Services\PlanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class MarketplaceController extends Controller
{
    protected DexpayService $dexpay;

    public function __construct(DexpayService $dexpay)
    {
        $this->dexpay = $dexpay;
    }

    // Affichage de la page d'abonnement Marketplace
    public function index()
    {
        $plans = PlanService::$marketplacePlans;
        foreach ($plans as $key => $plan) {
            $plans[$key]['fee'] = (int) round($plan['price'] * 0.03046);
        }
        $user = Auth::user();

        $activeSub = MarketplaceSubscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->first();

        return view('marketplace.index', compact('plans', 'user', 'activeSub'));
    }

    // Afficher le statut actuel de l'abonnement
    public function status()
    {
        $user = Auth::user();

// APRÈS
        $activeSubs = MarketplaceSubscription::where('user_id', $user->id)->where('status', 'active')->where('expires_at', '>', now())->get();

        $history = MarketplaceSubscription::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('marketplace.status', compact('activeSubs', 'history'));
    }

    // Lancer le paiement (Mobile Money)
    public function pay(Request $request)
    {
        $planKey = $request->plan;
        $entity = $request->input('entity', Auth::user()->name);

        if (!isset(PlanService::$marketplacePlans[$planKey])) {
            return back()->with('error', 'Plan Marketplace invalide.');
        }

        $amount = PlanService::$marketplacePlans[$planKey]['price'];

        $paymentFee = (int) round($amount * 0.03046);
        $totalAmount = $amount + $paymentFee;

        $reference = 'MKT-' . Auth::id() . '-' . $planKey . '-' . time();

        // APRÈS
        $apiUrl = config('services.dexpay.sandbox')
            ? config('services.dexpay.sandbox_url')
            : config('services.dexpay.api_url');

        Log::info('Marketplace - URL webhook envoyée à Dexpay', [
            'webhook_url' => route('marketplace.callback', $reference),
            'reference' => $reference
        ]);

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'x-api-key' => config('services.dexpay.api_key'),
            'Content-Type' => 'application/json',
        ])
            ->timeout(30)
            ->post($apiUrl . '/checkout-sessions', [
                'reference' => $reference,
                'item_name' => 'Abonnement ' . PlanService::$marketplacePlans[$planKey]['name'],
                'amount' => $totalAmount,
                'currency' => 'XOF',
                'success_url' => route('marketplace.pending', ['externalId' => $reference]),
                'failure_url' => route('marketplace.index'),
                'webhook_url' => route('marketplace.callback', $reference),
                'is_one_shot_payment' => true,
                'metadata' => [
                    'user_id' => Auth::id(),
                    'plan' => $planKey,
                    'entity_name' => $entity,
                    'amount_ht' => $amount,
                    'payment_fee' => $paymentFee,
                ],
            ]);

        $data = $response->json();

        if ($response->successful() && isset($data['data']['payment_url'])) {
            session([
                'pending_mkt_plan' => $planKey,
                'pending_mkt_amount' => $amount,
                'pending_mkt_total' => $totalAmount,
                'pending_mkt_external_id' => $reference,
                'pending_mkt_entity' => $entity,
            ]);

            Log::info('Marketplace - Session sauvegardée avant redirection', [
                'entity' => $entity,
                'plan' => $planKey,
                'reference' => $reference
            ]);

            $redirectUrl = $data['data']['sandbox_payment_url'] ?? $data['data']['payment_url'];
            return redirect()->away($redirectUrl);
        }

        Log::error('Marketplace - Échec paiement Dexpay', [
            'status' => $response->status(),
            'response_body' => $data,
            'plan' => $planKey,
            'entity' => $entity
        ]);

        return back()->with('error', $data['message'] ?? 'Paiement impossible.');
    }

    // Page d'attente
    public function pending($externalId)
    {
        return view('marketplace.pending', compact('externalId'));
    }

    // Vérification AJAX du statut
    public function checkStatus($externalId)
    {
        Log::info('Marketplace - Vérification statut', ['externalId' => $externalId]);

        // 🔥 VÉRIFIER SI LE WEBHOOK A DÉJÀ TRAITÉ
        $alreadyProcessed = MarketplaceSubscription::where('metadata->reference', $externalId)
            ->where('status', 'active')
            ->exists();

        if ($alreadyProcessed) {
            Log::info('Marketplace - Déjà traité, on redirige', ['externalId' => $externalId]);
            session()->forget(['pending_mkt_plan', 'pending_mkt_amount', 'pending_mkt_total', 'pending_mkt_external_id', 'pending_mkt_entity']);
            return response()->json(['success' => true, 'status' => 'SUCCESS', 'redirect' => route('marketplace.status')]);
        }

        $session = $this->dexpay->getCheckoutSession($externalId);

        if (!$session) {
            return response()->json(['success' => false, 'status' => 'PENDING']);
        }

        $status = $session['data']['status'] ?? $session['status'] ?? 'PENDING';

        Log::info('Marketplace - Statut reçu', ['status' => $status]);

        if ($status === 'completed') {
            $parts = explode('-', $externalId);
            $userId = $parts[1] ?? null;
            $planKey = $parts[2] ?? null;

            if ($userId && $planKey && isset(PlanService::$marketplacePlans[$planKey])) {
                $user = \App\Models\User::find($userId);

                $entityName = session('pending_mkt_entity', $user->name);
                $shop = \App\Models\Shop::where('user_id', $userId)->where('name', $entityName)->first();

                // APRÈS
                MarketplaceSubscription::where('user_id', $userId)
                    ->where('shop_id', $shop ? $shop->id : null)
                    ->update(['status' => 'cancelled']);

                MarketplaceSubscription::create([
                    'user_id' => $userId,
                    'shop_id' => $shop ? $shop->id : null,
                    'plan' => $planKey,
                    'status' => 'active',
                    'expires_at' => now()->addMonth(), // ✅ Abonnement MENSUEL
                    'metadata' => array_merge($session, ['reference' => $externalId]),
                ]);

                // Ajouter le log de réception
                CashoutLog::create([
                    'shop_id' => $shop->id ?? null,
                    'service_code' => $session['data']['operator'] ?? 'sandbox',
                    'phone'        => $user->phone ?? 'N/A',
                    'amount'       => session('pending_mkt_total') ?? PlanService::$marketplacePlans[$planKey]['price'],
                    'external_id'  => $externalId,
                    'status'       => 'success',
                    'response'     => json_encode($session),
                ]);

                Log::info('Marketplace - Abonnement activé via checkStatus', ['user_id' => $userId, 'plan' => $planKey]);

                $this->autoCashoutToAdmin(
                    PlanService::$marketplacePlans[$planKey]['price'],
                    $userId,
                    $externalId
                );
            }

            session()->forget([
                'pending_mkt_plan', 'pending_mkt_amount', 'pending_mkt_total',
                'pending_mkt_external_id', 'pending_mkt_entity'
            ]);

            return response()->json([
                'success' => true,
                'status' => 'SUCCESS',
                'redirect' => route('marketplace.status')
            ]);
        }

        if (in_array($status, ['failed', 'cancelled'])) {
            return response()->json([
                'success'  => false,
                'status'   => 'FAILED',
                'redirect' => route('marketplace.index')
            ]);
        }

        return response()->json(['success' => false, 'status' => 'PENDING']);
    }

    // Webhook Dexpay
    public function callback(Request $request, $externalId)
    {
        Log::info('WEBHOOK ARRIVÉ ! Marketplace', ['headers' => $request->headers->all(), 'externalId' => $externalId]);
        Log::info('Marketplace Webhook reçu', $request->all());

        // 🔥 VÉRIFIER SI CHECKSTATUS A DÉJÀ TRAITÉ
        $alreadyProcessed = MarketplaceSubscription::where('metadata->reference', $externalId)
            ->where('status', 'active')
            ->exists();

        if ($alreadyProcessed) {
            Log::info('Marketplace - Déjà traité par checkStatus, webhook ignoré', ['externalId' => $externalId]);
            return response()->json(['success' => true]);
        }


        $payload = $request->all();
        $event = $payload['event'] ?? ($payload['data']['status'] ?? null);

        if ($event === 'checkout.completed') {
            $parts = explode('-', $externalId);
            $userId = $parts[1] ?? null;
            $planKey = $parts[2] ?? null;

            if ($userId && $planKey && isset(PlanService::$marketplacePlans[$planKey])) {
                $user = \App\Models\User::find($userId);

                $entityName = session('pending_mkt_entity', $user->name);
                $shop = \App\Models\Shop::where('user_id', $user->id)->where('name', $entityName)->first();

                if ($user) {
                    // APRÈS
                    $existing = MarketplaceSubscription::where('user_id', $user->id)
                        ->where('shop_id', $shop ? $shop->id : null)
                        ->where('status', 'active')->first();

                    if ($existing) {
                        $existing->update(['status' => 'cancelled']);
                        Log::info('Marketplace - Ancien abonnement annulé pour upgrade (callback)', ['old_id' => $existing->id]);
                    }

                    MarketplaceSubscription::where('user_id', $user->id)
                        ->where('shop_id', $shop ? $shop->id : null)
                        ->update(['status' => 'cancelled']);

                    Log::info('Marketplace - Tentative création en base', [
                        'user_id' => $user->id,
                        'entityName' => $entityName,
                        'shop_found' => $shop ? $shop->id : 'AUCUNE BOUTIQUE TROUVÉE',
                        'plan' => $planKey,
                    ]);

                    $sub = MarketplaceSubscription::create([
                        'user_id' => $user->id,
                        'shop_id' => $shop ? $shop->id : null,
                        'plan' => $planKey,
                        'status' => 'active',
                        'expires_at' => now()->addMonth(), // ✅ Abonnement MENSUEL
                        'metadata' => array_merge($payload, ['reference' => $externalId]),
                    ]);

                    CashoutLog::create([
                        'shop_id' => $shop->id ?? null,
                        'service_code' => $payload['operator'] ?? 'sandbox',
                        'phone'        => $user->phone ?? 'N/A',
                        'amount'       => $payload['amount'] ?? PlanService::$marketplacePlans[$planKey]['price'],
                        'external_id'  => $externalId,
                        'status'       => 'success',
                        'response'     => json_encode($payload),
                    ]);

                    Log::info('Marketplace - Résultat création', [
                        'success' => $sub ? 'OUI' : 'NON',
                        'id_sub' => $sub ? $sub->id : 'NULL',
                    ]);

                    $this->autoCashoutToAdmin(
                        PlanService::$marketplacePlans[$planKey]['price'],
                        $userId,
                        $externalId
                    );

                    session()->forget(['pending_mkt_plan', 'pending_mkt_amount', 'pending_mkt_total', 'pending_mkt_external_id', 'pending_mkt_entity']);
                }
            }
        }

        return response()->json(['success' => true]);
    }

    // Fonction de cashout (réutilisée)
    private function autoCashoutToAdmin($amount, $userId, $externalReference)
    {
        try {
            $cashoutExternalId = 'MKT-CASHOUT-' . $externalReference;

            if (CashoutLog::where('external_id', $cashoutExternalId)->exists()) {
                Log::info('Cashout déjà existant, ignoré', ['external_id' => $cashoutExternalId]);
                return true;
            }

            $admin = \App\Models\User::where('role', 'admin')
                ->where('is_active', true)
                ->whereNotNull('phone')
                ->first();

            if (!$admin || !$admin->phone) {
                Log::error('Cashout auto impossible : aucun admin avec téléphone trouvé');
                return false;
            }

            $phone = $admin->phone;
            $cashoutAmount = (int) round($amount);

            if ($cashoutAmount < 250) {
                Log::warning('Cashout auto annulé : montant trop petit', ['amount' => $cashoutAmount]);
                return false;
            }

            Log::info('Auto cashout Marketplace vers admin', [
                'admin_id' => $admin->id,
                'amount'   => $cashoutAmount,
                'external_id' => $cashoutExternalId,
            ]);

            $result = $this->dexpay->createPayout([
                'phone'          => $phone,
                'amount'         => $cashoutAmount,
                'currency'       => 'XOF',
                'operator'       => 'wave_sn_payout',
                'countryISO'     => 'SN',
                'recipient_name' => $admin->name ?? 'Admin',
                'order_number'   => $externalReference,
                'shop_id'        => null,
            ]);

            CashoutLog::create([
                'admin_id'    => $admin->id,
                'service_code' => 'wave_sn_payout',
                'phone'       => $phone,
                'amount'      => $cashoutAmount,
                'external_id' => $cashoutExternalId,
                'status'      => $result['success'] ? 'success' : 'failed',
                'response'    => json_encode($result),
            ]);

            Log::info('Auto cashout Marketplace résultat', [
                'success' => $result['success'],
                'reference' => $result['reference'] ?? 'N/A',
            ]);

            return $result['success'];

        } catch (\Exception $e) {
            Log::error('Auto cashout Marketplace échoué', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
