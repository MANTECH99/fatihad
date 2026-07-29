<?php
// app/Services/FacebookAdsService.php

namespace App\Services;

use App\Models\FacebookCampaign;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacebookAdsService
{
    protected $accessToken;
    protected $adAccountId;
    protected $pageId;

    public function __construct($accessToken, $adAccountId, $pageId)
    {
        $this->accessToken = $accessToken;
        $this->adAccountId = $adAccountId;
        $this->pageId = $pageId;
    }



    protected function getObjective(FacebookCampaign $campaign): string
    {
        return match ($campaign->campaign_type) {
            'traffic'     => 'OUTCOME_TRAFFIC',
            'awareness'   => 'OUTCOME_AWARENESS',
            'engagement'  => 'OUTCOME_ENGAGEMENT',
            'retargeting' => 'OUTCOME_SALES',
            default       => 'OUTCOME_ENGAGEMENT',
        };
    }

    /**
     * Créer une campagne de boost pour un produit
     */


    public function createBoostCampaign(FacebookCampaign $campaign)
    {
        $product = $campaign->product;
        $shop = $campaign->shop;

        // 1. Créer la campagne
        $fbCampaign = $this->createCampaign($campaign);

        if (!isset($fbCampaign['id'])) {
            throw new \Exception('Échec création campagne: ' . json_encode($fbCampaign));
        }

        $campaign->update(['fb_campaign_id' => $fbCampaign['id']]);

        // 2. Créer l'AdSet (ciblage + budget)
        $fbAdSet = $this->createAdSet($campaign);

        if (!isset($fbAdSet['id'])) {
            throw new \Exception('Échec création AdSet: ' . json_encode($fbAdSet));
        }

        $campaign->update(['fb_adset_id' => $fbAdSet['id']]);

        // 3. Créer la publicité avec le post existant
        $fbAd = $this->createAd($campaign);

        if (!isset($fbAd['id'])) {
            throw new \Exception('Échec création publicité: ' . json_encode($fbAd));
        }

        $campaign->update([
            'fb_ad_id' => $fbAd['id'],
            'status' => 'active',
            'starts_at' => now(),
        ]);

        return $campaign;
    }

    protected function createCampaign(FacebookCampaign $campaign)
    {
        $response = Http::post(
            "https://graph.facebook.com/v18.0/{$this->adAccountId}/campaigns",
            [
                'name' => $campaign->name,
                'objective' => $this->getObjective($campaign),
                'status' => 'ACTIVE',
                'special_ad_categories' => [],
                'is_adset_budget_sharing_enabled' => false,
                'access_token' => $this->accessToken,
            ]
        );

        return $response->json();
    }

    protected function createAdSet(FacebookCampaign $campaign)
    {
        $targeting = $this->buildTargeting($campaign);

        $response = Http::post(
            "https://graph.facebook.com/v18.0/{$this->adAccountId}/adsets",
            [
                'name' => $campaign->name . ' - AdSet',
                'campaign_id' => $campaign->fb_campaign_id,
                'daily_budget' => intval($campaign->daily_budget * 100), // en centimes
                'bid_strategy' => 'LOWEST_COST_WITHOUT_CAP',  // ← ÇA
                'billing_event' => 'IMPRESSIONS',
                'optimization_goal' => 'REACH',
                'targeting' => json_encode($targeting),
                'start_time' => ($campaign->starts_at ?? now())->toIso8601String(),
                'end_time' => ($campaign->ends_at ?? now()->addDays((int) $campaign->duration_days))->toIso8601String(),
                'status' => 'ACTIVE',
                'is_adset_budget_sharing_enabled' => false,
                'access_token' => $this->accessToken,
            ]
        );

        return $response->json();
    }

    protected function createAd(FacebookCampaign $campaign)
    {
        $product = $campaign->product;

        $response = Http::post(
            "https://graph.facebook.com/v18.0/{$this->adAccountId}/ads",
            [
                'name' => $campaign->name . ' - Pub',
                'adset_id' => $campaign->fb_adset_id,
                'creative' => json_encode([
                    'object_story_id' => $product->facebook_post_id,
                ]),
                'status' => 'ACTIVE',
                'access_token' => $this->accessToken,
            ]
        );

        return $response->json();
    }

    protected function buildTargeting(FacebookCampaign $campaign)
    {
        $targeting = [
            'geo_locations' => ['countries' => ['SN']],
            'age_min' => 18,
            'age_max' => 65,
        ];

        if (!empty($campaign->targeting['cities'])) {
            $targeting['geo_locations']['cities'] = $campaign->targeting['cities'];
        }

        if (!empty($campaign->targeting['interests'])) {
            $targeting['interests'] = $campaign->targeting['interests'];
        }

        if (!empty($campaign->targeting['genders'])) {
            $targeting['genders'] = $campaign->targeting['genders'];
        }

        return $targeting;
    }

    public function getCampaignStats(FacebookCampaign $campaign)
    {
        if (!$campaign->fb_ad_id) {
            return null;
        }

        // Utiliser l'Ad pour les stats
        $response = Http::get(
            "https://graph.facebook.com/v18.0/{$campaign->fb_ad_id}/insights",
            [
                'fields' => 'reach,impressions,clicks,ctr,cpc,cpp,spend,actions',
                'date_preset' => 'maximum',
                'access_token' => $this->accessToken,
            ]
        );

        $data = $response->json();

        \Log::info('Facebook Stats Response', ['ad_id' => $campaign->fb_ad_id, 'response' => $data]);

        if (isset($data['data'][0])) {
            $stats = $data['data'][0];

            $campaign->update([
                'reach' => intval($stats['reach'] ?? 0),
                'impressions' => intval($stats['impressions'] ?? 0),
                'clicks' => intval($stats['clicks'] ?? 0),
                'spent' => floatval($stats['spend'] ?? 0),
                'ctr' => floatval($stats['ctr'] ?? 0),
                'cpc' => floatval($stats['cpc'] ?? 0),
                'cpp' => floatval($stats['cpp'] ?? 0),
                'stats' => $stats,
                'last_synced_at' => now(),
            ]);
        }

        return $campaign;
    }

    /**
     * Mettre en pause une campagne
     */
    public function pauseCampaign(FacebookCampaign $campaign)
    {
        if (!$campaign->fb_ad_id) {
            return false;
        }

        $response = Http::post(
            "https://graph.facebook.com/v18.0/{$campaign->fb_ad_id}",
            [
                'status' => 'PAUSED',
                'access_token' => $this->accessToken,
            ]
        );

        if ($response->successful()) {
            $campaign->update(['status' => 'paused']);
        }

        return $response->json();
    }

    /**
     * Réactiver une campagne
     */
    public function resumeCampaign(FacebookCampaign $campaign)
    {
        if (!$campaign->fb_ad_id) {
            return false;
        }

        $response = Http::post(
            "https://graph.facebook.com/v18.0/{$campaign->fb_ad_id}",
            [
                'status' => 'ACTIVE',
                'access_token' => $this->accessToken,
            ]
        );

        if ($response->successful()) {
            $campaign->update(['status' => 'active']);
        }

        return $response->json();
    }


    /**
     * Créer une campagne de retargeting
     */
    public function createRetargetingCampaign(FacebookCampaign $campaign)
    {
        // 1. Créer la campagne
        $fbCampaign = $this->createRetargetingCampaignFB($campaign);

        if (!isset($fbCampaign['id'])) {
            throw new \Exception('Échec création campagne retargeting: ' . json_encode($fbCampaign));
        }

        $campaign->update(['fb_campaign_id' => $fbCampaign['id']]);

        // 2. Créer l'AdSet avec audience personnalisée
        $fbAdSet = $this->createRetargetingAdSet($campaign);

        if (!isset($fbAdSet['id'])) {
            throw new \Exception('Échec création AdSet retargeting: ' . json_encode($fbAdSet));
        }

        $campaign->update(['fb_adset_id' => $fbAdSet['id']]);

        // 3. Créer la publicité catalogue dynamique
        $fbAd = $this->createRetargetingAd($campaign);

        if (!isset($fbAd['id'])) {
            throw new \Exception('Échec création pub retargeting: ' . json_encode($fbAd));
        }

        $campaign->update([
            'fb_ad_id' => $fbAd['id'],
            'status' => 'active',
            'starts_at' => now(),
        ]);

        return $campaign;
    }



    protected function createRetargetingCampaignFB(FacebookCampaign $campaign)
    {
        $response = Http::post(
            "https://graph.facebook.com/v18.0/{$this->adAccountId}/campaigns",
            [
                'name' => $campaign->name,
                'objective' => $this->getObjective($campaign),
                'status' => 'ACTIVE',
                'special_ad_categories' => [],
                'is_adset_budget_sharing_enabled' => false,  // ← AJOUTER
                'access_token' => $this->accessToken,
            ]
        );

        return $response->json();
    }

    protected function createRetargetingAdSet(FacebookCampaign $campaign)
    {
        $shop = $campaign->shop;
        $targeting = $campaign->targeting ?? [];

        // Créer ou récupérer le Product Set
        $productSetId = $this->getOrCreateProductSet($shop);

        // Audience de retargeting : visiteurs des 30 derniers jours
        $response = Http::post(
            "https://graph.facebook.com/v18.0/{$this->adAccountId}/adsets",
            [
                'name' => $campaign->name . ' - AdSet',
                'campaign_id' => $campaign->fb_campaign_id,
                'daily_budget' => intval($campaign->daily_budget * 100),
                'bid_strategy' => 'LOWEST_COST_WITHOUT_CAP',
                'billing_event' => 'IMPRESSIONS',
                'optimization_goal' => 'IMPRESSIONS',
                'targeting' => json_encode(array_merge($targeting, [
                    'geo_locations' => ['countries' => ['SN']],
                    'age_min' => 18,
                    'age_max' => 65,
                    'custom_audiences' => [], // Sera rempli dynamiquement par Facebook via le Pixel
                ])),
                'start_time' => ($campaign->starts_at ?? now())->toIso8601String(),
                'end_time' => ($campaign->ends_at ?? now()->addDays((int) $campaign->duration_days))->toIso8601String(),
                'status' => 'ACTIVE',
                'is_adset_budget_sharing_enabled' => false,
                'access_token' => $this->accessToken,
            ]
        );

        return $response->json();
    }

    protected function createRetargetingAd(FacebookCampaign $campaign)
    {
        $shop = $campaign->shop;

        $response = Http::post(
            "https://graph.facebook.com/v18.0/{$this->adAccountId}/ads",
            [
                'name' => $campaign->name . ' - Pub',
                'adset_id' => $campaign->fb_adset_id,
                'creative' => json_encode([
                    'object_story_spec' => [
                        'page_id' => $this->pageId,
                        'template_data' => [
                            'format_option' => 'carousel_images_multi_items',
                            'multi_share_end_card' => false,
                            'link' => "https://app.billeteriexpress.com/shop/{$shop->slug}",
                            'name' => 'Découvrez nos produits',
                            'description' => 'Revenez finaliser votre commande !',
                            'call_to_action' => ['type' => 'SHOP_NOW'],
                        ],
                    ],
                    'product_set_id' => $shop->facebook_product_set_id,
                ]),
                'status' => 'ACTIVE',
                'access_token' => $this->accessToken,
            ]
        );

        return $response->json();
    }

    /**
     * Créer un ensemble de produits pour le retargeting
     */
    protected function getOrCreateProductSet($shop)
    {
        if (!empty($shop->facebook_product_set_id)) {
            return $shop->facebook_product_set_id;
        }

        // Essayer de créer
        $response = Http::post(
            "https://graph.facebook.com/v18.0/{$shop->facebook_catalog_id}/product_sets",
            [
                'name' => 'Tous les produits - ' . $shop->name,
                'access_token' => $this->accessToken,
            ]
        );

        $data = $response->json();

        // Si déjà existant, récupérer l'ID
        if (isset($data['error']['error_data']['product_set_id'])) {
            $productSetId = $data['error']['error_data']['product_set_id'];
            $shop->update(['facebook_product_set_id' => $productSetId]);
            return $productSetId;
        }

        if (isset($data['id'])) {
            $shop->update(['facebook_product_set_id' => $data['id']]);
            return $data['id'];
        }

        throw new \Exception('Impossible de créer le Product Set: ' . json_encode($data));
    }

    public function createWhatsAppCampaign(FacebookCampaign $campaign)
    {
        $shop = $campaign->shop;
        $whatsappNumber = $campaign->whatsapp_number ?? $shop->whatsapp_phone;
        $message = $campaign->whatsapp_message ?? "Bonjour, je voudrais commander :";

        // 1. Campagne
        $fbCampaign = Http::post("https://graph.facebook.com/v18.0/{$this->adAccountId}/campaigns", [
            'name' => $campaign->name,
            'objective' => 'OUTCOME_TRAFFIC',
            'status' => 'ACTIVE',
            'special_ad_categories' => [],
            'is_adset_budget_sharing_enabled' => false,  // ← AJOUTER
            'access_token' => $this->accessToken,
        ])->json();

        if (!isset($fbCampaign['id'])) {
            throw new \Exception('Échec création campagne: ' . json_encode($fbCampaign));
        }
        $campaign->update(['fb_campaign_id' => $fbCampaign['id']]);

        // 2. AdSet
        $fbAdSet = Http::post("https://graph.facebook.com/v18.0/{$this->adAccountId}/adsets", [
            'name' => $campaign->name . ' - AdSet',
            'campaign_id' => $campaign->fb_campaign_id,
            'daily_budget' => intval($campaign->daily_budget * 100),
            'bid_strategy' => 'LOWEST_COST_WITHOUT_CAP',
            'billing_event' => 'IMPRESSIONS',
            'optimization_goal' => 'REACH',
            'is_adset_budget_sharing_enabled' => false,  // ← AJOUTER
            'targeting' => json_encode([
                'geo_locations' => ['countries' => ['SN']],
                'age_min' => 18,
                'age_max' => 65,
            ]),
            'start_time' => now()->toIso8601String(),
            'end_time' => now()->addDays((int) $campaign->duration_days)->toIso8601String(),
            'status' => 'ACTIVE',
            'access_token' => $this->accessToken,
        ])->json();

        if (!isset($fbAdSet['id'])) {
            throw new \Exception('Échec création AdSet: ' . json_encode($fbAdSet));
        }
        $campaign->update(['fb_adset_id' => $fbAdSet['id']]);

// Uploader l'image d'abord
        $imageHash = null;
        if ($campaign->whatsapp_image) {
            $imagePath = storage_path('app/public/' . $campaign->whatsapp_image);
            $uploadResponse = Http::attach(
                'source', file_get_contents($imagePath), basename($imagePath)
            )->post("https://graph.facebook.com/v18.0/{$this->adAccountId}/adimages", [
                'access_token' => $this->accessToken,
            ])->json();

            $imageHash = $uploadResponse['images'][basename($imagePath)]['hash'] ?? null;
        }

// Puis utiliser l'image hash dans la pub
        $fbAd = Http::post("https://graph.facebook.com/v18.0/{$this->adAccountId}/ads", [
            'name' => $campaign->name . ' - Pub',
            'adset_id' => $campaign->fb_adset_id,
            'creative' => json_encode([
                'object_story_spec' => [
                    'page_id' => $this->pageId,
                    'link_data' => [
                        'link' => "https://wa.me/" . preg_replace('/[^0-9]/', '', $whatsappNumber) . "?text=" . urlencode($message),
                        'message' => $message,
                        'name' => 'Commander via WhatsApp',
                        'description' => 'Échangez directement avec nous !',
                        'image_hash' => $imageHash,
                        'call_to_action' => ['type' => 'WHATSAPP_MESSAGE'],
                    ],
                ],
            ]),
            'status' => 'ACTIVE',
            'access_token' => $this->accessToken,
        ])->json();

        if (!isset($fbAd['id'])) {
            throw new \Exception('Échec création pub WhatsApp: ' . json_encode($fbAd));
        }

        $campaign->update([
            'fb_ad_id' => $fbAd['id'],
            'status' => 'active',
            'starts_at' => now(),
        ]);

        return $campaign;
    }



    /**
     * Envoyer un événement via l'API Conversions (CAPI)
     */
    public function sendConversionEvent($eventName, $userData, $customData, $shop)
    {
        if (!$shop->facebook_pixel_id) return;

        $payload = [
            'data' => [[
                'event_name' => $eventName,
                'event_time' => now()->timestamp,
                'action_source' => 'website',
                'event_source_url' => request()->fullUrl(),
                'user_data' => $userData,
                'custom_data' => $customData,
            ]],
            'access_token' => $shop->facebook_capi_token ?: env('FACEBOOK_CAPI_TOKEN', $this->accessToken),
        ];

        try {
            Http::post("https://graph.facebook.com/v18.0/{$shop->facebook_pixel_id}/events", $payload);
        } catch (\Exception $e) {
            \Log::error('CAPI error: ' . $e->getMessage());
        }
    }
}
