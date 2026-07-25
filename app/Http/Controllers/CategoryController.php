<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Shop;
use App\Services\ImageService;
use Illuminate\Http\Request;

class CategoryController extends Controller
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

        $categories = $shop->categories()
            ->withCount('products')
            ->orderBy('order')
            ->get();

        return view('merchant.categories.index', compact('shop', 'categories'));
    }

    public function store(Request $request, Shop $shop)
    {
        $this->authorize('update', $shop);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        $category = new Category($validated);
        $category->shop_id = $shop->id;
        $category->order = $shop->categories()->max('order') + 1;

        if ($request->hasFile('image')) {
            $category->image = $this->imageService->uploadAndOptimize(
                $request->file('image'), 'shops/' . $shop->id . '/categories', 400
            );
        }

        $category->save();

        return redirect()->back()->with('success', 'Catégorie ajoutée avec succès !');
    }

    public function update(Request $request, Shop $shop, Category $category)
    {
        $this->authorize('update', $shop);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            $this->imageService->delete($category->image);
            $validated['image'] = $this->imageService->uploadAndOptimize(
                $request->file('image'), 'shops/' . $shop->id . '/categories', 400
            );
        }

        $category->update($validated);

        return redirect()->back()->with('success', 'Catégorie mise à jour avec succès !');
    }

    public function destroy(Shop $shop, Category $category)
    {
        $this->authorize('update', $shop);

        // Détacher les produits de cette catégorie
        $category->products()->update(['category_id' => null]);

        $this->imageService->delete($category->image);
        $category->delete();

        return redirect()->back()->with('success', 'Catégorie supprimée avec succès.');
    }

    public function reorder(Request $request, Shop $shop)
    {
        $this->authorize('update', $shop);

        foreach ($request->orders as $order) {
            Category::where('id', $order['id'])
                ->where('shop_id', $shop->id)
                ->update(['order' => $order['order']]);
        }

        return response()->json(['success' => true]);
    }
}
