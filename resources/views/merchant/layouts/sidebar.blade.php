    @php
        $currentShopId = request('shop_id') ?? session('current_shop_id');

        // ⚠️ Sécurité : On vérifie que l'utilisateur est connecté avant d'accéder à ses shops
        $displayShops = collect(); // Collection vide par défaut

        if (auth()->check()) {
            if (!$currentShopId || $currentShopId === 'all') {
                $displayShops = auth()->user()->shops;
            } else {
                $displayShops = auth()->user()->shops->where('id', $currentShopId);
            }
        }
    @endphp
    <div class="px-4 py-6 h-full overflow-y-auto">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-2 mb-10">
            <div class="w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center text-white">
                <i class="fas fa-store"></i>
            </div>

            <div>
                <h2 class="text-xl font-bold text-gray-800">FatiHad</h2>
                <p class="text-xs text-gray-400">Merchant</p>
            </div>
        </div>

        {{-- COMMERCE --}}
        <h3 class="px-2 mt-8 mb-3 text-xs font-semibold tracking-widest uppercase text-gray-400">
            Commerce
        </h3>

        <nav class="space-y-1">

            <a href="{{ route('merchant.dashboard') }}{{ $currentShopId && $currentShopId !== 'all' ? '?shop_id='.$currentShopId : '' }}"
               class="flex items-center h-11 px-3 rounded-xl transition {{ request()->routeIs('merchant.dashboard') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600 hover:bg-gray-100' }}">
                <i data-lucide="layout-grid" class="w-5 h-5"></i>
                <span class="ml-3 text-[15px] font-medium">Dashboard</span>
            </a>

            <a href="{{ route('merchant.shops.index') }}"
               class="flex items-center h-11 px-3 rounded-xl transition {{ request()->routeIs('merchant.shops.index') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600 hover:bg-gray-100' }}">
                <i data-lucide="store" class="w-5 h-5"></i>
                <span class="ml-3 text-[15px] font-medium">Mes boutiques</span>
            </a>

            <a href="{{ route('subscription.index') }}"
               class="flex items-center h-11 px-3 rounded-xl transition {{ request()->routeIs('subscription.index') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600 hover:bg-gray-100' }}">
                <i data-lucide="credit-card" class="w-5 h-5"></i>
                <span class="ml-3 text-[15px] font-medium">Abonnements</span>
            </a>

            @php
                $hasCertification = auth()->check() && \App\Models\Certification::where('user_id', auth()->id())
                    ->where('status', 'active')
                    ->where('expires_at', '>', now())
                    ->exists();
            @endphp

            <a href="{{ $hasCertification ? route('certification.status') : route('certification.index') }}"
               class="flex items-center h-11 px-3 rounded-xl transition {{ request()->routeIs('certification.*') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600 hover:bg-gray-100' }}">
                <i data-lucide="shield-check" class="w-5 h-5"></i>
                <span class="ml-3 text-[15px] font-medium">
            {{ $hasCertification ? 'Ma Certification' : 'Certification' }}
        </span>
            </a>

            @php
                $hasMarketplace = auth()->check() && \App\Models\MarketplaceSubscription::where('user_id', auth()->id())
                    ->where('status', 'active')
                    ->where('expires_at', '>', now())
                    ->exists();
            @endphp

            <a href="{{ $hasMarketplace ? route('marketplace.status') : route('marketplace.index') }}"
               class="flex items-center h-11 px-3 rounded-xl transition {{ request()->routeIs('marketplace.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }}">
                <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                <span class="ml-3 text-[15px] font-medium">
            {{ $hasMarketplace ? ' Accès Marketplace' : 'Accès Marketplace' }}
        </span>
            </a>
        </nav>



        @if(auth()->check() && $displayShops->isNotEmpty())
            @foreach($displayShops as $shop)

            <div class="mt-6">

                <h3 class="px-2 mb-3 text-xs font-semibold tracking-widest uppercase text-gray-400">
                    {{ $shop->name }}
                </h3>

                <nav class="space-y-1">

                    <a href="{{ route('merchant.products.index',$shop) }}"
                       class="flex items-center h-11 px-3 rounded-xl transition {{ request()->routeIs('merchant.products.index') && request()->route('shop')?->id == $shop->id ?'bg-emerald-50 text-emerald-700' : 'text-gray-600 hover:bg-gray-100' }}">
                        <i data-lucide="package" class="w-5 h-5"></i>
                        <span class="ml-3 text-[15px]">Produits</span>
                    </a>

                    <a href="{{ route('merchant.categories.index',$shop) }}"
                       class="flex items-center h-11 px-3 rounded-xl transition {{ request()->routeIs('merchant.categories.*') && request()->route('shop')?->id == $shop->id ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600 hover:bg-gray-100' }}">
                        <i data-lucide="tags" class="w-5 h-5"></i>
                        <span class="ml-3 text-[15px]">Catégories</span>
                    </a>

                    <a href="{{ route('merchant.orders.index',$shop) }}"
                       class="flex items-center h-11 px-3 rounded-xl transition {{ request()->routeIs('merchant.orders.*') && request()->route('shop')?->id == $shop->id ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600 hover:bg-gray-100' }}">

                        <i data-lucide="clipboard-list" class="w-5 h-5"></i>

                        <span class="ml-3 text-[15px]">Commandes</span>

                        @php
                            $pendingCount = $shop->orders()->where('order_status','pending')->count();
                        @endphp

                        @if($pendingCount)
                            <span class="ml-auto h-5 min-w-[20px] px-1 rounded-full bg-red-500 text-white text-[11px] flex items-center justify-center">
                                {{ $pendingCount }}
                            </span>
                        @endif

                    </a>

                    @php $userPlan = auth()->user()->plan ?? 'free'; @endphp
                    <a href="{{ route('merchant.products.marketplace', $shop) }}"
                       class="flex items-center h-11 px-3 rounded-xl transition {{ request()->routeIs('merchant.products.marketplace') && request()->route('shop')?->id == $shop->id ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600 hover:bg-gray-100' }}">
                        <i data-lucide="share-2" class="w-5 h-5"></i>
                        <span class="ml-3 text-[15px]">Marketplace</span>
                        @if(!$hasMarketplace)
                            <i data-lucide="lock" class="w-4 h-4 ml-auto text-gray-400"></i>
                        @endif
                    </a>

                    <a href="{{ route('merchant.customers.index', $shop) }}"
                       class="flex items-center h-11 px-3 rounded-xl transition {{ request()->routeIs('merchant.customers.*') && request()->route('shop')?->id == $shop->id ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600 hover:bg-gray-100' }}">
                        <i data-lucide="users" class="w-5 h-5"></i>
                        <span class="ml-3 text-[15px]">Clients & CRM</span>
                        @if($userPlan === 'free')
                            <span class="ml-auto px-1.5 py-0.5 bg-emerald-500 text-white text-[10px] font-semibold rounded mr-1">Pro</span>
                            <i data-lucide="lock" class="w-4 h-4 text-gray-400"></i>
                        @endif
                    </a>

                    <a href="{{ route('merchant.carts.abandoned', $shop) }}"
                       class="flex items-center h-11 px-3 rounded-xl transition {{ request()->routeIs('merchant.carts.*') && request()->route('shop')?->id == $shop->id ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600 hover:bg-gray-100' }}">
                        <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                        <span class="ml-3 text-[15px]">Paniers oubliés</span>
                        @if($userPlan === 'free')
                            <span class="ml-auto px-1.5 py-0.5 bg-emerald-500 text-white text-[10px] font-semibold rounded mr-1">Pro</span>
                            <i data-lucide="lock" class="w-4 h-4 text-gray-400"></i>
                        @endif
                    </a>
                    <a href="{{ route('merchant.sales.index', $shop) }}"
                       class="flex items-center h-11 px-3 rounded-xl transition {{ request()->routeIs('merchant.sales.*') && request()->route('shop')?->id == $shop->id ?'bg-emerald-50 text-emerald-700' : 'text-gray-600 hover:bg-gray-100' }}">
                        <i data-lucide="trending-up" class="w-5 h-5"></i>
                        <span class="ml-3 text-[15px]">Ventes</span>
                        @if($userPlan === 'free')
                            <span class="ml-auto px-1.5 py-0.5 bg-emerald-500 text-white text-[10px] font-semibold rounded mr-1">Pro</span>
                            <i data-lucide="lock" class="w-4 h-4 text-gray-400"></i>
                        @endif
                    </a>

                    <a href="{{ route('merchant.stocks.index', $shop) }}"
                       class="flex items-center h-11 px-3 rounded-xl transition {{ request()->routeIs('merchant.stocks.*') && request()->route('shop')?->id == $shop->id ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600 hover:bg-gray-100' }}">
                        <i data-lucide="database" class="w-5 h-5"></i>
                        <span class="ml-3 text-[15px]">Stocks</span>
                        @if($userPlan === 'free')
                            <span class="ml-auto px-1.5 py-0.5 bg-emerald-500 text-white text-[10px] font-semibold rounded mr-1">Pro</span>
                            <i data-lucide="lock" class="w-4 h-4 text-gray-400"></i>
                        @endif
                    </a>

                    {{-- ➡️ AJOUTER ICI, après Stocks, avant Paiements --}}
                    <a href="{{ route('merchant.boost.index', $shop) }}"
                       class="flex items-center h-11 px-3 rounded-xl transition {{ request()->routeIs('merchant.boost.*') && request()->route('shop')?->id == $shop->id ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600 hover:bg-gray-100' }}">
                        <i data-lucide="rocket" class="w-5 h-5"></i>
                        <span class="ml-3 text-[15px]">Fatiha Ads</span>
                        @if($userPlan === 'free')
                            <span class="ml-auto px-1.5 py-0.5 bg-emerald-500 text-white text-[10px] font-semibold rounded mr-1">Pro</span>
                            <i data-lucide="lock" class="w-4 h-4 text-gray-400"></i>
                        @endif
                    </a>

                    <a href="{{ route('merchant.facebook.stats', $shop) }}"
                       class="flex items-center h-11 px-3 rounded-xl transition {{ request()->routeIs('merchant.facebook.stats') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600 hover:bg-gray-100' }}">
                        <i data-lucide="bar-chart-2" class="w-5 h-5"></i>
                        <span class="ml-3 text-[15px]">Stats Facebook</span>
                    </a>

                    {{-- ➡️ AJOUTER ICI --}}
                    <a href="{{ route('merchant.paiements.shop', $shop) }}"
                       class="flex items-center h-11 px-3 rounded-xl transition {{ request()->routeIs('merchant.paiements.*') && request()->route('shop')?->id == $shop->id ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600 hover:bg-gray-100' }}">
                        <i data-lucide="banknote" class="w-5 h-5"></i>
                        <span class="ml-3 text-[15px]">Paiements</span>
                        @if($userPlan === 'free')
                            <span class="ml-auto px-1.5 py-0.5 bg-emerald-500 text-white text-[10px] font-semibold rounded mr-1">Pro</span>
                            <i data-lucide="lock" class="w-4 h-4 text-gray-400"></i>
                        @endif
                    </a>

                    <a href="{{ route('merchant.cashout.index', $shop) }}"
                       class="flex items-center h-11 px-3 rounded-xl transition {{ request()->routeIs('merchant.cashout.*') && request()->route('shop')?->id == $shop->id ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600 hover:bg-gray-100' }}">
                        <i data-lucide="wallet" class="w-5 h-5"></i>
                        <span class="ml-3 text-[15px]">Mes retraits</span>
                    </a>

                    <a href="{{ route('merchant.shops.edit',$shop) }}"
                       class="flex items-center h-11 px-3 rounded-xl transition {{ request()->routeIs('merchant.shops.edit') && request()->route('shop')?->id == $shop->id ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600 hover:bg-gray-100' }}">
                        <i data-lucide="settings" class="w-5 h-5"></i>
                        <span class="ml-3 text-[15px]">Paramètres</span>
                    </a>

                </nav>

            </div>

            @endforeach
        @endif

    </div>
