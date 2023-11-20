<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function index(Request $request)
    {
        $acceptHeader = $request->header('Accept');

        if($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
            $products = Product::OrderBy("product_id", "DESC")->paginate(10);

            if ($acceptHeader === 'application/json') {
                return response()->json($products->items('data'), 200);
            } else {
                $xml = new \SimpleXMLElement('<products/>');
                foreach ($products->items('data') as $item)
                {
                    $xmlItem = $xml->addChild('product');

                    $xmlItem->addChild('product_id', $item->product_id);
                    $xmlItem->addChild('product_name', $item->product_name);
                    $xmlItem->addChild('desctiprion', $item->description);
                    $xmlItem->addChild('price', $item->price);
                    $xmlItem->addChild('stock', $item->stock);
                    $xmlItem->addChild('production_date', $item->production_date);
                    $xmlItem->addChild('expiry_date', $item->expiry_date);
                    $xmlItem->addChild('manufacturer', $item->manufacturer);
                    $xmlItem->addChild('category_id', $item->category_id);
                    $xmlItem->addChild('status', $item->status);
                    $xmlItem->addChild('created_at', $item->created_at);
                    $xmlItem->addChild('updated_at', $item->updated_at);
                }
                return $xml->asXML();
            }
        } else {
            return response('Not Acceptable!', 406);
        }
    }
    public function store(Request $request)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
            
            $contentTypeHeader = $request->header('Content-Type');

            if ($contentTypeHeader === 'application/json') {
                $input = $request->all();
                $product = Product::create($input);

                return response()->json($product, 200);
            } elseif ($contentTypeHeader === 'application/xml') {
                $xmlInput = $request->getContent(); // Get raw XML content
                $xml = simplexml_load_string($xmlInput);
                
                if ($xml) {
                    $data = [
                        'product_name' => (string) $xml->product_name,
                        'description' => (string) $xml->description,
                        'price' => (float) $xml->price,
                        'stock' => (int) $xml->stock,
                        'production_date' => (string) $xml->production_date,
                        'expiry_date' => (string) $xml->expiry_date,
                        'manufacturer' => (string) $xml->manufacturer,
                        'category_id' => (int) $xml->category_id,
                        'status' => (string) $xml->status,
                    ];

                    $product = Product::create($data);

                    return response($xmlInput, 200)->header('Content-Type', 'application/xml');
                } else {
                    return response('Invalid XML', 400);
                }
            } else {
                return response('Unsupported Media Type', 415);
            }
        } else {
            return response('Not Acceptable!', 406);
        }
    }
    public function show(Request $request, $product_id)
    {
        $acceptHeader = $request->header('Accept');

        if($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
            $product = Product::find($product_id);

            if ($acceptHeader === 'application/json') {
                if(!$product) {
                    abort(404);
                }
                return response()->json($product, 200);
            } else {
                $xml = new \SimpleXMLElement('<products/>');
                $item = $product;
                
                $xmlItem = $xml->addChild('product');
                $xmlItem->addChild('product_id', $item->product_id);
                $xmlItem->addChild('product_name', $item->product_name);
                $xmlItem->addChild('desctiprion', $item->description);
                $xmlItem->addChild('price', $item->price);
                $xmlItem->addChild('stock', $item->stock);
                $xmlItem->addChild('production_date', $item->production_date);
                $xmlItem->addChild('expiry_date', $item->expiry_date);
                $xmlItem->addChild('manufacturer', $item->manufacturer);
                $xmlItem->addChild('category_id', $item->category_id);
                $xmlItem->addChild('status', $item->status);
                $xmlItem->addChild('created_at', $item->created_at);
                $xmlItem->addChild('updated_at', $item->updated_at);
                
                return $xml->asXML();
            }
        } else {
            return response('Not Acceptable!', 406);
        }
    }
    public function update(Request $request, $product_id)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
            
            $contentTypeHeader = $request->header('Content-Type');
            $input = $request->all();
            $product = Product::find($product_id);
            if ($contentTypeHeader === 'application/json') {

                if(!$product) {
                    abort(404);
                }
                $product->fill($input);
                $product->save();
                return response()->json($product, 200);
            } elseif ($contentTypeHeader === 'application/xml') {
                $xmlInput = $request->getContent(); // Get raw XML content
                $xml = simplexml_load_string($xmlInput);
                
                if ($xml) {
                    $data = [
                        'product_name' => (string) $xml->product_name,
                        'description' => (string) $xml->description,
                        'price' => (float) $xml->price,
                        'stock' => (int) $xml->stock,
                        'production_date' => (string) $xml->production_date,
                        'expiry_date' => (string) $xml->expiry_date,
                        'manufacturer' => (string) $xml->manufacturer,
                        'category_id' => (int) $xml->category_id,
                        'status' => (string) $xml->status,
                    ];

                    $product->update($data);

                    return response($xmlInput, 200)->header('Content-Type', 'application/xml');
                } else {
                    return response('Invalid XML', 400);
                }
            } else {
                return response('Unsupported Media Type', 415);
            }
        } else {
            return response('Not Acceptable!', 406);
        }
    }
    public function destroy(Request $request, $product_id)
    {
        $product = Product::find($product_id);

        if (!$product) {
            return response('Product not found', 404);
        }

        $product->delete();

        return response('Product deleted', 200);
    }
}
