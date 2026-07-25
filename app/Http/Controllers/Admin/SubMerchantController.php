<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubMerchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SubMerchantController extends Controller
{
    private function api()
    {
        $apiKey = config('services.dexchange.api_key');
        $baseUrl = config('services.dexchange.api_url');
        $parsedUrl = parse_url($baseUrl);
        $baseApiUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];

        return [$apiKey, $baseApiUrl];
    }

    public function index()
    {
        [$apiKey, $baseApiUrl] = $this->api();

        try {
            $response = Http::timeout(30)
                ->withOptions(['verify' => false])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Accept' => 'application/json',
                ])->get($baseApiUrl . '/api/v1/sub-merchant');

            $data = $response->json();
            $subMerchants = $data['data'] ?? [];

            // Synchroniser avec la base locale
            foreach ($subMerchants as $sm) {
                // Ignorer les sous-marchands qui ne correspondent pas à celui du .env
                if ($sm['subMerchantId'] !== config('services.dexchange.sub_merchant_id')) {
                    continue;
                }

                SubMerchant::updateOrCreate(
                    ['sub_merchant_id' => $sm['subMerchantId']],
                    [
                        'name' => $sm['name'],
                        'commercial_name' => $sm['commercialName'],
                        'is_active' => $sm['isActive'] ?? true,
                        'is_default' => $sm['isDefault'] ?? false,
                        'data' => $sm,
                        'site' => 'Seneshop', // ← AJOUTER
                    ]
                );
            }

        } catch (\Exception $e) {
            Log::error('SubMerchant List Error:', ['message' => $e->getMessage()]);
        }

        $subMerchants = SubMerchant::latest()->get();

        return view('admin.sub-merchants.index', compact('subMerchants'));
    }

    public function create()
    {
        return view('admin.sub-merchants.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'commercial_name' => 'required|string',
            'site' => 'required|in:disso,caravane',
            'countries' => 'required|array',
        ]);

        [$apiKey, $baseApiUrl] = $this->api();

        try {
            $response = Http::timeout(30)
                ->withOptions(['verify' => false])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])->post($baseApiUrl . '/api/v1/sub-merchant', [
                    'name' => $request->name,
                    'commercialName' => $request->commercial_name,
                    'countries' => $request->countries,
                ]);

            $data = $response->json();

            if ($data['success'] ?? false) {
                SubMerchant::create([
                    'sub_merchant_id' => $data['data']['subMerchantId'],
                    'name' => $request->name,
                    'commercial_name' => $request->commercial_name,
                    'site' => $request->site,
                    'is_active' => true,
                    'data' => $data['data'],
                ]);

                return redirect()->route('admin.sub-merchants.index')
                    ->with('success', 'Sous-marchand créé avec succès. ID: ' . $data['data']['subMerchantId']);
            }

            return back()->with('error', $data['message'] ?? 'Erreur lors de la création.');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function edit(SubMerchant $subMerchant)
    {
        return view('admin.sub-merchants.edit', compact('subMerchant'));
    }

    public function update(Request $request, SubMerchant $subMerchant)
    {
        [$apiKey, $baseApiUrl] = $this->api();

        $payload = [];
        if ($request->commercial_name) $payload['commercialName'] = $request->commercial_name;
        if ($request->countries) $payload['countries'] = $request->countries;

        try {
            $response = Http::timeout(30)
                ->withOptions(['verify' => false])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])->patch($baseApiUrl . '/api/v1/sub-merchant/' . $subMerchant->sub_merchant_id, $payload);

            $data = $response->json();

            if ($data['success'] ?? false) {
                $subMerchant->update([
                    'commercial_name' => $request->commercial_name ?? $subMerchant->commercial_name,
                    'site' => $request->site ?? $subMerchant->site,
                ]);

                return redirect()->route('admin.sub-merchants.index')
                    ->with('success', 'Sous-marchand mis à jour.');
            }

            return back()->with('error', $data['message'] ?? 'Erreur.');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

}
