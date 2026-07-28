<?php

namespace App\Http\Controllers;

use App\Models\Certification;
use App\Models\CashoutLog;
use App\Services\DexpayService;
use App\Services\PlanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CertificationController extends Controller
{
    protected DexpayService $dexpay;

    public function __construct(DexpayService $dexpay)
    {
        $this->dexpay = $dexpay;
    }

    // Affichage de la page de certification (comme votre capture d'écran)
    public function index()
    {
        $plans = PlanService::$certifications;
        // Ajouter les frais pour chaque plan
        foreach ($plans as $key => $plan) {
            $plans[$key]['fee'] = (int) round($plan['price'] * 0.0303);
        }
        $user = Auth::user();

        // Vérifier si l'utilisateur a déjà une certification active
        $activeCert = Certification::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        return view('certification.index', compact('plans', 'user', 'activeCert'));
    }

    // Afficher le statut actuel de la certification
    public function status()
    {
        $user = Auth::user();

        $activeCerts = Certification::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->get();

        // Récupérer l'historique (toutes les certifications passées)
        $history = Certification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('certification.status', compact('activeCerts', 'history'));
    }

    // Lancer le paiement (Mobile Money)
    public function pay(Request $request)
    {
        $planKey = $request->plan;
        $entity = $request->input('entity', Auth::user()->name);

        if (!isset(PlanService::$certifications[$planKey])) {
            return back()->with('error', 'Plan de certification invalide.');
        }

        $amount = PlanService::$certifications[$planKey]['price'];

        // Frais Dexpay (~3%)
        $paymentFee = (int) round($amount * 0.03046);
        $totalAmount = $amount + $paymentFee;

        $reference = 'CERT-' . Auth::id() . '-' . $planKey . '-' . time();

        $shop = \App\Models\Shop::where('user_id', Auth::id())->where('name', $entity)->first();

        // APRÈS
        $apiUrl = config('services.dexpay.sandbox')
            ? config('services.dexpay.sandbox_url')
            : config('services.dexpay.api_url');

        Log::info('Certification - URL webhook envoyée à Dexpay', [
            'webhook_url' => route('certification.callback', $reference),
            'reference' => $reference
        ]);

        // Appel à l'API Dexpay (comme pour vos abonnements)
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'x-api-key' => config('services.dexpay.api_key'),
            'Content-Type' => 'application/json',
        ])
            ->timeout(30)
            ->post($apiUrl . '/checkout-sessions', [
                'reference' => $reference,
                'item_name' => 'Certification ' . PlanService::$certifications[$planKey]['name'],
                'amount' => $totalAmount,
                'currency' => 'XOF',
                'success_url' => route('certification.pending', ['externalId' => $reference]),
                'failure_url' => route('certification.index'),
                'webhook_url' => route('certification.callback', $reference),
                'is_one_shot_payment' => true,
                'metadata' => [
                    'user_id' => Auth::id(),
                    'plan' => $planKey,
                    'entity_name' => $entity,
                    'amount_ht' => $amount,
                    'payment_fee' => $paymentFee,
                    'shop_id' => $shop->id ?? null, // ← ICI aussi
                ],
            ]);

        $data = $response->json();

        if ($response->successful() && isset($data['data']['payment_url'])) {


            session([
                'pending_cert_plan' => $planKey,
                'pending_cert_amount' => $amount,
                'pending_cert_total' => $totalAmount,
                'pending_cert_external_id' => $reference,
                'pending_cert_entity' => $entity,
                'pending_cert_shop_id' => $shop->id ?? null,
            ]);

            session()->save();

            // 🔥 NOUVEAU LOG ICI
            Log::info('Certification - Session sauvegardée avant redirection', [
                'entity' => $entity,
                'plan' => $planKey,
                'reference' => $reference,
                        'shop_id' => session('pending_cert_shop_id'), // ← AJOUTER
            ]);

            $redirectUrl = $data['data']['sandbox_payment_url'] ?? $data['data']['payment_url'];
            return redirect()->away($redirectUrl);
        }

        // ✅ C'EST ICI QU'ON LOGUE L'ERREUR POUR LA VOIR
        Log::error('Certification - Échec paiement Dexpay', [
            'status' => $response->status(),
            'response_body' => $data,
            'plan' => $planKey,
            'entity' => $entity
        ]);

        return back()->with('error', $data['message'] ?? 'Paiement impossible.');
    }

    // Page d'attente (en attendant le webhook)
    public function pending($externalId)
    {
        return view('certification.pending', compact('externalId'));
    }

    // Vérification AJAX du statut (polling)
    // Vérification AJAX du statut (polling)
    // Vérification AJAX du statut (polling)
    public function checkStatus($externalId)
    {
        Log::info('Certification - Vérification statut', ['externalId' => $externalId]);

        // 🔥 VÉRIFIER SI LE WEBHOOK A DÉJÀ TRAITÉ CETTE TRANSACTION
        $alreadyProcessed = Certification::where('metadata->reference', $externalId)
            ->orWhere('metadata->data->reference', $externalId)
            ->where('status', 'active')
            ->exists();

        if ($alreadyProcessed) {
            Log::info('Certification - Déjà traitée par webhook, on redirige', ['externalId' => $externalId]);

            // Nettoyer la session
            session()->forget([
                'pending_cert_plan', 'pending_cert_amount', 'pending_cert_total',
                'pending_cert_external_id', 'pending_cert_entity', 'pending_cert_shop_id'
            ]);

            return response()->json([
                'success' => true,
                'status' => 'SUCCESS',
                'redirect' => route('certification.status')
            ]);
        }

        $session = $this->dexpay->getCheckoutSession($externalId);

        if (!$session) {
            return response()->json(['success' => false, 'status' => 'PENDING']);
        }

        $status = $session['data']['status'] ?? $session['status'] ?? 'PENDING';

        Log::info('Certification - Statut reçu', ['status' => $status]);

        if ($status === 'completed') {
            $parts = explode('-', $externalId);
            $userId = $parts[1] ?? null;
            $planKey = $parts[2] ?? null;

            if ($userId && $planKey && isset(PlanService::$certifications[$planKey])) {
                $user = \App\Models\User::find($userId);

                $shopId = session('pending_cert_shop_id');
                $shop = $shopId ? \App\Models\Shop::find($shopId) : null;

// Si pas de boutique, on arrête
                if (!$shop) {
                    Log::error('Certification - Aucune boutique sélectionnée');
                    return response()->json(['success' => false, 'status' => 'ERROR']);
                }

                $entityName = $shop->name;

                // Vérifier si une certification existe déjà pour ce shop
                $existing = Certification::where('user_id', $userId)
                    ->where('shop_id', $shop->id)
                    ->where('status', 'active')
                    ->first();

                if ($existing) {
                    // Annuler l'ancienne certification pour la remplacer
                    $existing->update(['status' => 'cancelled']);
                    Log::info('Certification - Ancienne certification annulée pour upgrade', ['old_id' => $existing->id, 'new_plan' => $planKey]);
                }


// Désactiver les anciennes certifications (qu'il y en ait ou pas)
                Certification::where('user_id', $userId)
                    ->where('shop_id', $shop ? $shop->id : null)
                    ->update(['status' => 'cancelled']);

// Créer la nouvelle certification
                Certification::create([
                    'user_id' => $userId,
                    'shop_id' => $shop ? $shop->id : null,
                    'plan' => $planKey,
                    'entity_name' => $entityName,
                    'status' => 'active',
                    'expires_at' => now()->addYear(),
                    'metadata' => array_merge($session, ['reference' => $externalId]), // ← AJOUTER LA RÉFÉRENCE ICI
                ]);
                // Ajouter le log de réception
                CashoutLog::create([
                    'shop_id'      => $shop->id,
                    'service_code' => $session['data']['operator'] ?? 'sandbox',
                    'phone'        => $user->phone ?? 'N/A',
                    'amount' => session('pending_cert_total') ?? PlanService::$certifications[$planKey]['price'],
                    'external_id'  => $externalId,
                    'status'       => 'success',
                    'response'     => json_encode($session),
                ]);

                Log::info('Certification activée via checkStatus', ['user_id' => $userId, 'plan' => $planKey]);

// Cashout admin
                $this->autoCashoutToAdmin(
                    PlanService::$certifications[$planKey]['price'],
                    $userId,
                    $externalId
                );
            }

            // Vider la session et rediriger
            session()->forget([
                'pending_cert_plan', 'pending_cert_amount', 'pending_cert_total',
                'pending_cert_external_id', 'pending_cert_entity'
            ]);

            return response()->json([
                'success' => true,
                'status' => 'SUCCESS',
                'redirect' => route('certification.status')
            ]);
        }

        if (in_array($status, ['failed', 'cancelled'])) {
            return response()->json([
                'success'  => false,
                'status'   => 'FAILED',
                'redirect' => route('certification.index')
            ]);
        }

        return response()->json(['success' => false, 'status' => 'PENDING']);
    }

    // Webhook Dexpay (pour activer la certification automatiquement)
    public function callback(Request $request, $externalId)
    {

        Log::info('WEBHOOK ARRIVÉ !', ['headers' => $request->headers->all(), 'externalId' => $externalId]);
        Log::info('Certification Webhook reçu', $request->all());

        // 🔥 AJOUTER CECI
        $alreadyProcessed = Certification::where('metadata->reference', $externalId)
            ->where('status', 'active')
            ->exists();

        if ($alreadyProcessed) {
            Log::info('Certification - Déjà traitée par checkStatus, webhook ignoré', ['externalId' => $externalId]);
            return response()->json(['success' => true]);
        }

        $payload = $request->all();
        $event = $payload['event'] ?? ($payload['data']['status'] ?? null);

        if ($event === 'checkout.completed') {
            $parts = explode('-', $externalId);
            $userId = $parts[1] ?? null;
            $planKey = $parts[2] ?? null;

            if ($userId && $planKey && isset(PlanService::$certifications[$planKey])) {
                $user = \App\Models\User::find($userId);

                // Récupérer shop_id depuis les métadonnées du webhook
                $shopId = $payload['metadata']['shop_id'] ?? $payload['data']['metadata']['shop_id'] ?? session('pending_cert_shop_id');
                $shop = $shopId ? \App\Models\Shop::find($shopId) : null;

// Si pas de boutique, on arrête
                if (!$shop) {
                    Log::error('Certification - Aucune boutique sélectionnée');
                    return response()->json(['success' => false, 'status' => 'ERROR']);
                }

                $entityName = $shop->name;

                if ($user) {
                    // Vérifier si une certification existe déjà pour cet utilisateur
                    $existing = Certification::where('user_id', $user->id)
                        ->where('shop_id', $shop ? $shop->id : null)
                        ->where('status', 'active')
                        ->first();

                    if ($existing) {
                        // Annuler l'ancienne certification pour la remplacer
                        $existing->update(['status' => 'cancelled']);
                        Log::info('Certification - Ancienne certification annulée pour upgrade', ['old_id' => $existing->id, 'new_plan' => $planKey]);
                    }

                    // Si pas d'existante, on crée et on désactive les anciennes
// APRÈS
                    Certification::where('user_id', $user->id)
                        ->where('shop_id', $shop ? $shop->id : null)
                        ->update(['status' => 'cancelled']);

                    // 🔥 LOG 1 : Vérifier ce qu'on a récupéré
                    Log::info('Certification - Tentative création en base', [
                        'user_id' => $user->id,
                        'entityName' => $entityName,
                        'shop_found' => $shop ? $shop->id : 'AUCUNE BOUTIQUE TROUVÉE',
                        'plan' => $planKey,
                    ]);

                    // Créer la certification active
                    $cert = Certification::create([
                        'user_id' => $user->id,
                        'shop_id' => $shop ? $shop->id : null,
                        'plan' => $planKey,
                        'entity_name' => $entityName,
                        'status' => 'active',
                        'expires_at' => now()->addYear(),
                        'metadata' => array_merge($payload, ['reference' => $externalId]), // ← AJOUTER LA RÉFÉRENCE ICI
                    ]);

                    // Ajouter le log de réception
                    CashoutLog::create([
                        'shop_id'      => $shop->id,
                        'service_code' => $payload['operator'] ?? 'sandbox',
                        'phone'        => $user->phone ?? 'N/A',
                        'amount' => $payload['amount'] ?? PlanService::$certifications[$planKey]['price'],
                        'external_id'  => $externalId,
                        'status'       => 'success',
                        'response'     => json_encode($payload),
                    ]);

                    // 🔥 LOG 2 : Vérifier si la création a réussi
                    Log::info('Certification - Résultat création', [
                        'success' => $cert ? 'OUI' : 'NON',
                        'id_cert' => $cert ? $cert->id : 'NULL',
                    ]);

                    // Cashout automatique vers l'admin (comme vous l'avez déjà)
                    $this->autoCashoutToAdmin(
                        PlanService::$certifications[$planKey]['price'],
                        $userId,
                        $externalId
                    );

                    // 🔥 Vider la session pour éviter les conflits plus tard
                    session()->forget(['pending_cert_plan', 'pending_cert_amount', 'pending_cert_total', 'pending_cert_external_id', 'pending_cert_entity']);
                }
            }
        }

        return response()->json(['success' => true]);
    }

    // Fonction de cashout (réutilisée depuis votre SubscriptionController)
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
}
