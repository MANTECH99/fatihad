<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ShopController as AdminShopController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\SubMerchantController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\BoostController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\StorefrontController;
use Illuminate\Support\Facades\Route;


Route::post('/shop/{shop:slug}/save-phone', [App\Http\Controllers\StorefrontController::class, 'savePhone'])->name('storefront.save-phone');

// ========== MARKETPLACE PUBLIQUE ==========
Route::prefix('marketplace/public')->name('marketplace.public.')->group(function () {
    Route::get('/', [App\Http\Controllers\MarketplacePublicController::class, 'index'])->name('home');
    Route::get('/marketplace/produits', [App\Http\Controllers\MarketplacePublicController::class, 'allProducts'])->name('all-products');
    Route::get('/marketplace/promotions', [App\Http\Controllers\MarketplacePublicController::class, 'promotions'])->name('promotions');
    Route::get('/marketplace/nouveautes', [App\Http\Controllers\MarketplacePublicController::class, 'nouveautes'])->name('nouveautes');
    Route::get('/marketplace/boutiques', [App\Http\Controllers\MarketplacePublicController::class, 'allShops'])->name('shops');
    Route::get('/marketplace/vendre', [App\Http\Controllers\MarketplacePublicController::class, 'vendre'])->name('vendre');
    Route::get('/marketplace/contact', [App\Http\Controllers\MarketplacePublicController::class, 'contact'])->name('contact');
});

use App\Models\Shop;

Route::get('/storefront/{shop:slug}/manifest.json', function (Shop $shop) {
    $logoUrl = $shop->logo_url;

    if ($logoUrl && !str_starts_with($logoUrl, 'http')) {
        $logoUrl = asset('storage/' . $logoUrl);
    }

    $icons = $logoUrl
        ? [['src' => $logoUrl, 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any maskable'], ['src' => $logoUrl, 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any maskable']]
        : [['src' => '/images/icons/icon-192x192.png', 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any maskable'], ['src' => '/images/icons/icon-512x512.png', 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any maskable']];

    $shopUrl = route('storefront.show', $shop->slug);

    return response()->json([
        'name' => $shop->name,
        'short_name' => $shop->name,
        'description' => $shop->description ?? 'Commandez facilement sur ' . $shop->name,
        'start_url' => $shopUrl,
        'display' => 'standalone',
        'background_color' => '#ffffff',
        'theme_color' => '#10B981',
        'orientation' => 'portrait-primary',
        'lang' => 'fr',
        'id' => '/shop/' . $shop->slug . '/',
        'scope' => $shopUrl,
        'icons' => $icons
    ]);
})->name('storefront.manifest');


// Dans routes/web.php (en dehors des groupes, accessible publiquement)
Route::get('/feed-facebook/{shop:slug}', [App\Http\Controllers\FacebookCatalogController::class, 'feed'])
    ->name('facebook.catalog.feed');


Route::post('/shop/{shop:slug}/review', [App\Http\Controllers\ReviewController::class, 'store'])->name('review.store');
// Remplace par :
Route::prefix('cart/{shop:slug}')->group(function () {
    Route::post('/add', [StorefrontController::class, 'addToCart'])->name('cart.add');
    Route::post('/update', [StorefrontController::class, 'updateCart'])->name('cart.update');
    Route::post('/remove', [StorefrontController::class, 'removeFromCart'])->name('cart.remove');
    Route::get('/', [StorefrontController::class, 'getCart'])->name('cart.get');
});
// Routes publiques - Boutique
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/shop/{shop:slug}', [StorefrontController::class, 'show'])->name('storefront.show');
Route::get('/shop/{shop:slug}/checkout', [StorefrontController::class, 'checkout'])->name('storefront.checkout');
Route::post('/shop/{shop:slug}/order', [StorefrontController::class, 'placeOrder'])->name('storefront.order');
Route::get('/shop/{shop:slug}/order/{order:order_number}/confirmation', [StorefrontController::class, 'orderConfirmation'])->name('storefront.order.confirmation');
Route::get('/shop/{shop:slug}/product/{product:slug}', [StorefrontController::class, 'product'])->name('storefront.product');



// Routes authentifiées
Route::middleware('auth')->group(function () {

    // Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Espace Commerçant
    Route::prefix('merchant')->name('merchant.')->middleware(['check.trial'])->group(function () {
        Route::get('shops/{shop}/cashout', [App\Http\Controllers\MerchantCashoutController::class, 'index'])
            ->name('cashout.index');
        Route::get('shops/{shop}/facebook-stats', [App\Http\Controllers\FacebookController::class, 'stats'])
            ->name('facebook.stats');
        Route::get('shops/{shop}/abandoned-carts', [App\Http\Controllers\CartReminderController::class, 'index'])->name('carts.abandoned');


        Route::get('shops/{shop}/customers/export', [CustomerController::class, 'export'])->name('customers.export');
        Route::get('shops/{shop}/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
        Route::get('shops/{shop}/customers', [CustomerController::class, 'index'])->name('customers.index');

        Route::get('shops/{shop}/whatsapp/create', [BoostController::class, 'createWhatsAppForm'])->name('boost.whatsapp.create');
        Route::post('shops/{shop}/whatsapp', [BoostController::class, 'createWhatsApp'])->name('boost.whatsapp');
        Route::get('shops/{shop}/promote', [BoostController::class, 'promoteSaas'])->name('boost.promote');
        Route::post('shops/{shop}/promote', [BoostController::class, 'storePromoteSaas'])->name('boost.promote.store');

        Route::post('shops/{shop}/boost/{campaign}/duplicate', [BoostController::class, 'duplicate'])
            ->name('boost.duplicate');
        Route::post('shops/{shop}/boost/{campaign}/launch', [BoostController::class, 'launch'])
            ->name('boost.launch');

        Route::get('shops/{shop}/boost/{campaign}/edit', [BoostController::class, 'edit'])->name('boost.edit');
        Route::put('shops/{shop}/boost/{campaign}', [BoostController::class, 'update'])->name('boost.update');

        Route::post('shops/{shop}/retargeting', [BoostController::class, 'createRetargeting'])
            ->name('boost.retargeting');

        Route::get('shops/{shop}/retargeting/create', [BoostController::class, 'createRetargetingForm'])
            ->name('boost.retargeting.create');


        // Dans routes/web.php, dans le groupe merchant

// Boost Facebook
        Route::get('shops/{shop}/boost', [BoostController::class, 'index'])->name('boost.index');
        Route::get('shops/{shop}/products/{product}/boost', [BoostController::class, 'create'])->name('boost.create');
        Route::post('shops/{shop}/products/{product}/boost', [BoostController::class, 'store'])->name('boost.store');
        Route::post('shops/{shop}/boost/{campaign}/pause', [BoostController::class, 'pause'])->name('boost.pause');
        Route::post('shops/{shop}/boost/{campaign}/resume', [BoostController::class, 'resume'])->name('boost.resume');
        Route::post('shops/{shop}/boost/{campaign}/sync', [BoostController::class, 'syncStats'])->name('boost.sync');


// Dans routes/web.php, modifier les routes Facebook :
        Route::get('facebook/connect', [App\Http\Controllers\FacebookController::class, 'connect'])
            ->name('facebook.connect');
        Route::get('facebook/callback', [App\Http\Controllers\FacebookController::class, 'callback'])
            ->name('facebook.callback');
        Route::post('facebook/disconnect', [App\Http\Controllers\FacebookController::class, 'disconnect'])
            ->name('facebook.disconnect');

        Route::get('/shops/{shop}/stocks', [App\Http\Controllers\ProductController::class, 'stockIndex'])->name('stocks.index');
        Route::post('/shops/{shop}/stocks/movement', [App\Http\Controllers\ProductController::class, 'stockMovement'])->name('products.stock-movement');


        // ✅ GESTION MARKETPLACE DU MARCHAND
        Route::get('shops/{shop}/marketplace', [App\Http\Controllers\ProductController::class, 'marketplaceIndex'])->name('products.marketplace');
        Route::put('shops/{shop}/marketplace', [App\Http\Controllers\ProductController::class, 'updateMarketplace'])->name('products.updateMarketplace');


        Route::get('shops/{shop}/sales', [App\Http\Controllers\SaleController::class, 'index'])->name('sales.index');
        Route::post('shops/{shop}/sales', [App\Http\Controllers\SaleController::class, 'store'])->name('sales.store');
        Route::delete('shops/{shop}/sales/{sale}', [App\Http\Controllers\SaleController::class, 'destroy'])->name('sales.destroy');

        Route::get('shops/{shop}/orders/{order}/invoice', [App\Http\Controllers\OrderController::class, 'invoice'])->name('orders.invoice');

// Dans routes/web.php, remplace la route dashboard par :
        Route::get('/dashboard', [App\Http\Controllers\MerchantDashboardController::class, 'index'])->name('dashboard');

        Route::get('shops/{shop}/paiements', [App\Http\Controllers\PaymentHistoryController::class, 'index'])->name('paiements.shop');

        // Boutiques
        Route::resource('shops', ShopController::class);
        Route::post('shops/{shop}/toggle-status', [ShopController::class, 'toggleStatus'])
            ->name('shops.toggle-status');

        // Catégories
        Route::get('shops/{shop}/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('shops/{shop}/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('shops/{shop}/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('shops/{shop}/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        Route::post('shops/{shop}/categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');

        // Produits
        Route::get('shops/{shop}/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('shops/{shop}/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('shops/{shop}/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('shops/{shop}/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('shops/{shop}/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('shops/{shop}/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::post('shops/{shop}/products/{product}/toggle', [ProductController::class, 'toggleAvailability'])->name('products.toggle');
        Route::delete('shops/{shop}/products/{product}/gallery/{index}', [ProductController::class, 'removeGalleryImage'])->name('products.gallery.remove');

        // Commandes
        Route::get('shops/{shop}/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('shops/{shop}/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('shops/{shop}/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::post('shops/{shop}/orders/{order}/payment', [OrderController::class, 'updatePayment'])->name('orders.update-payment');
        Route::delete('shops/{shop}/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
        Route::get('shops/{shop}/orders/export', [OrderController::class, 'export'])->name('orders.export');

    });

    // Administration
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {

        Route::get('/subscriptions', [AdminDashboardController::class, 'subscriptions'])
            ->name('subscriptions.index');
        Route::post('/subscriptions/{subscription}/cancel', [AdminDashboardController::class, 'cancelSubscription'])
            ->name('subscriptions.cancel');


        // Sous-marchands
        Route::get('/sub-merchants', [SubMerchantController::class, 'index'])->name('sub-merchants.index');
        Route::get('/sub-merchants/create', [SubMerchantController::class, 'create'])->name('sub-merchants.create');
        Route::post('/sub-merchants', [SubMerchantController::class, 'store'])->name('sub-merchants.store');
        Route::get('/sub-merchants/{subMerchant}/edit', [SubMerchantController::class, 'edit'])->name('sub-merchants.edit');
        Route::put('/sub-merchants/{subMerchant}', [SubMerchantController::class, 'update'])->name('sub-merchants.update');
        Route::post('/sub-merchants/{subMerchant}/toggle', [SubMerchantController::class, 'toggle'])->name('sub-merchants.toggle');

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Gestion des boutiques
        Route::get('/shops', [AdminShopController::class, 'index'])->name('shops.index');
        Route::get('/shops/{shop}', [AdminShopController::class, 'show'])->name('shops.show');
        Route::post('/shops/{shop}/approve', [AdminShopController::class, 'approve'])->name('shops.approve');
        Route::post('/shops/{shop}/reject', [AdminShopController::class, 'reject'])->name('shops.reject');
        Route::post('/shops/{shop}/toggle-active', [AdminShopController::class, 'toggleActive'])->name('shops.toggle-active');
        Route::delete('/shops/{shop}', [AdminShopController::class, 'destroy'])->name('shops.destroy');

        // Gestion des commandes
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');

        // Gestion des utilisateurs
        Route::resource('users', AdminUserController::class)->except(['create', 'edit']);
        Route::post('/users/{user}/toggle-active', [AdminUserController::class, 'toggleActive'])->name('users.toggle-active');

        // Paramètres
        Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings/general', [AdminSettingsController::class, 'updateGeneral'])->name('settings.general');
    });
});


// Paiement Dexpay Africa
Route::prefix('payment')->name('payment.')->group(function () {
    Route::get('/init/{shop:slug}/{order:order_number}', [App\Http\Controllers\PaymentController::class, 'init'])->name('init');
    Route::post('/callback/{order:order_number}', [App\Http\Controllers\PaymentController::class, 'callback'])->name('callback');
    Route::get('/success/{order:order_number}', [App\Http\Controllers\PaymentController::class, 'success'])->name('success');
    Route::get('/failure/{order:order_number}', [App\Http\Controllers\PaymentController::class, 'failure'])->name('failure');
    Route::get('/check-status/{order:order_number}', [App\Http\Controllers\PaymentController::class, 'checkStatus'])->name('check-status');
});

// Webhook global Dexpay (point d'entrée unique pour tous les événements)
Route::post('/webhooks/dexpay', [App\Http\Controllers\PaymentController::class, 'webhook'])->name('webhooks.dexpay');
// routes/web.php
Route::get('/2fa/verify', [App\Http\Controllers\TwoFactorController::class, 'showVerifyForm'])->name('2fa.verify');
Route::post('/2fa/verify', [App\Http\Controllers\TwoFactorController::class, 'verify']);
Route::get('/2fa/setup', [App\Http\Controllers\TwoFactorController::class, 'setup'])->name('2fa.setup')->middleware('auth');

Route::middleware(['auth', 'admin', '2fa'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/cashout', [App\Http\Controllers\Admin\CashoutController::class, 'index'])->name('cashout.index');
    Route::post('/cashout', [App\Http\Controllers\Admin\CashoutController::class, 'initiate'])->name('cashout.initiate');
    Route::get('/payouts', [App\Http\Controllers\Admin\CashoutController::class, 'payouts'])->name('payouts.index');
});

// Callback hors middleware
Route::post('/admin/cashout/callback', [App\Http\Controllers\Admin\CashoutController::class, 'callback'])->name('admin.cashout.callback');


Route::post('/2fa/check-password', function (\Illuminate\Http\Request $request) {
    if ($request->password === 'Mantech772607977@') {
        return response()->json(['success' => true]);
    }
    return response()->json(['success' => false]);
})->name('2fa.check-password');


Route::prefix('subscription')->name('subscription.')->group(function () {
    Route::get('/', [App\Http\Controllers\SubscriptionController::class, 'index'])->name('index');
    Route::post('/subscribe', [App\Http\Controllers\SubscriptionController::class, 'subscribe'])->name('subscribe');
    Route::post('/cancel', [App\Http\Controllers\SubscriptionController::class, 'cancel'])->name('cancel');
    Route::get('/payment/{plan}', [App\Http\Controllers\SubscriptionController::class, 'showPayment'])->name('payment');
    Route::get('/pay', [App\Http\Controllers\SubscriptionController::class, 'pay'])->name('pay');
    Route::get('/pending/{externalId}', [App\Http\Controllers\SubscriptionController::class, 'pending'])->name('pending'); // ➡️ AJOUTÉ
    Route::get('/check-status/{externalId}', [App\Http\Controllers\SubscriptionController::class, 'checkStatus'])->name('check-status'); // ➡️ AJOUTÉ
    Route::post('/callback/{externalId}', [App\Http\Controllers\SubscriptionController::class, 'paymentCallback'])->name('callback');
});

// Certification
Route::prefix('certification')->name('certification.')->group(function () {
    Route::get('/', [App\Http\Controllers\CertificationController::class, 'index'])->name('index');
    Route::get('/status', [App\Http\Controllers\CertificationController::class, 'status'])->name('status');
    Route::post('/pay', [App\Http\Controllers\CertificationController::class, 'pay'])->name('pay');
    Route::get('/pending/{externalId}', [App\Http\Controllers\CertificationController::class, 'pending'])->name('pending');
    Route::get('/check-status/{externalId}', [App\Http\Controllers\CertificationController::class, 'checkStatus'])->name('check-status');
});

Route::post('/certification/callback/{externalId}', [App\Http\Controllers\CertificationController::class, 'callback'])->name('certification.callback');

Route::prefix('marketplace')->name('marketplace.')->middleware(['auth', 'check.trial'])->group(function () {
    Route::get('/', [App\Http\Controllers\MarketplaceController::class, 'index'])->name('index');
    Route::get('/status', [App\Http\Controllers\MarketplaceController::class, 'status'])->name('status');
    Route::post('/pay', [App\Http\Controllers\MarketplaceController::class, 'pay'])->name('pay');
    Route::get('/pending/{externalId}', [App\Http\Controllers\MarketplaceController::class, 'pending'])->name('pending');
    Route::get('/check-status/{externalId}', [App\Http\Controllers\MarketplaceController::class, 'checkStatus'])->name('check-status');
});

Route::post('/marketplace/callback/{externalId}', [App\Http\Controllers\MarketplaceController::class, 'callback'])->name('marketplace.callback');

Route::get('/privacy', function () {
    return view('pages.privacy');
})->name('privacy');

Route::get('/delete-data', function () {
    return view('pages.delete-data');
})->name('delete-data');

Route::get('/conditions-generales', function () {
    return view('pages.conditions-generales');
})->name('conditions-generales');


Route::get('/test-whatsapp', function () {
    $shop = \App\Models\Shop::first(); // Plus simple, prend le premier shop

    $campaign = \App\Models\FacebookCampaign::create([
        'shop_id' => $shop->id,
        'product_id' => $shop->products()->first()->id ?? 1,
        'name' => 'Test WhatsApp ' . now()->format('H:i'),
        'campaign_type' => 'traffic',
        'daily_budget' => 1,
        'total_budget' => 3,
        'duration_days' => 3,
        'whatsapp_number' => $shop->whatsapp_phone,
        'whatsapp_message' => 'Bonjour, test !',
        'status' => 'pending',
        'ends_at' => now()->addDays(3),
    ]);

    try {
        $adsService = new \App\Services\FacebookAdsService(
            $shop->facebook_access_token,
            'act_' . $shop->facebook_ad_account_id,
            $shop->facebook_page_id
        );
        $result = $adsService->createWhatsAppCampaign($campaign);
        dd($result);
    } catch (\Exception $e) {
        dd($e->getMessage());
    }
});


require __DIR__.'/auth.php';
