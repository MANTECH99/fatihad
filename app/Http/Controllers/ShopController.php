<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Services\ImageService;
use App\Services\PlanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ShopController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
        $this->middleware('auth');
    }

    public function index()
    {
        $shops = Auth::user()->shops()->withCount(['orders', 'products'])->get();
        return view('merchant.shops.index', compact('shops'));
    }

    public function create()
    {
        return view('merchant.shops.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'whatsapp_phone' => 'required|string|max:20',
            'wave_number' => 'nullable|string|max:20',           // ← AJOUTER
            'orange_money_number' => 'nullable|string|max:20',   // ← AJOUTER
            'payout_method' => 'nullable|string|in:wave,orange_money', // ← AJOUTER
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'delivery_zones' => 'nullable|array',  // ← Changer aussi
            'delivery_fee' => 'nullable|numeric|min:0',
            'min_order' => 'nullable|numeric|min:0',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'opening_hours' => 'nullable|array',
            'is_open' => 'nullable|boolean',
            'stamp' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // <-- AJOUTER ICI
            'facebook_ad_account_id' => 'nullable|string|max:50',
            'facebook_catalog_id' => 'nullable|string|max:50',
            'facebook_pixel_id' => 'nullable|string|max:50',  // ← AJOUTER
            'facebook_capi_token' => 'nullable|string|max:255',
        ]);


        // ← AJOUTE ICI
        if (!PlanService::canCreateShop(auth()->user())) {
            return view('merchant.upgrade', [
                'message' => 'Limite de boutiques atteinte',
                'detail' => 'Votre plan ' . PlanService::get(auth()->user()->plan)['name'] . ' vous permet de créer ' . PlanService::get(auth()->user()->plan)['shops'] . ' boutique(s). Passez à un plan supérieur pour continuer.'
            ]);
        }

        $shop = new Shop($validated);
        $shop->user_id = Auth::id();
        $shop->slug = Str::slug($validated['name']) . '-' . Str::random(6);

// Par :
        $shop->delivery_zones = $request->delivery_zones;

        // Upload images
        if ($request->hasFile('logo')) {
            $shop->logo = $this->imageService->uploadAndOptimize(
                $request->file('logo'), 'shops/logos', 400
            );
        }

        if ($request->hasFile('cover_image')) {
            $shop->cover_image = $this->imageService->uploadAndOptimize(
                $request->file('cover_image'), 'shops/covers', 1200
            );
        }

        // Upload du cachet
        if ($request->hasFile('stamp')) {
            $shop->stamp = $this->imageService->uploadAndOptimize(
                $request->file('stamp'), 'shops/stamps', 400
            );
        }

        $shop->save();

        return redirect()->route('merchant.shops.edit', $shop)
            ->with('success', 'Boutique créée avec succès !');
    }

    public function edit(Shop $shop)
    {
        $this->authorize('update', $shop);
        return view('merchant.shops.edit', compact('shop'));
    }

    public function update(Request $request, Shop $shop)
    {
        $this->authorize('update', $shop);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'whatsapp_phone' => 'required|string|max:20',
            'wave_number' => 'nullable|string|max:20',           // ← AJOUTER
            'orange_money_number' => 'nullable|string|max:20',   // ← AJOUTER
            'payout_method' => 'nullable|string|in:wave,orange_money', // ← AJOUTER
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'delivery_zones' => 'nullable|array',  // ← Changer string en array
            'delivery_fee' => 'nullable|numeric|min:0',
            'min_order' => 'nullable|numeric|min:0',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'opening_hours' => 'nullable|array',
            'is_open' => 'nullable|boolean',
            'stamp' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // <-- AJOUTER ICI
            'facebook_ad_account_id' => 'nullable|string|max:50',
            'facebook_catalog_id' => 'nullable|string|max:50',
            'facebook_pixel_id' => 'nullable|string|max:50',  // ← AJOUTER
            'facebook_capi_token' => 'nullable|string|max:255',
        ]);

        $validated['delivery_zones'] = $request->has('delivery_zones')
            ? $request->delivery_zones
            : null;

        $validated['is_open'] = $request->boolean('is_open');

        // Supprimer le logo si demandé
        if ($request->delete_logo == '1' && $shop->logo) {
            $this->imageService->delete($shop->logo);
            $validated['logo'] = null;
        }

// Supprimer la couverture si demandée
        if ($request->delete_cover == '1' && $shop->cover_image) {
            $this->imageService->delete($shop->cover_image);
            $validated['cover_image'] = null;
        }


        // Upload new images
        if ($request->hasFile('logo')) {
            $this->imageService->delete($shop->logo);
            $validated['logo'] = $this->imageService->uploadAndOptimize(
                $request->file('logo'), 'shops/logos', 400
            );
        }

        if ($request->hasFile('cover_image')) {
            $this->imageService->delete($shop->cover_image);
            $validated['cover_image'] = $this->imageService->uploadAndOptimize(
                $request->file('cover_image'), 'shops/covers', 1200
            );
        }

        // Supprimer le cachet si demandé
        if ($request->delete_stamp == '1' && $shop->stamp) {
            $this->imageService->delete($shop->stamp);
            $validated['stamp'] = null;
        }

        // Upload du cachet (s'il y a un nouveau fichier)
        if ($request->hasFile('stamp')) {
            // On supprime l'ancien avant de mettre le nouveau
            if ($shop->stamp) {
                $this->imageService->delete($shop->stamp);
            }
            $validated['stamp'] = $this->imageService->uploadAndOptimize(
                $request->file('stamp'), 'shops/stamps', 400
            );
        }

        $shop->update($validated);

        return redirect()->back()->with('success', 'Boutique mise à jour avec succès !');
    }

    public function toggleStatus(Shop $shop)
    {
        $this->authorize('update', $shop);

        $shop->update(['is_open' => !$shop->is_open]);

        $status = $shop->is_open ? 'ouverte' : 'fermée';
        return response()->json([
            'success' => true,
            'message' => "Boutique {$status} avec succès.",
            'is_open' => $shop->is_open
        ]);
    }

    public function destroy(Shop $shop)
    {
        $this->authorize('delete', $shop);

        // Delete images
        $this->imageService->delete($shop->logo);
        $this->imageService->delete($shop->cover_image);

        // Force delete = suppression définitive
        $shop->forceDelete();

        return redirect()->route('merchant.shops.index')
            ->with('success', 'Boutique supprimée avec succès.');
    }
}
