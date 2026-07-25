{{-- resources/views/admin/layouts/sidebar.blade.php --}}
<div class="p-6">
    <div class="flex items-center space-x-2 mb-8">
        <i class="fas fa-shield-alt text-2xl text-white"></i>
        <span class="text-xl font-bold text-white">Seneshop Admin</span>
    </div>

    <nav class="space-y-1">
        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-800' : 'hover:bg-indigo-800' }}">
            <i class="fas fa-tachometer-alt w-5"></i>
            <span class="ml-3">Tableau de bord</span>
        </a>

        <a href="{{ route('admin.subscriptions.index') }}"
           class="flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.subscriptions.index') ? 'bg-indigo-800' : 'hover:bg-indigo-800' }}">
            <i class="fas fa-credit-card w-5"></i>
            <span class="ml-3">Abonnements</span>
        </a>



        <a href="{{ route('admin.shops.index') }}"
           class="flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.shops.*') ? 'bg-indigo-800' : 'hover:bg-indigo-800' }}">
            <i class="fas fa-store w-5"></i>
            <span class="ml-3">Boutiques</span>
        </a>

        <a href="{{ route('admin.orders.index') }}"
           class="flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.orders.*') ? 'bg-indigo-800' : 'hover:bg-indigo-800' }}">
            <i class="fas fa-shopping-cart w-5"></i>
            <span class="ml-3">Commandes</span>
        </a>

        <a href="{{ route('admin.users.index') }}"
           class="flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.users.*') ? 'bg-indigo-800' : 'hover:bg-indigo-800' }}">
            <i class="fas fa-users w-5"></i>
            <span class="ml-3">Utilisateurs</span>
        </a>

        <a href="{{ route('admin.cashout.index') }}"
           class="flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.cashout.*')  ? 'bg-indigo-800' : 'hover:bg-indigo-800' }}">
            <span class="mr-3 text-lg">💰</span> Cashout
        </a>


        <a href="{{ route('admin.settings.index') }}"
           class="flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.settings.*') ? 'bg-indigo-800' : 'hover:bg-indigo-800' }}">
            <i class="fas fa-cog w-5"></i>
            <span class="ml-3">Paramètres</span>
        </a>
    </nav>
</div>
