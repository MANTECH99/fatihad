<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FacebookService
{
    protected $accessToken;
    protected $pageId;

    public function __construct($accessToken, $pageId)
    {
        $this->accessToken = $accessToken;
        $this->pageId = $pageId;
    }

    public function publishProduct($product, $shop)
    {
        $message = $this->buildProductMessage($product, $shop);
        $productUrl = route('storefront.product', [
            'shop' => $shop->slug,
            'product' => $product
        ]);

        if ($product->image_url) {
            return $this->publishWithPhotoFile($message, $product, $productUrl);
        }

        return $this->publishWithLink($message, $productUrl);
    }

    /**
     * Publier avec le fichier image directement (plus fiable)
     */
    public function publishWithPhotoFile($message, $product, $link)
    {
        // Récupérer le chemin du fichier
        $imagePath = $product->image; // ex: shops/1/products/xxx.webp

        if (!Storage::disk('public')->exists($imagePath)) {
            Log::error('Image not found: ' . $imagePath);
            return $this->publishWithLink($message, $link);
        }

        $fullPath = Storage::disk('public')->path($imagePath);

        $response = Http::attach(
            'source', // nom du champ pour Facebook
            file_get_contents($fullPath),
            basename($imagePath)
        )->post("https://graph.facebook.com/v18.0/{$this->pageId}/photos", [
            'caption' => $message . "\n\n🛒 " . $link,
            'access_token' => $this->accessToken,
        ]);

        Log::info('Facebook publish result', $response->json());
        return $response->json();
    }

    public function publishWithPhoto($message, $imageUrl, $link)
    {
        $response = Http::timeout(60)->post("https://graph.facebook.com/v18.0/{$this->pageId}/photos", [
            'url' => $imageUrl,
            'caption' => $message . "\n\n🛒 " . $link,
            'access_token' => $this->accessToken,
        ]);

        Log::info('Facebook publish photo', $response->json());
        return $response->json();
    }

    public function publishWithLink($message, $link)
    {
        $response = Http::post("https://graph.facebook.com/v18.0/{$this->pageId}/feed", [
            'message' => $message,
            'link' => $link,
            'access_token' => $this->accessToken,
        ]);

        Log::info('Facebook publish link', $response->json());
        return $response->json();
    }

    protected function buildProductMessage($product, $shop)
    {
        $message = "🛍️ {$shop->name} - Nouveau produit !\n\n";
        $message .= "📌 {$product->name}\n";

        if ($product->hasDiscount()) {
            $message .= "💰 {$product->sale_price} FCFA (au lieu de {$product->price} FCFA, -{$product->discount_percentage}%)\n";
        } else {
            $message .= "💰 {$product->price} FCFA\n";
        }

        if ($product->description) {
            $message .= "\n📝 " . strip_tags($product->description);
        }

        return $message;
    }


    /**
     * Récupérer les statistiques d'une page Facebook
     */
    public function getPageStats($pageId)
    {
        $response = Http::get(
            "https://graph.facebook.com/v18.0/{$pageId}",
            [
                'fields' => 'fan_count,engagement,name,category',
                'access_token' => $this->accessToken,
            ]
        );

        $data = $response->json();

        return [
            'name' => $data['name'] ?? 'Inconnue',
            'category' => $data['category'] ?? '',
            'followers' => $data['fan_count'] ?? 0,
            'engagement' => $data['engagement']['count'] ?? 0,
            'new_likes' => 0,
            'link' => '',
        ];
    }

    /**
     * Récupérer les statistiques d'engagement d'un post Facebook
     */
    public function getPostEngagement($facebookPostId)
    {
        $response = Http::get(
            "https://graph.facebook.com/v18.0/{$facebookPostId}",
            [
                'fields' => 'likes.summary(true),comments.summary(true),shares',
                'access_token' => $this->accessToken,
            ]
        );

        $data = $response->json();

        \Log::info('Post engagement response', ['post_id' => $facebookPostId, 'data' => $data]);

        return [
            'likes' => $data['likes']['summary']['total_count'] ?? 0,
            'comments' => $data['comments']['summary']['total_count'] ?? 0,
            'shares' => $data['shares']['count'] ?? 0,
            'impressions' => 0,
            'engaged_users' => 0,
        ];
    }
}
