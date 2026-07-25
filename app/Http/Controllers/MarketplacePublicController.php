<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Product;
use App\Models\MarketplaceSubscription;
use Illuminate\Http\Request;

class MarketplacePublicController extends Controller
{
    /**
     * Affiche la page d'accueil de la Marketplace avec toutes les boutiques actives.
     */
    public function index()
    {
        // Récupérer uniquement les boutiques approuvées et actives
        // ET qui ont un abonnement marketplace actif
        $shops = Shop::where('is_active', true)
            ->where('status', 'approved')
            ->whereHas('marketplaceSubscription', function ($query) {
                $query->where('status', 'active')
                    ->where('expires_at', '>', now());
            })
            ->withCount('products')
            ->orderBy('name')
            ->paginate(12);

        $totalProducts = Product::where('is_available', true)
            ->whereHas('shop', function ($query) {
                $query->where('is_active', true)->where('status', 'approved');
            })
            ->count();

        return view('marketplace.public.index', compact('shops', 'totalProducts'));
    }


    public function allProducts(Request $request)
    {
        $query = Product::where('is_available', true)
            ->where('published_on_marketplace', true)
            ->whereHas('shop', function ($query) {
                $query->where('is_active', true)
                    ->where('status', 'approved')
                    ->whereHas('marketplaceSubscription', function ($subQuery) {
                        $subQuery->where('status', 'active')
                            ->where('expires_at', '>', now());
                    });
            })
            ->with(['shop', 'category', 'reviews']);

        // Filtre par catégorie
        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        // Filtre par prix minimum
        if ($request->has('prix_min') && $request->prix_min != '') {
            $query->whereRaw('COALESCE(sale_price, price) >= ?', [$request->prix_min]);
        }

        // Filtre par prix maximum
        if ($request->has('prix_max') && $request->prix_max != '') {
            $query->whereRaw('COALESCE(sale_price, price) <= ?', [$request->prix_max]);
        }

        // Tri par prix
        if ($request->has('tri') && in_array($request->tri, ['asc', 'desc'])) {
            $query->orderByRaw('COALESCE(sale_price, price) ' . $request->tri);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Filtre par recherche
        if ($request->has('q') && $request->q != '') {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        $products = $query->paginate(20);
        $products->appends($request->all());

        // Catégories (inchangé)
        $categories = \App\Models\Category::whereHas('products', function ($query) {
            $query->where('is_available', true)
                ->where('published_on_marketplace', true)
                ->whereHas('shop', function ($q) {
                    $q->where('is_active', true)
                        ->where('status', 'approved')
                        ->whereHas('marketplaceSubscription', function ($subQ) {
                            $subQ->where('status', 'active')
                                ->where('expires_at', '>', now());
                        });
                });
        })->withCount(['products' => function ($query) {
            $query->where('is_available', true)
                ->where('published_on_marketplace', true)
                ->whereHas('shop', function ($q) {
                    $q->where('is_active', true)
                        ->where('status', 'approved')
                        ->whereHas('marketplaceSubscription', function ($subQ) {
                            $subQ->where('status', 'active')
                                ->where('expires_at', '>', now());
                        });
                });
        }])->orderBy('name')->get();

        $totalProducts = $products->total();

        return view('marketplace.public.all-products', compact('products', 'categories', 'totalProducts'));
    }


    /**
     * Affiche les produits en promotion sur la marketplace.
     */

    public function promotions(Request $request)
    {
        $query = Product::where('is_available', true)
            ->where('published_on_marketplace', true)
            ->whereNotNull('sale_price')
            ->whereColumn('sale_price', '<', 'price')
            ->whereHas('shop', function ($query) {
                $query->where('is_active', true)
                    ->where('status', 'approved')
                    ->whereHas('marketplaceSubscription', function ($subQuery) {
                        $subQuery->where('status', 'active')
                            ->where('expires_at', '>', now());
                    });
            })
            ->with(['shop', 'category', 'reviews']);

        // Filtre par catégorie
        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        // Filtre par prix minimum
        if ($request->has('prix_min') && $request->prix_min != '') {
            $query->where('sale_price', '>=', $request->prix_min);
        }

        // Filtre par prix maximum
        if ($request->has('prix_max') && $request->prix_max != '') {
            $query->where('sale_price', '<=', $request->prix_max);
        }

        // Tri par prix
        if ($request->has('tri') && in_array($request->tri, ['asc', 'desc'])) {
            $query->orderBy('sale_price', $request->tri);
        } else {
            $query->orderByRaw('((price - sale_price) / price) DESC');
        }

        $products = $query->paginate(20);
        $products->appends($request->all());

        // Catégories (inchangé)
        $categories = \App\Models\Category::whereHas('products', function ($query) {
            $query->where('is_available', true)
                ->where('published_on_marketplace', true)
                ->whereNotNull('sale_price')
                ->whereColumn('sale_price', '<', 'price')
                ->whereHas('shop', function ($q) {
                    $q->where('is_active', true)
                        ->where('status', 'approved')
                        ->whereHas('marketplaceSubscription', function ($subQ) {
                            $subQ->where('status', 'active')
                                ->where('expires_at', '>', now());
                        });
                });
        })->withCount(['products' => function ($query) {
            $query->where('is_available', true)
                ->where('published_on_marketplace', true)
                ->whereNotNull('sale_price')
                ->whereColumn('sale_price', '<', 'price')
                ->whereHas('shop', function ($q) {
                    $q->where('is_active', true)
                        ->where('status', 'approved')
                        ->whereHas('marketplaceSubscription', function ($subQ) {
                            $subQ->where('status', 'active')
                                ->where('expires_at', '>', now());
                        });
                });
        }])->orderBy('name')->get();

        $totalProducts = $products->total();

        return view('marketplace.public.promotions', compact('products', 'categories', 'totalProducts'));
    }


    /**
     * Affiche les nouveautés sur la marketplace.
     */
    public function nouveautes(Request $request)
    {
        $query = Product::where('is_available', true)
            ->where('published_on_marketplace', true)
            ->whereHas('shop', function ($query) {
                $query->where('is_active', true)
                    ->where('status', 'approved')
                    ->whereHas('marketplaceSubscription', function ($subQuery) {
                        $subQuery->where('status', 'active')
                            ->where('expires_at', '>', now());
                    });
            })
            ->with(['shop', 'category', 'reviews']);

        // Filtre par catégorie
        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        // Filtre par prix minimum
        if ($request->has('prix_min') && $request->prix_min != '') {
            $query->whereRaw('COALESCE(sale_price, price) >= ?', [$request->prix_min]);
        }

        // Filtre par prix maximum
        if ($request->has('prix_max') && $request->prix_max != '') {
            $query->whereRaw('COALESCE(sale_price, price) <= ?', [$request->prix_max]);
        }

        // Tri par prix
        if ($request->has('tri') && in_array($request->tri, ['asc', 'desc'])) {
            $query->orderByRaw('COALESCE(sale_price, price) ' . $request->tri);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(20);
        $products->appends($request->all());

        // Catégories (inchangé)
        $categories = \App\Models\Category::whereHas('products', function ($query) {
            $query->where('is_available', true)
                ->where('published_on_marketplace', true)
                ->where('created_at', '>=', now()->subDays(30))
                ->whereHas('shop', function ($q) {
                    $q->where('is_active', true)
                        ->where('status', 'approved')
                        ->whereHas('marketplaceSubscription', function ($subQ) {
                            $subQ->where('status', 'active')
                                ->where('expires_at', '>', now());
                        });
                });
        })->withCount(['products' => function ($query) {
            $query->where('is_available', true)
                ->where('published_on_marketplace', true)
                ->where('created_at', '>=', now()->subDays(30))
                ->whereHas('shop', function ($q) {
                    $q->where('is_active', true)
                        ->where('status', 'approved')
                        ->whereHas('marketplaceSubscription', function ($subQ) {
                            $subQ->where('status', 'active')
                                ->where('expires_at', '>', now());
                        });
                });
        }])->orderBy('name')->get();

        $totalProducts = $products->total();

        return view('marketplace.public.nouveautes', compact('products', 'categories', 'totalProducts'));
    }


    /**
     * Affiche toutes les boutiques de la marketplace.
     */
    public function allShops(Request $request)
    {
        $query = Shop::where('is_active', true)
            ->where('status', 'approved')
            ->whereHas('marketplaceSubscription', function ($query) {
                $query->where('status', 'active')
                    ->where('expires_at', '>', now());
            })
            ->withCount('products');

        // Recherche par nom
        if ($request->has('q') && $request->q != '') {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        $shops = $query->orderBy('name')->paginate(16);
        $shops->appends($request->all());

        $totalShops = $shops->total();

        return view('marketplace.public.shops', compact('shops', 'totalShops'));
    }

    /**
     * Affiche la page "Vendre sur Xala".
     */
    public function vendre()
    {
        return view('marketplace.public.vendre');
    }

    /**
     * Affiche la page contact.
     */
    public function contact()
    {
        return view('marketplace.public.contact');
    }


}
