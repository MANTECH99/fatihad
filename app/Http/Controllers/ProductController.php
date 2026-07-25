<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceSubscription;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Category;
use App\Models\StockMovement;
use App\Services\FacebookService;
use App\Services\ImageService;
use App\Services\PlanService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
        $this->middleware('auth');
    }

    public function index(Shop $shop)
    {
        $this->authorize('view', $shop);

        $products = $shop->products()
            ->with('category')
            ->orderBy('order')
            ->paginate(20);

        return view('merchant.products.index', compact('shop', 'products'));
    }

    public function create(Shop $shop)
    {
        $this->authorize('update', $shop);

        $categories = $shop->categories()->orderBy('name')->get();
        return view('merchant.products.create', compact('shop', 'categories'));
    }

    public function store(Request $request, Shop $shop)
    {
        $this->authorize('update', $shop);

        $validated = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'gallery' => 'nullable|array',
            'gallery.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'options' => 'nullable|array',
            'is_available' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'sku' => 'nullable|string|max:100',
            'cost_price' => 'nullable|numeric|min:0',  // ← AJOUTER
            'stock' => 'nullable|integer|min:0',
            'track_inventory' => 'nullable|boolean',
            'stock_alert' => 'nullable|integer|min:0',    // ← AJOUTER
            'supplier' => 'nullable|string|max:255',     // ← AJOUTER
        ]);

        // ← AJOUTE ICI
        if (!PlanService::canAddProduct(auth()->user(), $shop)) {
            return view('merchant.upgrade', [
                'message' => 'Limite de produits atteinte',
                'detail' => 'Votre plan ' . PlanService::get(auth()->user()->plan)['name'] . ' vous permet d\'ajouter ' . PlanService::get(auth()->user()->plan)['products'] . ' produit(s). Passez à un plan supérieur pour continuer.'
            ]);
        }

        $product = new Product($validated);
        $product->shop_id = $shop->id;
        $product->is_available = $request->boolean('is_available', true);
        $product->is_featured = $request->boolean('is_featured', false);

        // Upload image principale
        if ($request->hasFile('image')) {
            $product->image = $this->imageService->uploadAndOptimize(
                $request->file('image'), 'shops/' . $shop->id . '/products', 800
            );
        }

        // Upload galerie
        if ($request->hasFile('gallery')) {
            $product->gallery = $this->imageService->uploadMultiple(
                $request->file('gallery'), 'shops/' . $shop->id . '/products/gallery', 800
            );
        }

        // Formater les options
        if ($request->options) {
            $product->options = $this->formatOptions($request->options);
        }

        $product->order = $shop->products()->max('order') + 1;
        $product->save();

// Dans ProductController@store, remplacer le bloc Facebook par :
        if ($request->boolean('publish_to_facebook') && $shop->hasFacebookConnected()) {
            try {
                $facebookService = new FacebookService(
                    $shop->facebook_access_token,
                    $shop->facebook_page_id
                );

                $fbResponse = $facebookService->publishProduct($product, $shop);

                if (isset($fbResponse['id']) || isset($fbResponse['post_id'])) {
                    $postId = $fbResponse['post_id'] ?? $fbResponse['id'];
                    $product->update(['facebook_post_id' => $postId]);

                    \Log::info('Facebook publish success', ['post_id' => $postId]);
                }
            } catch (\Exception $e) {
                \Log::error('Facebook publish error: ' . $e->getMessage());
            }
        }

        return redirect()->route('merchant.products.index', $shop)
            ->with('success', 'Produit ajouté avec succès !');
    }

    public function edit(Shop $shop, Product $product)
    {
        $this->authorize('update', $shop);

        $categories = $shop->categories()->orderBy('name')->get();
        return view('merchant.products.edit', compact('shop', 'product', 'categories'));
    }

    public function update(Request $request, Shop $shop, Product $product)
    {
        $this->authorize('update', $shop);

        $validated = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'gallery' => 'nullable|array',
            'gallery.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'options' => 'nullable|array',
            'is_available' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'sku' => 'nullable|string|max:100',
            'cost_price' => 'nullable|numeric|min:0',  // ← AJOUTER
            'stock' => 'nullable|integer|min:0',
            'track_inventory' => 'nullable|boolean',
            'stock_alert' => 'nullable|integer|min:0',    // ← AJOUTER
            'supplier' => 'nullable|string|max:255',     // ← AJOUTER
        ]);

        $validated['is_available'] = $request->boolean('is_available', true);
        $validated['is_featured'] = $request->boolean('is_featured', false);

        // Upload nouvelle image
        if ($request->hasFile('image')) {
            $this->imageService->delete($product->image);
            $validated['image'] = $this->imageService->uploadAndOptimize(
                $request->file('image'), 'shops/' . $shop->id . '/products', 800
            );
        }

        // Upload nouvelle galerie (ajout aux existantes)
        if ($request->hasFile('gallery')) {
            $existingGallery = $product->gallery ?? [];
            $newImages = $this->imageService->uploadMultiple(
                $request->file('gallery'), 'shops/' . $shop->id . '/products/gallery', 800
            );
            $validated['gallery'] = array_merge($existingGallery, $newImages);
        }

        // Formater les options
        if ($request->options) {
            $validated['options'] = $this->formatOptions($request->options);
        }

        $product->update($validated);

        return redirect()->back()->with('success', 'Produit mis à jour avec succès !');
    }

    public function destroy(Shop $shop, Product $product)
    {
        $this->authorize('update', $shop);

        // Delete images
        $this->imageService->delete($product->image);
        if ($product->gallery) {
            foreach ($product->gallery as $image) {
                $this->imageService->delete($image);
            }
        }

        $product->delete();

        return redirect()->route('merchant.products.index', $shop)
            ->with('success', 'Produit supprimé avec succès.');
    }

    public function toggleAvailability(Shop $shop, Product $product)
    {
        $this->authorize('update', $shop);

        $product->update(['is_available' => !$product->is_available]);

        $status = $product->is_available ? 'disponible' : 'indisponible';
        return response()->json([
            'success' => true,
            'message' => "Produit marqué comme {$status}.",
            'is_available' => $product->is_available
        ]);
    }

    public function removeGalleryImage(Shop $shop, Product $product, $index)
    {
        $this->authorize('update', $shop);

        if (isset($product->gallery[$index])) {
            $this->imageService->delete($product->gallery[$index]);
            $gallery = $product->gallery;
            unset($gallery[$index]);
            $product->update(['gallery' => array_values($gallery)]);
        }

        return response()->json(['success' => true, 'message' => 'Image supprimée.']);
    }

    protected function formatOptions($options)
    {
        $formatted = [];

        foreach ($options['name'] as $index => $name) {
            if (empty($name)) continue;

            $formatted[] = [
                'name' => $name,
                'values' => explode(',', $options['values'][$index] ?? ''),
                'prices' => array_map('floatval', explode(',', $options['prices'][$index] ?? '0')),
                'required' => isset($options['required'][$index]),
            ];
        }

        return $formatted ?: null;
    }

    // Afficher la page de gestion des produits Marketplace
    public function marketplaceIndex(Shop $shop)
    {
        $this->authorize('update', $shop);

        $products = $shop->products()
            ->where('is_available', true)
            ->orderBy('order')
            ->get();

        return view('merchant.products.marketplace', compact('shop', 'products'));
    }

    public function updateMarketplace(Request $request, Shop $shop)
    {
        $this->authorize('update', $shop);

        $selectedIds = $request->input('products', []);
        $countToPublish = count($selectedIds);

        // Récupérer l'abonnement actif
        $subscription = MarketplaceSubscription::where('shop_id', $shop->id)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->first();

        if (!$subscription) {
            return redirect()->back()->with('error', 'Vous n\'avez pas d\'abonnement Marketplace actif.');
        }

        // Vérifier la limite du plan
        $plan = PlanService::$marketplacePlans[$subscription->plan];
        $max = $plan['max_products'];

        if ($max !== -1 && $countToPublish > $max) {
            return redirect()->back()->with('error',
                "Votre plan {$plan['name']} limite la publication à {$max} produits. Vous avez sélectionné {$countToPublish} produits."
            );
        }

        // Mettre à jour les produits
        $shop->products()->update(['published_on_marketplace' => false]);
        if (!empty($selectedIds)) {
            $shop->products()
                ->whereIn('id', $selectedIds)
                ->update(['published_on_marketplace' => true]);
        }

        return redirect()->back()->with('success', 'Vos produits Marketplace ont été mis à jour !');
    }


    public function stockIndex(Shop $shop)
    {

        // ✅ Récupérer le plan (sécurisé avec ?? 'free')
        //  $userPlan = auth()->user()->plan ?? 'free';

        // ✅ Vérification : accès refusé SEULEMENT si le plan est 'free'
        // if ($userPlan === 'free') {
            // Afficher le bloc Premium au lieu de la page des ventes
        //  return view('merchant.partials.premium-block');
        //  }
        $this->authorize('view', $shop);

        // Récupérer les produits
        $products = $shop->products()
            ->orderBy('name')
            ->get();

        // Récupérer l'historique des mouvements (les 50 derniers pour ne pas surcharger)
        $movements = StockMovement::where('shop_id', $shop->id)
            ->with('product') // Pour charger le nom du produit
            ->latest()
            ->paginate(50); // Pagination pour éviter de tout charger

        return view('merchant.stocks.index', compact('shop', 'products', 'movements'));
    }

    public function stockMovement(Request $request, Shop $shop)
    {
        $this->authorize('update', $shop);

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:entry,return,loss,sortie',
            'quantity' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:255',
            'new_cost_price' => 'nullable|numeric|min:0',
            'new_sale_price' => 'nullable|numeric|min:0',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // 1. Enregistrer le mouvement dans l'historique
        StockMovement::create([
            'shop_id' => $shop->id,
            'product_id' => $product->id,
            'type' => $validated['type'],
            'quantity' => $validated['quantity'],
            'reason' => $validated['reason'],
            'new_cost_price' => $validated['new_cost_price'],
            'new_sale_price' => $validated['new_sale_price'],
        ]);

        // 2. Mettre à jour le stock du produit
        if ($validated['type'] === 'entry' || $validated['type'] === 'return') {
            // Entrée ou Retour = on ajoute au stock
            $product->increment('stock', $validated['quantity']);
        } elseif ($validated['type'] === 'loss' || $validated['type'] === 'sortie') {
            // Perte = on retire du stock
            if ($product->stock < $validated['quantity']) {
                return redirect()->back()->with('error', 'Stock insuffisant pour effectuer cette perte.');
            }
            $product->decrement('stock', $validated['quantity']);
        }

        // 3. Mettre à jour les prix si renseignés
        $updateData = [];
        if ($request->filled('new_cost_price')) {
            $updateData['cost_price'] = $validated['new_cost_price'];
        }
        if ($request->filled('new_sale_price')) {
            $updateData['price'] = $validated['new_sale_price'];
        }
        if (!empty($updateData)) {
            $product->update($updateData);
        }

        return redirect()->back()->with('success', 'Mouvement de stock appliqué avec succès !');
    }
}
