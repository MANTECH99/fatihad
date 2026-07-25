<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;

class FacebookCatalogController extends Controller
{
    public function feed(Shop $shop)
    {
        $products = $shop->products()
            ->where('is_available', true)
            ->get();

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><rss xmlns:g="http://base.google.com/ns/1.0" version="2.0"/>');
        $channel = $xml->addChild('channel');
        $channel->addChild('title', htmlspecialchars($shop->name));
        $channel->addChild('link', route('storefront.show', $shop->slug));
        $channel->addChild('description', htmlspecialchars($shop->description ?? ''));

        foreach ($products as $product) {
            $item = $channel->addChild('item');
            $item->addChild('g:id', $product->id);
            $item->addChild('g:title', htmlspecialchars($product->name));
            $item->addChild('g:description', htmlspecialchars($product->description ?? ''));
            $item->addChild('g:link', route('storefront.product', ['shop' => $shop->slug, 'product' => $product]));
            $item->addChild('g:image_link', $product->image_url ?? 'https://placehold.co/800x800?text=Produit');
            $item->addChild('g:price', number_format($product->current_price, 0, '.', '') . ' XOF');
            $item->addChild('g:availability', $product->is_available && (!$product->track_inventory || $product->stock > 0) ? 'in stock' : 'out of stock');
            $item->addChild('g:condition', 'new');
            $item->addChild('g:brand', htmlspecialchars($shop->name));
        }

        return response($xml->asXML(), 200)
            ->header('Content-Type', 'application/xml');
    }
}
