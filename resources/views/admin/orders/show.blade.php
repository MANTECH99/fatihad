{{-- resources/views/admin/orders/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Commande ' . $order->order_number)
@section('header', 'Commande ' . $order->order_number)

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne principale -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Articles -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-semibold text-lg mb-4">Articles commandés</h3>

                <div class="divide-y">
                    @foreach($order->items as $item)
                        <div class="py-3 flex justify-between">
                            <div>
                                <span class="bg-gray-100 px-2 py-0.5 rounded text-sm mr-2">{{ $item->quantity }}x</span>
                                <span class="font-medium">{{ $item->product_name }}</span>
                                @if($item->options && is_array($item->options))
                                    <p class="text-sm text-gray-500 mt-0.5">
                                        @foreach($item->options as $key => $value)
                                            {{ $key }}: {{ $value }}@if(!$loop->last), @endif
                                        @endforeach
                                    </p>
                                @endif
                                @if($item->special_instructions)
                                    <p class="text-xs text-orange-600 mt-0.5">📝 {{ $item->special_instructions }}</p>
                                @endif
                            </div>
                            <div class="text-right">
                                <span class="font-medium">{{ number_format($item->subtotal, 0, ',', ' ') }} FCFA</span>
                                <br>
                                <span class="text-xs text-gray-500">{{ number_format($item->price, 0, ',', ' ') }} FCFA / unité</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="border-t mt-4 pt-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Sous-total</span>
                        <span>{{ number_format($order->subtotal, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Frais de livraison</span>
                        <span>{{ $order->delivery_fee > 0 ? number_format($order->delivery_fee, 0, ',', ' ') . ' FCFA' : 'Gratuit' }}</span>
                    </div>
                    @if($order->discount > 0)
                        <div class="flex justify-between text-sm text-red-600">
                            <span>Remise</span>
                            <span>-{{ number_format($order->discount, 0, ',', ' ') }} FCFA</span>
                        </div>
                    @endif
                    <div class="flex justify-between font-bold text-lg pt-2 border-t">
                        <span>Total</span>
                        <span class="text-indigo-600">{{ number_format($order->total, 0, ',', ' ') }} FCFA</span>
                    </div>
                </div>
            </div>

            <!-- Historique des statuts -->
            @if($order->status_history && is_array($order->status_history))
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="font-semibold text-lg mb-4">Historique des statuts</h3>
                    <div class="space-y-4">
                        @foreach($order->status_history as $history)
                            <div class="flex items-start">
                                <div class="w-3 h-3 rounded-full bg-indigo-500 mt-1.5 mr-3"></div>
                                <div>
                                    <p class="text-sm font-medium">{{ $history['note'] ?? 'Statut mis à jour' }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($history['timestamp'])->format('d/m/Y à H:i') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Colonne latérale -->
        <div class="space-y-6">
            <!-- Statut actuel -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-semibold mb-4">Statut de la commande</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-500">Statut</p>
                        <span class="inline-block mt-1 px-3 py-1 text-sm rounded-full bg-{{ $order->getStatusColor() }}-100 text-{{ $order->getStatusColor() }}-800 font-medium">
                        {{ $order->getStatusLabel() }}
                    </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Paiement</p>
                        <span class="inline-block mt-1 px-3 py-1 text-sm rounded-full
                        {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' :
                           ($order->payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }} font-medium">
                        {{ $order->payment_status === 'paid' ? 'Payé' : ($order->payment_status === 'pending' ? 'En attente' : $order->payment_status) }}
                    </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Méthode de paiement</p>
                        <p class="font-medium">{{ $order->getPaymentMethodLabel() }}</p>
                    </div>
                    @if($order->payment_transaction_id)
                        <div>
                            <p class="text-sm text-gray-500">Transaction</p>
                            <p class="text-xs font-mono">{{ $order->payment_transaction_id }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Client -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-semibold mb-4">Client</h3>
                <div class="space-y-2">
                    <p class="font-medium">{{ $order->customer_name }}</p>
                    <p class="text-sm">
                        <a href="tel:{{ $order->customer_phone }}" class="text-indigo-600 hover:underline">
                            <i class="fas fa-phone mr-1"></i> {{ $order->customer_phone }}
                        </a>
                    </p>
                    @if($order->customer_phone)
                        <p class="text-sm">
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer_phone) }}"
                               target="_blank" class="text-green-600 hover:underline">
                                <i class="fab fa-whatsapp mr-1"></i> WhatsApp
                            </a>
                        </p>
                    @endif
                    @if($order->customer_email)
                        <p class="text-sm">{{ $order->customer_email }}</p>
                    @endif
                </div>
            </div>

            <!-- Livraison -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-semibold mb-4">Livraison</h3>
                <div class="space-y-2">
                    <p class="text-sm"><strong>Adresse :</strong></p>
                    <p class="text-sm">{{ $order->customer_address }}</p>

                    @if($order->delivery_person)
                        <div class="mt-3 pt-3 border-t">
                            <p class="text-sm"><strong>Livreur :</strong> {{ $order->delivery_person }}</p>
                            @if($order->delivery_person_phone)
                                <p class="text-sm"><strong>Tél livreur :</strong> {{ $order->delivery_person_phone }}</p>
                            @endif
                        </div>
                    @endif

                    @if($order->customer_note)
                        <div class="mt-3 pt-3 border-t">
                            <p class="text-sm"><strong>Note client :</strong></p>
                            <p class="text-sm text-orange-600">{{ $order->customer_note }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Boutique -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-semibold mb-4">Boutique</h3>
                <div class="flex items-center space-x-3">
                    <img src="{{ $order->shop->logo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($order->shop->name) . '&background=10b981&color=fff' }}"
                         class="w-10 h-10 rounded-full">
                    <div>
                        <p class="font-medium">{{ $order->shop->name }}</p>
                        <a href="{{ route('admin.shops.show', $order->shop) }}" class="text-sm text-indigo-600 hover:underline">
                            Voir la boutique
                        </a>
                    </div>
                </div>
            </div>

            <!-- Notification WhatsApp -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-semibold mb-4">Notification</h3>
                <p class="text-sm">
                    <strong>WhatsApp envoyé :</strong>
                    @if($order->whatsapp_notification_sent)
                        <span class="text-green-600">✅ Oui</span>
                        <br>
                        <span class="text-xs text-gray-500">
                        Le {{ $order->whatsapp_notified_at?->format('d/m/Y à H:i') }}
                    </span>
                    @else
                        <span class="text-yellow-600">❌ Non</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Bouton retour -->
    <div class="mt-6">
        <a href="{{ route('admin.orders.index') }}" class="text-indigo-600 hover:text-indigo-800">
            <i class="fas fa-arrow-left mr-2"></i> Retour à la liste
        </a>
    </div>
@endsection
