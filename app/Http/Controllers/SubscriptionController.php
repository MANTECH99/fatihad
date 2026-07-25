<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\CashoutLog;
use App\Services\DexpayService;
use App\Services\PlanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    protected DexpayService $dexpay;

    public function __construct(DexpayService $dexpay)
    {
        $this->dexpay = $dexpay;
        $this->middleware('auth')->except(['paymentCallback', 'checkStatus']);
    }

    public function index()
    {
        $plans = PlanService::$plans;
        $currentPlan = auth()->user()->plan ?? 'free';
        $subscription = Subscription::where('user_id', auth()->id())->latest()->first();

        return view('subscription.index', compact('plans', 'currentPlan', 'subscription'));
    }

    public function subscribe(Request $request)
    {
        $plan = $request->plan;

        if (!isset(PlanService::$plans[$plan])) {
            return back()->with('error', 'Plan invalide.');
        }

        if ($plan === 'free') {
            $user = auth()->user();

            if (!$user->trial_ends_at || $user->trial_ends_at->isPast()) {
                return back()->with('error', 'Vous avez déjà utilisé votre essai gratuit. Choisissez un plan payant.');
            }

            return $this->activatePlan(auth()->user(), $plan);
        }

        return redirect()->route('subscription.payment', $plan);
    }

    public function showPayment($plan)
    {
        if (!isset(PlanService::$plans[$plan]) || $plan === 'free') {
            return back()->with('error', 'Plan invalide.');
        }

        $amount = PlanService::$plans[$plan]['price'];

        return view('subscription.payment', compact('plan', 'amount'));
    }

    public function pay(Request $request)
    {
        $plan = $request->plan;
        $paymentMethod = $request->input('method', 'wave');

        if (!isset(PlanService::$plans[$plan]) || $plan === 'free') {
            return back()->with('error', 'Plan invalide.');
        }

        $amount = PlanService::$plans[$plan]['price'];

        // Frais de paiement mobile
        $paymentFee = (int) round($amount * 0.0303);
        $totalAmount = $amount + $paymentFee;

        $shop = auth()->user()->shops->first();
        $phone = $shop->whatsapp_phone ?? auth()->user()->phone;

        $reference = 'SUB-' . auth()->id() . '-' . $plan . '-' . time();

        // Déterminer la bonne URL
        $apiUrl = config('services.dexpay.sandbox')
            ? config('services.dexpay.sandbox_url')
            : config('services.dexpay.api_url');

        // Créer une checkout session Dexpay
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'x-api-key'    => config('services.dexpay.api_key'),
            'Content-Type' => 'application/json',
        ])
            ->timeout(30)
            ->post($apiUrl . '/checkout-sessions', [  // ← $apiUrl au lieu de config('services.dexpay.api_url')
                'reference'         => $reference,
                'item_name'         => PlanService::$plans[$plan]['name'],
                'amount'            => $totalAmount,
                'currency'          => 'XOF',
                'success_url'       => route('subscription.pending', ['externalId' => $reference]),
                'failure_url'       => route('subscription.payment', $plan),
                'webhook_url'       => route('subscription.callback', $reference),
                'is_one_shot_payment' => true,
                'metadata'          => [
                    'user_id'    => auth()->id(),
                    'plan'       => $plan,
                    'amount_ht'  => $amount,
                    'payment_fee' => $paymentFee,
                    'method'     => $paymentMethod,
                ],
            ]);

        $data = $response->json();

        Log::info('Dexpay Abonnement - Response', ['data' => $data]);

        if ($response->successful() && isset($data['data']['payment_url'])) {
            session([
                'pending_plan'           => $plan,
                'pending_amount'         => $amount,
                'pending_total_paid'     => $totalAmount,
                'pending_payment_fee'     => $paymentFee,
                'pending_phone'          => $phone,
                'pending_external_id'    => $reference,
                'pending_method'         => $paymentMethod,
            ]);

            $redirectUrl = $data['data']['sandbox_payment_url'] ?? $data['data']['payment_url'];

            if ($redirectUrl) {
                return redirect()->away($redirectUrl);
            }

            return redirect()->route('subscription.pending', ['externalId' => $reference]);
        }

        $errorMsg = $data['message'] ?? 'Paiement impossible. Réessayez.';
        return back()->with('error', $errorMsg);
    }

    /**
     * Webhook Dexpay pour les abonnements
     */
    public function paymentCallback(Request $request, $externalId)
    {
        Log::info('Subscription webhook reçu', $request->all());

        $payload = $request->all();
        $event = $payload['event'] ?? ($payload['data']['status'] ?? null);
        $data  = $payload['data'] ?? $payload;

        if ($event === 'checkout.completed' || ($payload['status'] ?? '') === 'completed') {
            $parts = explode('-', $externalId);
            $userId = $parts[1] ?? null;
            $plan = $parts[2] ?? null;

            if ($userId && $plan && isset(PlanService::$plans[$plan])) {
                $user = \App\Models\User::find($userId);
                if ($user) {
                    // Activer le plan seulement si pas déjà actif
                    if ($user->plan !== $plan) {
                        $this->activatePlan($user, $plan);
                        session()->flash('success', 'Votre abonnement est activé !'); // ← ajoute ceci
                        Log::info('Plan activé via webhook', ['user' => $userId, 'plan' => $plan]);
                    }

                    // Cashout automatique vers l'admin (anti-doublon)
                    $cashoutExternalId = 'SUB-CASHOUT-' . $externalId;

                    if (!CashoutLog::where('external_id', $cashoutExternalId)->exists()) {
                        $this->autoCashoutToAdmin(
                            PlanService::$plans[$plan]['price'],
                            $userId,
                            $externalId
                        );
                    } else {
                        Log::info('Cashout déjà effectué, ignoré', ['external_id' => $cashoutExternalId]);
                    }
                }
            }
        }

        return response()->json(['success' => true]);
    }

    public function cancel()
    {
        Subscription::where('user_id', auth()->id())
            ->where('status', 'active')
            ->update(['status' => 'cancelled']);

        auth()->user()->update([
            'plan' => 'free',
            'trial_ends_at' => null,
        ]);

        return back()->with('success', 'Abonnement annulé.');
    }

    private function activatePlan($user, $plan)
    {
        Subscription::where('user_id', $user->id)->update(['status' => 'cancelled']);

        if ($plan === 'free') {
            $trialEndsAt = now()->addDays(14);
            $endsAt = null;
        } else {
            $trialEndsAt = null;
            $endsAt = now()->addMonth();
        }

        Subscription::create([
            'user_id' => $user->id,
            'plan' => $plan,
            'status' => 'active',
            'trial_ends_at' => $trialEndsAt,
            'ends_at' => $endsAt,
        ]);

        $user->update([
            'plan' => $plan,
            'trial_ends_at' => $trialEndsAt,
        ]);

        return redirect()->route('subscription.index')
            ->with('success', 'Votre abonnement ' . PlanService::$plans[$plan]['name'] . ' est activé !');
    }

    /**
     * Cashout automatique vers l'admin après un paiement d'abonnement
     */
    private function autoCashoutToAdmin($amount, $userId, $externalReference)
    {
        try {
            $cashoutExternalId = 'SUB-CASHOUT-' . $externalReference;

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

            $phone = $admin->phone; // déjà formaté ou sera nettoyé par DexpayService
            $cashoutAmount = (int) round($amount);

            if ($cashoutAmount < 250) {
                Log::warning('Cashout auto annulé : montant trop petit', ['amount' => $cashoutAmount]);
                return false;
            }

            Log::info('Auto cashout abonnement vers admin', [
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

            Log::info('Auto cashout abonnement résultat', [
                'success' => $result['success'],
                'reference' => $result['reference'] ?? 'N/A',
            ]);

            return $result['success'];

        } catch (\Exception $e) {
            Log::error('Auto cashout abonnement échoué', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function pending($externalId)
    {
        $pendingPlan = session('pending_plan') ?? request('plan');
        $pendingAmount = session('pending_amount') ?? request('amount');
        $pendingTotalPaid = session('pending_total_paid') ?? request('total');
        $pendingMethod = session('pending_method') ?? request('method');

        if (!$pendingPlan) {
            $parts = explode('-', $externalId);
            $pendingPlan = $parts[2] ?? 'starter';
            $pendingAmount = PlanService::$plans[$pendingPlan]['price'] ?? 0;
            $pendingTotalPaid = $pendingAmount;
            $pendingMethod = 'wave';
        }

        return view('subscription.pending', compact(
            'pendingPlan', 'pendingAmount', 'pendingTotalPaid', 'externalId', 'pendingMethod'
        ));
    }

    public function checkStatus($externalId)
    {
        $session = $this->dexpay->getCheckoutSession($externalId);

        Log::info('DEBUG checkStatus RAW', ['session' => $session]);

        if (!$session) {
            return response()->json(['success' => false, 'status' => 'PENDING']);
        }

        $status = $session['data']['status'] ?? $session['status'] ?? 'PENDING';
        //                                         ↑ ajoute ce fallback

        Log::info('Check status abonnement', ['externalId' => $externalId, 'status' => $status]);

        if ($status === 'completed') {
            $parts = explode('-', $externalId);
            $plan = $parts[2] ?? 'starter';
            $userId = $parts[1] ?? auth()->id();
            $amount = PlanService::$plans[$plan]['price'] ?? 0;

            $user = \App\Models\User::find($userId);
            if ($user && $user->plan !== $plan) {
                $this->activatePlan($user, $plan);
            }

            $cashoutExternalId = 'SUB-CASHOUT-' . $externalId;
            if (!CashoutLog::where('external_id', $cashoutExternalId)->exists()) {
                $this->autoCashoutToAdmin($amount, $userId, $externalId);
            }

            session()->forget([
                'pending_plan', 'pending_transaction_id', 'pending_amount',
                'pending_total_paid', 'pending_payment_fee', 'pending_phone',
                'pending_service', 'pending_external_id', 'pending_method'
            ]);

            session()->flash('success', 'Félicitations ! Votre abonnement ' . PlanService::$plans[$plan]['name'] . ' est activé avec succès.');

            return response()->json([
                'success'  => true,
                'status'   => 'SUCCESS',
                'redirect' => route('subscription.index')
            ]);
        }

        if (in_array($status, ['failed', 'cancelled'])) {
            return response()->json([
                'success'  => false,
                'status'   => 'FAILED',
                'redirect' => route('subscription.index')
            ]);
        }

        return response()->json(['success' => false, 'status' => 'PENDING']);
    }
}
