{{-- resources/views/storefront/checkout.blade.php --}}
@extends('layouts.storefront')

@section('title', 'Commander - ' . $shop->name)

@section('content')

    @if($shop->facebook_pixel_id && count($cartItems) > 0)
        <script>
            fbq('track', 'AddToCart', {
                content_ids: [{{ collect($cartItems)->pluck('id')->implode(',') }}],
                content_type: 'product',
                value: {{ $subtotal }},
                currency: 'XOF'
            });
        </script>
    @endif
    <div class="max-w-2xl mx-auto px-4 py-6">
        <!-- Retour -->
        <a href="{{ route('storefront.show', $shop->slug) }}" class="text-gray-600 hover:text-gray-900 mb-4 inline-block">
            <i class="fas fa-arrow-left mr-2"></i> Retour à la boutique
        </a>

        <h1 class="text-2xl font-bold text-gray-900 mb-6">Votre commande</h1>

        @if(count($cartItems) == 0)
            <div class="bg-white rounded-lg shadow mb-6 p-8 text-center">
                <i class="fas fa-shopping-cart text-6xl text-gray-300 mb-4"></i>
                <h2 class="text-xl font-bold text-gray-700 mb-2">Votre panier est vide</h2>
                <p class="text-gray-500 mb-4">Ajoutez des produits pour commencer votre commande.</p>
                <a href="{{ route('storefront.show', $shop->slug) }}" class="inline-block bg-emerald-500 text-white px-6 py-3 rounded-lg font-bold hover:bg-emerald-600 transition">
                    <i class="fas fa-store mr-2"></i> Voir la boutique
                </a>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                @if(is_array(session('error')))
                    @foreach(session('error') as $err)
                        {{ $err }}<br>
                    @endforeach
                @else
                    {{ session('error') }}
                @endif
            </div>
        @endif



        <form action="{{ route('storefront.order', $shop->slug) }}" method="POST" id="checkout-form">
            @csrf

            <!-- Panier -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-4 py-3 border-b">
                    <h2 class="font-semibold">Articles</h2>
                </div>

                <div class="divide-y">
                    @foreach($cartItems as $item)
                        <div class="px-4 py-3">
                            <div class="flex space-x-4">
                                <!-- Image produit -->
                                @if($item->image_url)
                                    <img src="{{ $item->image_url }}" alt="{{ $item->name }}"
                                         class="w-20 h-20 object-contain rounded-lg flex-shrink-0 bg-gray-50">
                                @endif

                                <!-- Infos produit -->
                                <div class="flex-1">
                                    <span class="font-medium text-sm">{{ $item->name }}</span>

                                    @if($item->cart_options)
                                        <p class="text-xs text-gray-500 mt-1">
                                            @foreach($item->cart_options as $key => $value)
                                                {{ $key }}: {{ $value }}@if(!$loop->last), @endif
                                            @endforeach
                                        </p>
                                    @endif

                                    <div class="mt-2 text-sm">
                            <span class="font-bold text-emerald-600">
                                {{ number_format($item->current_price * $item->cart_quantity, 0, ',', ' ') }} FCFA
                            </span>
                                        @if($item->cart_quantity > 1)
                                            <span class="text-gray-400 text-xs ml-2">
                                    ({{ number_format($item->current_price, 0, ',', ' ') }} FCFA/unité)
                                </span>
                                        @endif
                                    </div>

                                    <!-- Quantité + Supprimer -->
                                    <div class="flex items-center justify-between mt-3">
                                        <div class="inline-flex items-center border-2 border-gray-300 rounded-lg" data-cart-key="{{ $item->cart_key }}">
                                            <button type="button"
                                                    onclick="updateQuantity('{{ $item->cart_key }}', {{ $item->cart_quantity - 1 }})"
                                                    class="minus-btn w-8 h-8 flex items-center justify-center text-gray-600 hover:text-emerald-500 rounded-l-lg">
                                                -
                                            </button>
                                            <span class="quantity-display px-3 text-center text-sm font-medium border-x-2 border-gray-300 py-1">{{ $item->cart_quantity }}</span>
                                            <button type="button"
                                                    onclick="updateQuantity('{{ $item->cart_key }}', {{ $item->cart_quantity + 1 }})"
                                                    class="plus-btn w-8 h-8 flex items-center justify-center text-emerald-500 hover:text-emerald-600 rounded-r-lg">
                                                +
                                            </button>
                                        </div>

                                        <button type="button" onclick="removeItem('{{ $item->cart_key }}')"
                                                class="text-gray-400 hover:text-red-500">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            {{-- Zone de livraison --}}
            @if(!empty($shop->delivery_zones))
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="px-4 py-3 border-b">
                        <h2 class="font-semibold">Zone de livraison</h2>
                    </div>
                    <div class="p-4">
                        <select name="delivery_zone" id="delivery_zone" required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                                onchange="updateDeliveryFee(); this.setCustomValidity('')"
                                oninvalid="this.setCustomValidity('Veuillez choisir votre zone de livraison')">
                            <option value="">Choisir votre zone</option>
                            @foreach($shop->delivery_zones as $zone)
                                <option value="{{ $zone['name'] }}" data-price="{{ $zone['price'] }}">
                                    {{ $zone['name'] }} - {{ number_format($zone['price'], 0, ',', ' ') }} FCFA
                                </option>
                            @endforeach
                        </select>
                        <p id="zone-error" class="text-red-500 text-sm mt-1 hidden">Veuillez choisir votre zone de livraison</p>
                    </div>
                </div>
            @endif
            <!-- Résumé -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-4 py-3 border-b">
                    <h2 class="font-semibold">Résumé</h2>
                </div>

                <div class="px-4 py-3 space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Sous-total</span>
                        <span>{{ number_format($subtotal, 0, ',', ' ') }} FCFA</span>
                    </div>

                    {{-- Par : --}}
                    <div class="flex justify-between" id="delivery-fee-row">
                        <span class="text-gray-600">Frais de livraison</span>
                        <span id="delivery-fee-display">
        @if($shop->delivery_fee > 0)
                                {{ number_format($shop->delivery_fee, 0, ',', ' ') }} FCFA
                            @else
                                <span class="text-green-600">Gratuit</span>
                            @endif
    </span>
                    </div>

                    <div class="flex justify-between text-sm text-gray-500">
                        <span>Frais paiement mobile (Wave/OM)</span>
                        <span id="payment-fee-display">{{ number_format($estimatedPaymentFee, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <p class="text-xs text-gray-400">Applicable uniquement pour Wave et Orange Money</p>

                    <div class="border-t pt-2 flex justify-between font-bold text-lg">
                        <span>Total</span>
                        <span class="text-emerald-600" id="total-display">{{ number_format($total, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <input type="hidden" name="total" id="total-hidden" value="{{ $total }}">
                </div>
            </div>



            <!-- Informations client -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-4 py-3 border-b">
                    <h2 class="font-semibold">Vos informations</h2>
                </div>

                <div class="p-4 space-y-4">
                    <div>
                        <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">
                            Nom complet *
                        </label>
                        <input type="text" name="customer_name" id="customer_name" required
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                               placeholder="Votre nom" value="{{ old('customer_name') }}">
                        @error('customer_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-1">
                            Téléphone *
                        </label>
                        <input type="tel" name="customer_phone" id="customer_phone" required
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                               placeholder="+221 77 123 45 67" value="{{ old('customer_phone') }}">
                        <p class="text-xs text-gray-500 mt-1">Format: +221 XX XXX XX XX</p>
                        @error('customer_phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-1">
                            Email (optionnel)
                        </label>
                        <input type="email" name="customer_email" id="customer_email"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                               placeholder="votre@email.com" value="{{ old('customer_email') }}">
                    </div>

                    <div>
                        <label for="customer_address" class="block text-sm font-medium text-gray-700 mb-1">
                            Adresse de livraison *
                        </label>
                        <textarea name="customer_address" id="customer_address" required rows="2"
                                  class="w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                                  placeholder="Indiquez votre adresse complète">{{ old('customer_address') }}</textarea>
                        @error('customer_address')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="customer_note" class="block text-sm font-medium text-gray-700 mb-1">
                            Note (optionnel)
                        </label>
                        <textarea name="customer_note" id="customer_note" rows="2"
                                  class="w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                                  placeholder="Instructions spéciales...">{{ old('customer_note') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Paiement -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-4 py-3 border-b">
                    <h2 class="font-semibold">Mode de paiement</h2>
                </div>

                <div class="p-4 space-y-3">
                    <label class="flex items-center p-3 border rounded-md cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="payment_method" value="cash_on_delivery" checked
                               class="text-emerald-500 focus:ring-emerald-500">
                        <div class="ml-3">
                            <span class="font-medium">Paiement à la livraison</span>
                            <p class="text-sm text-gray-500">Payez en espèces à la réception</p>
                        </div>
                    </label>

                    <label class="flex items-center p-3 border rounded-md cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="payment_method" value="wave"
                               class="text-emerald-500 focus:ring-emerald-500">
                        <div class="ml-3">
                            <span class="font-medium">Wave</span>
                            <p class="text-sm text-gray-500">Payez via Wave</p>
                        </div>
                    </label>

                    <label class="flex items-center p-3 border rounded-md cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="payment_method" value="orange_money"
                               class="text-emerald-500 focus:ring-emerald-500">
                        <div class="ml-3">
                            <span class="font-medium">Orange Money</span>
                            <p class="text-sm text-gray-500">Payez via Orange Money</p>
                        </div>
                    </label>
                </div>
            </div>
            <input type="hidden" name="delivery_fee_amount" id="delivery-fee-amount" value="{{ $shop->delivery_fee }}">
            <!-- Validation -->
            <button type="submit" class="w-full bg-emerald-500 text-white py-4 rounded-lg font-bold text-lg hover:bg-emerald-600 transition shadow-lg">
                <i class="fas fa-check-circle mr-2"></i> Confirmer la commande
            </button>

            <p class="text-center text-sm text-gray-500 mt-4">
                En confirmant, vous acceptez d'être contacté pour la livraison.
            </p>
        </form>
    </div>
    <!-- Modal Suppression -->
    <div id="deleteModal" style="display:none; position:fixed; inset:0; z-index:9999; align-items:center; justify-content:center; background:rgba(0,0,0,0.4); backdrop-filter:blur(2px);">
        <div style="background:white; border-radius:20px; width:320px; overflow:hidden; box-shadow:0 25px 50px rgba(0,0,0,0.15); animation:popIn 0.25s ease-out;">
            <div style="background:#fef2f2; padding:24px; text-align:center;">
                <div style="width:56px; height:56px; background:white; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 12px; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
                    <i class="fas fa-trash-alt" style="color:#ef4444; font-size:22px;"></i>
                </div>
                <h3 style="font-size:17px; font-weight:700; color:#1f2937; margin:0;">Supprimer l'article ?</h3>
                <p style="font-size:13px; color:#9ca3af; margin:4px 0 0;">Cette action est irréversible.</p>
            </div>
            <div style="display:flex; border-top:1px solid #f3f4f6;">
                <button onclick="closeModal()" style="flex:1; padding:14px; background:white; border:none; color:#6b7280; font-weight:600; font-size:14px; cursor:pointer;">Annuler</button>
                <button id="confirmBtn" style="flex:1; padding:14px; background:#10b981; border:none; color:white; font-weight:600; font-size:14px; cursor:pointer;">Supprimer</button>
            </div>
        </div>
    </div>

    <style>@keyframes popIn{from{opacity:0;transform:scale(0.92)}to{opacity:1;transform:scale(1)}}</style>

    <script>




        let itemKey = null;
        function removeItem(key) { itemKey = key; document.getElementById('deleteModal').style.display = 'flex'; }
        function closeModal() { itemKey = null; document.getElementById('deleteModal').style.display = 'none'; resetModal(); }

        document.getElementById('confirmBtn').onclick = async () => {
            if (!itemKey) return;

            const btn = document.getElementById('confirmBtn');
            const annuler = document.querySelector('#deleteModal button');
            const icon = document.querySelector('#deleteModal .fa-trash-alt');
            const title = document.querySelector('#deleteModal h3');
            const text = document.querySelector('#deleteModal p');
            const box = document.querySelector('#deleteModal div div:first-child');

            // Désactiver les boutons
            btn.disabled = true;
            btn.style.opacity = '0.7';
            annuler.disabled = true;
            annuler.style.opacity = '0.7';

            // Animation suppression
            icon.className = 'fas fa-spinner fa-spin';
            icon.style.color = '#10b981';
            title.textContent = 'Suppression...';
            text.textContent = 'Veuillez patienter';
            box.style.background = '#ecfdf5';

            await fetch(CART_REMOVE_URL, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: new URLSearchParams({ key: itemKey }) });
            location.reload();
        };

        function resetModal() {
            document.getElementById('confirmBtn').disabled = false;
            document.getElementById('confirmBtn').style.opacity = '1';
            document.querySelector('#deleteModal button').disabled = false;
            document.querySelector('#deleteModal button').style.opacity = '1';
            document.querySelector('#deleteModal .fa-spinner').className = 'fas fa-trash-alt';
            document.querySelector('#deleteModal .fa-trash-alt').style.color = '#ef4444';
            document.querySelector('#deleteModal h3').textContent = "Supprimer l'article ?";
            document.querySelector('#deleteModal p').textContent = 'Cette action est irréversible.';
            document.querySelector('#deleteModal div div:first-child').style.background = '#fef2f2';
        }
    </script>
    <script>
        const defaultDeliveryFee = {{ $shop->delivery_fee }};
        const subtotal = {{ $subtotal }};
        const estimatedPaymentFee = {{ $estimatedPaymentFee }};

        function updateDeliveryFee() {
            const select = document.getElementById('delivery_zone');
            const selectedOption = select.options[select.selectedIndex];
            const price = selectedOption.getAttribute('data-price');
            const display = document.getElementById('delivery-fee-display');
            const hiddenInput = document.getElementById('delivery-fee-amount');

            if (price) {
                display.textContent = new Intl.NumberFormat('fr-FR').format(price) + ' FCFA';
                hiddenInput.value = price;
            } else {
                display.innerHTML = defaultDeliveryFee > 0
                    ? new Intl.NumberFormat('fr-FR').format(defaultDeliveryFee) + ' FCFA'
                    : '<span class="text-green-600">Gratuit</span>';
                hiddenInput.value = defaultDeliveryFee;
            }

            updateTotal();
        }

        function updateTotal() {
            const deliveryFee = parseInt(document.getElementById('delivery-fee-amount').value) || 0;
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
            const isMobile = paymentMethod === 'wave' || paymentMethod === 'orange_money';
            const paymentFee = isMobile ? Math.round((subtotal + deliveryFee) * 0.03046) : 0;
            const total = subtotal + deliveryFee + paymentFee;

            document.getElementById('total-display').textContent = new Intl.NumberFormat('fr-FR').format(total) + ' FCFA';
            document.getElementById('total-hidden').value = total;
            document.getElementById('payment-fee-display').textContent = new Intl.NumberFormat('fr-FR').format(paymentFee) + ' FCFA';
        }

        // Écouter le changement de mode de paiement
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', updateTotal);
        });


        document.getElementById('checkout-form').addEventListener('submit', function(e) {
            const zoneSelect = document.getElementById('delivery_zone');
            if (zoneSelect && !zoneSelect.value) {
                e.preventDefault();
                document.getElementById('zone-error').classList.remove('hidden');
                zoneSelect.classList.add('border-red-500');
                zoneSelect.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });

        async function updateQuantity(key, quantity) {
            if (quantity <= 0) {
                removeItem(key);
                return;
            }

            // Trouver le conteneur de quantité correspondant
            const container = document.querySelector(`[data-cart-key="${key}"]`);
            const displaySpan = container.querySelector('.quantity-display');
            const minusBtn = container.querySelector('.minus-btn');
            const plusBtn = container.querySelector('.plus-btn');

            // Désactiver les boutons et afficher le loader
            minusBtn.disabled = true;
            plusBtn.disabled = true;
            displaySpan.innerHTML = '<i class="fas fa-spinner fa-spin text-emerald-500"></i>';

            const resp = await fetch('{{ route("cart.update", $shop->slug) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    items: [{ key: key, quantity: quantity }]
                })
            });

            const data = await resp.json();

            if (data.success) {
                location.reload();
            } else {
                // Réactiver si erreur
                minusBtn.disabled = false;
                plusBtn.disabled = false;
                displaySpan.textContent = quantity;
            }
        }
    </script>
    {{--
    <script>
        // Sauvegarder le téléphone en session dès qu'il est modifié (pour paniers abandonnés)
        document.getElementById('customer_phone').addEventListener('blur', function() {
            const phone = this.value;
            if (phone.length >= 9) {
                fetch('{{ route('storefront.save-phone', $shop->slug) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        phone: phone,
                        name: document.getElementById('customer_name').value
                    })
                });
            }
        });
    </script>
    --}}
@endsection
