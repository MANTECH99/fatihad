<?php
// app/Http/Controllers/FacebookController.php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FacebookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function connect(Request $request)
    {
        // Récupérer le shop_id depuis le paramètre GET
        $shopId = $request->shop_id;

        if (!$shopId) {
            return redirect()->back()->with('error', 'Boutique non spécifiée.');
        }

        // Stocker en session pour le callback
        session(['facebook_connect_shop_id' => $shopId]);

        $appId = config('services.facebook.app_id');
        $redirectUri = route('merchant.facebook.callback');

        $url = "https://www.facebook.com/v18.0/dialog/oauth?" . http_build_query([
                'client_id' => $appId,
                'redirect_uri' => $redirectUri,
                'scope' => 'pages_show_list,pages_read_engagement,pages_manage_posts,ads_management,ads_read,catalog_management',
                'response_type' => 'code',
            ]);

        return redirect($url);
    }

    public function callback(Request $request)
    {
        // Récupérer le shop_id depuis la session
        $shopId = session('facebook_connect_shop_id');

        if (!$shopId) {
            return redirect()->route('merchant.dashboard')
                ->with('error', 'Session expirée. Veuillez réessayer.');
        }

        $shop = Shop::findOrFail($shopId);
        $this->authorize('update', $shop);

        // Échanger le code contre un token
        $appId = config('services.facebook.app_id');
        $appSecret = config('services.facebook.app_secret');
        $redirectUri = route('merchant.facebook.callback');

        $response = Http::get('https://graph.facebook.com/v18.0/oauth/access_token', [
            'client_id' => $appId,
            'client_secret' => $appSecret,
            'redirect_uri' => $redirectUri,
            'code' => $request->code,
        ]);

        $data = $response->json();

        // 🔍 DEBUG
        \Log::info('Facebook OAuth Response', ['data' => $data]);

        // Si erreur, on l'affiche
        if (isset($data['error'])) {
            dd($data['error']);
        }
        if (isset($data['access_token'])) {
            // Récupérer les pages de l'utilisateur
            $pagesResponse = Http::get('https://graph.facebook.com/v18.0/me/accounts', [
                'access_token' => $data['access_token'],
            ]);

            $pages = $pagesResponse->json();

            // 🔍 DEBUG
            \Log::info('Facebook Pages Response', ['pages' => $pages]);

            if (isset($pages['error'])) {
                dd($pages['error']);
            }

            if (isset($pages['data'][0])) {
                $page = $pages['data'][0];

                $shop->update([
                    'facebook_access_token' => $page['access_token'],
                    'facebook_page_id' => $page['id'],
                    'facebook_page_name' => $page['name'],
                    'facebook_connected_at' => now(),
                ]);

                // Nettoyer la session
                session()->forget('facebook_connect_shop_id');

                return redirect()->route('merchant.products.create', $shop)
                    ->with('success', 'Page Facebook connectée avec succès !');
            }
        }

        return redirect()->route('merchant.products.create', $shop)
            ->with('error', 'Erreur lors de la connexion Facebook.');
    }

    public function disconnect(Request $request)
    {
        // Récupérer le shop_id depuis la requête
        $shopId = $request->shop_id;
        $shop = Shop::findOrFail($shopId);

        $this->authorize('update', $shop);

        $shop->update([
            'facebook_access_token' => null,
            'facebook_page_id' => null,
            'facebook_page_name' => null,
            'facebook_connected_at' => null,
        ]);

        return redirect()->back()->with('success', 'Page Facebook déconnectée.');
    }

    public function stats(Shop $shop)
    {
        $this->authorize('view', $shop);

        if (!$shop->hasFacebookConnected()) {
            return redirect()->back()->with('error', 'Page Facebook non connectée.');
        }

        $fbService = new \App\Services\FacebookService(
            $shop->facebook_access_token,
            $shop->facebook_page_id
        );

        $pageStats = $fbService->getPageStats($shop->facebook_page_id);

        $products = $shop->products()
            ->whereNotNull('facebook_post_id')
            ->get()
            ->map(function ($product) use ($fbService) {
                $product->fb_stats = $fbService->getPostEngagement($product->facebook_post_id);
                return $product;
            });

        return view('merchant.facebook.stats', compact('shop', 'pageStats', 'products'));
    }
}
