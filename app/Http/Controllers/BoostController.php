<?php
// app/Http/Controllers/BoostController.php

namespace App\Http\Controllers;

use App\Models\FacebookCampaign;
use App\Models\Product;
use App\Models\Shop;
use App\Services\FacebookAdsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BoostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Page de création de boost
     */
    public function create(Shop $shop, Product $product)
    {
        $this->authorize('update', $shop);

        if (!$product->facebook_post_id) {
            return redirect()->back()
                ->with('error', 'Ce produit n\'a pas encore été publié sur Facebook.');
        }

        return view('merchant.boost.create', compact('shop', 'product'));
    }

    /**
     * Lancer une campagne de boost
     */
    public function store(Request $request, Shop $shop, Product $product)
    {
        $this->authorize('update', $shop);

        $validated = $request->validate([
            'daily_budget' => 'required|numeric|min:1|max:1000',
            'duration_days' => 'required|integer|min:1|max:30',
            'audience_type' => 'required|in:local,followers,custom',
            'age_min' => 'nullable|integer|min:18|max:65',
            'age_max' => 'nullable|integer|min:18|max:65',
            'interests' => 'nullable|string',
            'city' => 'nullable|string',
            'radius' => 'nullable|integer|min:5|max:100',
            'starts_at' => 'nullable|date|after:now',
        ]);

        // Construire le ciblage
        $targeting = ['age_min' => $validated['age_min'] ?? 18, 'age_max' => $validated['age_max'] ?? 65];

        if ($validated['audience_type'] === 'local' && $request->city) {
            $targeting['geo_locations'] = [
                'cities' => [['key' => $request->city]],
                'radius' => $validated['radius'] ?? 20,
                'distance_unit' => 'kilometer',
            ];
        }

        if ($request->interests) {
            $targeting['interests'] = array_map('trim', explode(',', $request->interests));
        }

        // Calculer le budget total
        $totalBudget = $validated['daily_budget'] * $validated['duration_days'];

        // Créer la campagne en base
        $campaign = FacebookCampaign::create([
            'shop_id' => $shop->id,
            'product_id' => $product->id,
            'name' => "Boost - {$product->name} - " . now()->format('d/m/Y H:i'),
            'daily_budget' => $validated['daily_budget'],
            'total_budget' => $totalBudget,
            'duration_days' => (int) $validated['duration_days'],
            'targeting' => $targeting,
            'audience_type' => $validated['audience_type'],
            'status' => 'pending',
            'starts_at' => $request->starts_at ? Carbon::parse($request->starts_at) : now(),
            'ends_at' => ($request->starts_at ? Carbon::parse($request->starts_at) : now())->addDays((int) $validated['duration_days']),
        ]);

        // Lancer sur Facebook
        try {
            $adsService = new FacebookAdsService(
                $shop->facebook_access_token,
                'act_' . $shop->facebook_ad_account_id, // À ajouter dans Shop
                $shop->facebook_page_id
            );

            $adsService->createBoostCampaign($campaign);

            return redirect()->route('merchant.boost.index', $shop)
                ->with('success', '🚀 Campagne lancée avec succès !');
        } catch (\Exception $e) {
            $campaign->update(['status' => 'rejected']);
            Log::error('Boost error: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Liste des campagnes
     */
    public function index(Shop $shop)
    {
        $this->authorize('view', $shop);

        $campaigns = FacebookCampaign::where('shop_id', $shop->id)
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('merchant.boost.index', compact('shop', 'campaigns'));
    }

    /**
     * Mettre en pause
     */
    public function pause(Shop $shop, FacebookCampaign $campaign)
    {
        $this->authorize('update', $shop);

        try {
            $adsService = new FacebookAdsService(
                $shop->facebook_access_token,
                'act_' . $shop->facebook_ad_account_id,
                $shop->facebook_page_id
            );

            $adsService->pauseCampaign($campaign);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Réactiver
     */
    public function resume(Shop $shop, FacebookCampaign $campaign)
    {
        $this->authorize('update', $shop);

        try {
            $adsService = new FacebookAdsService(
                $shop->facebook_access_token,
                'act_' . $shop->facebook_ad_account_id,
                $shop->facebook_page_id
            );

            $adsService->resumeCampaign($campaign);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Synchroniser les stats
     */
    public function syncStats(Shop $shop, FacebookCampaign $campaign)
    {
        $this->authorize('view', $shop);

        try {
            $adsService = new FacebookAdsService(
                $shop->facebook_access_token,
                'act_' . $shop->facebook_ad_account_id,
                $shop->facebook_page_id
            );

            $adsService->getCampaignStats($campaign);

            return response()->json(['success' => true, 'campaign' => $campaign->fresh()]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
// Page de création
    public function createRetargetingForm(Shop $shop)
    {
        $this->authorize('update', $shop);

        if (!$shop->hasFacebookConnected()) {
            return redirect()->back()->with('error', 'Connectez votre page Facebook d\'abord.');
        }

        return view('merchant.boost.retargeting-create', compact('shop'));
    }

// Traitement (modifié pour accepter les données du formulaire)
    public function createRetargeting(Request $request, Shop $shop)
    {
        $this->authorize('update', $shop);

        $validated = $request->validate([
            'daily_budget' => 'required|numeric|min:1|max:1000',
            'duration_days' => 'required|integer|min:1|max:30',
        ]);

        $campaign = FacebookCampaign::create([
            'shop_id' => $shop->id,
            'product_id' => $shop->products()->first()->id ?? 0,
            'name' => 'Retargeting - ' . $shop->name . ' - ' . now()->format('d/m/Y H:i'),
            'campaign_type' => 'retargeting',
            'daily_budget' => $validated['daily_budget'],
            'total_budget' => $validated['daily_budget'] * $validated['duration_days'],
            'duration_days' => (int) $validated['duration_days'],
            'audience_type' => 'retargeting',
            'status' => 'pending',
            'ends_at' => now()->addDays((int) $validated['duration_days']),
        ]);

        try {
            $adsService = new \App\Services\FacebookAdsService(
                $shop->facebook_access_token,
                'act_' . $shop->facebook_ad_account_id,
                $shop->facebook_page_id
            );

            $adsService->createRetargetingCampaign($campaign);

            return redirect()->route('merchant.boost.index', $shop)
                ->with('success', '🎯 Campagne de retargeting lancée !');
        } catch (\Exception $e) {
            $campaign->update(['status' => 'rejected']);
            \Log::error('Retargeting error: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function launch(Shop $shop, FacebookCampaign $campaign)
    {
        $this->authorize('update', $shop);

        if ($campaign->status !== 'draft') {
            return redirect()->back()->with('error', 'Cette campagne est déjà lancée.');
        }

        try {
            $adsService = new \App\Services\FacebookAdsService(
                $shop->facebook_access_token,
                'act_' . $shop->facebook_ad_account_id,
                $shop->facebook_page_id
            );

            if ($campaign->campaign_type === 'retargeting') {
                $adsService->createRetargetingCampaign($campaign);
            } else {
                $adsService->createBoostCampaign($campaign);
            }

            return redirect()->back()->with('success', '🚀 Campagne lancée !');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function duplicate(Shop $shop, FacebookCampaign $campaign)
    {
        $new = $campaign->replicate();
        $new->name = $campaign->name . ' (copie)';
        $new->status = 'draft';
        $new->fb_campaign_id = null;
        $new->fb_adset_id = null;
        $new->fb_ad_id = null;
        $new->reach = 0;
        $new->impressions = 0;
        $new->clicks = 0;
        $new->spent = 0;
        $new->save();

        return redirect()->back()->with('success', 'Campagne dupliquée !');
    }

    public function edit(Shop $shop, FacebookCampaign $campaign)
    {
        $this->authorize('update', $shop);
        $products = $shop->products()->whereNotNull('facebook_post_id')->get();

        return view('merchant.boost.edit', compact('shop', 'campaign', 'products'));
    }

    public function update(Request $request, Shop $shop, FacebookCampaign $campaign)
    {
        $this->authorize('update', $shop);

        $validated = $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'daily_budget' => 'required|numeric|min:1|max:1000',
            'duration_days' => 'required|integer|min:1|max:30',
        ]);

        $campaign->update([
            'product_id' => $validated['product_id'] ?? $campaign->product_id,
            'daily_budget' => $validated['daily_budget'],
            'duration_days' => (int) $validated['duration_days'],
            'total_budget' => $validated['daily_budget'] * $validated['duration_days'],
            'ends_at' => now()->addDays((int) $validated['duration_days']),
        ]);

        return redirect()->route('merchant.boost.index', $shop)
            ->with('success', 'Campagne mise à jour !');
    }


    public function promoteSaas(Shop $shop)
    {
        $this->authorize('update', $shop);

        if (!$shop->hasFacebookConnected()) {
            return redirect()->back()->with('error', 'Connectez votre page Facebook d\'abord.');
        }

        return view('merchant.boost.promote', compact('shop'));
    }

    public function storePromoteSaas(Request $request, Shop $shop)
    {
        $this->authorize('update', $shop);

        $validated = $request->validate([
            'message' => 'required|string|max:500',
            'landing_url' => 'required|url',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'daily_budget' => 'required|numeric|min:1|max:1000',
            'duration_days' => 'required|integer|min:1|max:30',
        ]);

        // 1. Publier le post sur Facebook
        $fbService = new \App\Services\FacebookService(
            $shop->facebook_access_token,
            $shop->facebook_page_id
        );

        $message = $validated['message'] . "\n\n👉 " . $validated['landing_url'];

        // Sauvegarder l'image si uploadée
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('promotions', 'public');
        }

        if ($imagePath) {
            $product = new \App\Models\Product([
                'name' => 'Promotion SaaS',
                'image' => $imagePath,
                'price' => 0,
            ]);
            $postResult = $fbService->publishWithPhotoFile(
                $message,
                $product,
                $validated['landing_url']
            );
        } else {
            $postResult = $fbService->publishWithLink(
                $message,
                $validated['landing_url']
            );
        }

        $facebookPostId = $postResult['post_id'] ?? $postResult['id'] ?? null;

        // 2. Créer la campagne de boost
        $campaign = FacebookCampaign::create([
            'shop_id' => $shop->id,
            'product_id' => $shop->products()->first()->id ?? 0,
            'landing_url' => $validated['landing_url'],
            'post_message' => $validated['message'],
            'name' => 'Promo SaaS - ' . now()->format('d/m/Y H:i'),
            'campaign_type' => 'boost',
            'daily_budget' => $validated['daily_budget'],
            'total_budget' => $validated['daily_budget'] * $validated['duration_days'],
            'duration_days' => (int) $validated['duration_days'],
            'audience_type' => 'custom',
            'status' => 'pending',
            'ends_at' => now()->addDays((int) $validated['duration_days']),
        ]);

        // Sauvegarder le facebook_post_id temporairement
        if ($facebookPostId) {
            \DB::table('products')->where('id', $campaign->product_id)
                ->update(['facebook_post_id' => $facebookPostId]);
        }

        try {
            $adsService = new \App\Services\FacebookAdsService(
                $shop->facebook_access_token,
                'act_' . $shop->facebook_ad_account_id,
                $shop->facebook_page_id
            );

            $adsService->createBoostCampaign($campaign);

            return redirect()->route('merchant.boost.index', $shop)
                ->with('success', '🚀 Campagne SaaS lancée !');
        } catch (\Exception $e) {
            $campaign->update(['status' => 'rejected']);
            \Log::error('Promote SaaS error: ' . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
