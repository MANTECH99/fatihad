{{-- resources/views/merchant/orders/show.blade.php --}}
@extends('merchant.layouts.app')

@section('title', 'Commande ' . $order->order_number)
@section('header', 'Commande ' . $order->order_number)

@section('content')
    <div x-data="orderDetail({{ $order->id }})">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonne principale -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Statut -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="font-semibold mb-4">Statut de la commande</h2>

                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach(['pending', 'confirmed', 'preparing', 'ready', 'out_for_delivery', 'delivered'] as $status)
                            @php
                                $colors = [
                                    'pending' => 'bg-yellow-500',
                                    'confirmed' => 'bg-blue-500',
                                    'preparing' => 'bg-indigo-500',
                                    'ready' => 'bg-green-500',
                                    'out_for_delivery' => 'bg-orange-500',
                                    'delivered' => 'bg-emerald-500',
                                ];
                                $isCurrent = $order->order_status === $status;
                            @endphp
                            <button @click="updateStatus('{{ $status }}')"
                                    class="px-3 py-2 rounded-md text-sm transition text-white {{ $isCurrent ? $colors[$status] : 'bg-gray-300 text-gray-700 hover:bg-gray-400' }}">
                                {{ app(\App\Models\Order::class)->getStatusLabel($status) }}
                            </button>
                        @endforeach
                    </div>

                    <div class="flex gap-2">
                        <button @click="updateStatus('cancelled')"
                                class="px-3 py-2 bg-red-100 text-red-700 rounded-md text-sm hover:bg-red-200">
                            <i class="fas fa-times mr-1"></i> Annuler
                        </button>
                    </div>

                    <template x-if="['out_for_delivery'].includes(currentStatus) || orderStatus === 'out_for_delivery'">
                        <div class="mt-4 p-3 bg-gray-50 rounded-md space-y-2">
                            <input type="text" x-model="deliveryPerson" placeholder="Nom du livreur"
                                   class="w-full border-gray-300 rounded-md text-sm">
                            <input type="text" x-model="deliveryPhone" placeholder="Téléphone du livreur"
                                   class="w-full border-gray-300 rounded-md text-sm">
                        </div>
                    </template>
                </div>

                <!-- Articles -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="font-semibold mb-4">Articles commandés</h2>

                    <div class="divide-y">
                        @foreach($order->items as $item)
                            <div class="py-3 flex justify-between">
                                <div>
                                    <span class="bg-gray-100 px-2 py-0.5 rounded text-sm mr-2">{{ $item->quantity }}x</span>
                                    <span class="font-medium">{{ $item->product_name }}</span>
                                    @if($item->options)
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
                                <span class="font-medium">{{ number_format($item->subtotal, 0, ',', ' ') }} FCFA</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t mt-4 pt-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Sous-total</span>
                            <span>{{ number_format($order->subtotal, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Livraison</span>
                            <span>{{ $order->delivery_fee > 0 ? number_format($order->delivery_fee, 0, ',', ' ') . ' FCFA' : 'Gratuit' }}</span>
                        </div>
                        <div class="flex justify-between font-bold text-lg">
                            <span>Total</span>
                            <span class="text-emerald-600">{{ number_format($order->total, 0, ',', ' ') }} FCFA</span>
                        </div>
                    </div>
                </div>

                <!-- Historique -->
                @if($order->status_history)
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="font-semibold mb-4">Historique</h2>
                        <div class="space-y-3">
                            @foreach($order->status_history as $history)
                                <div class="flex items-start">
                                    <div class="w-2 h-2 rounded-full bg-emerald-500 mt-2 mr-3"></div>
                                    <div>
                                        <p class="text-sm">{{ $history['note'] ?? '' }}</p>
                                        <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($history['timestamp'])->format('d/m/Y à H:i') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Colonne latérale -->
            <div class="space-y-6">
                <!-- Infos client -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="font-semibold mb-4">Client</h2>
                    <div class="space-y-2 text-sm">
                        <p><strong>{{ $order->customer_name }}</strong></p>
                        <p>
                            <a href="tel:{{ $order->customer_phone }}" class="text-blue-600">
                                <i class="fas fa-phone mr-1"></i> {{ $order->customer_phone }}
                            </a>
                        </p>
                        @if($order->customer_phone)
                            <p>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer_phone) }}"
                                   target="_blank" class="text-green-600">
                                    <i class="fab fa-whatsapp mr-1"></i> WhatsApp
                                </a>
                            </p>
                        @endif
                        @if($order->customer_email)
                            <p>{{ $order->customer_email }}</p>
                        @endif
                    </div>
                </div>

                <!-- Adresse -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="font-semibold mb-4">Livraison</h2>
                    <p class="text-sm">{{ $order->customer_address }}</p>
                    @if($order->customer_note)
                        <div class="mt-3 p-2 bg-yellow-50 rounded text-sm">
                            <strong>Note :</strong> {{ $order->customer_note }}
                        </div>
                    @endif
                </div>

                <!-- Paiement -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="font-semibold mb-4">Paiement</h2>
                    <p class="text-sm"><strong>Méthode :</strong> {{ $order->getPaymentMethodLabel() }}</p>
                    <p class="text-sm mt-1">
                        <strong>Statut :</strong>
                        @if($order->payment_status === 'paid')
                            <span class="text-green-600">Payé</span>
                        @elseif($order->payment_status === 'pending')
                            <span class="text-yellow-600">En attente</span>
                        @else
                            <span class="text-red-600">{{ $order->payment_status }}</span>
                        @endif
                    </p>

                    @if($order->payment_status !== 'paid')
                        <button @click="markAsPaid()"
                                class="mt-3 w-full bg-green-500 text-white py-2 rounded-md text-sm hover:bg-green-600"
                                :disabled="paymentStatus === 'paid'">
                            <i class="fas fa-check mr-1"></i> Marquer comme payé
                        </button>
                    @endif
                    {{-- Bouton facture --}}
                    <a href="{{ route('merchant.orders.invoice', ['shop' => $shop, 'order' => $order]) }}"
                       target="_blank"
                       class="mt-3 w-full bg-gray-100 text-gray-700 py-2 rounded-md text-sm hover:bg-gray-200 flex items-center justify-center">
                        <i class="fas fa-file-invoice mr-1"></i> Télécharger la facture
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function orderDetail(orderId) {
            return {
                currentStatus: '{{ $order->order_status }}',
                orderStatus: '{{ $order->order_status }}',
                paymentStatus: '{{ $order->payment_status }}',
                deliveryPerson: '{{ $order->delivery_person }}',
                deliveryPhone: '{{ $order->delivery_person_phone }}',

                async updateStatus(status) {
                    if (!confirm('Changer le statut en "' + status + '" ?')) return;

                    try {
                        const response = await fetch('{{ route('merchant.orders.update-status', ['shop' => $shop, 'order' => $order]) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({
                                order_status: status,
                                delivery_person: this.deliveryPerson,
                                delivery_person_phone: this.deliveryPhone,
                            })
                        });

                        const data = await response.json();
                        if (data.success) {
                            this.currentStatus = status;
                            this.orderStatus = status;
                            location.reload();
                        }
                    } catch (error) {
                        alert('Erreur lors de la mise à jour');
                    }
                },

                async markAsPaid() {
                    if (!confirm('Confirmer le paiement ?')) return;

                    try {
                        const response = await fetch('{{ route('merchant.orders.update-payment', ['shop' => $shop, 'order' => $order]) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({ payment_status: 'paid' })
                        });

                        const data = await response.json();
                        if (data.success) {
                            this.paymentStatus = 'paid';
                            location.reload();
                        }
                    } catch (error) {
                        alert('Erreur lors de la mise à jour');
                    }
                }
            }
        }
    </script>
@endpush
