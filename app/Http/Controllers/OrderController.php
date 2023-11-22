<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Auth;

class OrderController extends Controller 
{
    public function index(Request $request)
    {
        $acceptHeader = $request->header('Accept');

        if($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
            $orders = Order::Where(['users_id' => Auth::user()->id])->OrderBy("order_id", "DESC")->paginate(10);

            if ($acceptHeader === 'application/json') {
                return response()->json($orders->items('data'), 200);
            } else {
                $xml = new \SimpleXMLElement('<orders/>');
                foreach ($orders->items('data') as $item)
                {
                    $xmlItem = $xml->addChild('order');

                    $xmlItem->addChild('order_id', $item->order_id);
                    $xmlItem->addChild('customer_name', $item->customer_name);
                    $xmlItem->addChild('product_id', $item->product_id);
                    $xmlItem->addChild('total_amount', $item->total_amount);
                    $xmlItem->addChild('payment_status', $item->payment_status);
                    $xmlItem->addChild('order_date', $item->order_date);
                    $xmlItem->addChild('users_id', $item->users_id);
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
        //dd($request->header());

        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
            
            $contentTypeHeader = $request->header('Content-Type');

            if ($contentTypeHeader === 'application/json') {

                $input = $request->all();
                $order = Order::create($input);
            
                return response()->json(['data' => $order], 200);

            } elseif ($contentTypeHeader === 'application/xml') {
                $xmlInput = $request->getContent();
                $xml = simplexml_load_string($xmlInput);

                if($xml) {
                    $order = Order::create([
                        'customer_name' => $xml->customer_name,
                        'product_id' => $xml->product_id,
                        'total_amount' => $xml->total_amount,
                        'payment_status' => $xml->payment_status,
                        'order_date' => $xml->order_date,
                        'users_id' => $xml->users_id,
                    ]);

                    return response($order, 200)->header('Content-Type', 'application/xml');
                } else {
                    return response('XML cannot be parsed!', 400);
                }

            } else {
                return response('Unsupported Media Type', 415);
            }
        } else {
            return response('Not Acceptable!', 406);
        }
    }

    public function show($order_id, Request $request)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
            $order = Order::find($order_id);

            if ($acceptHeader === 'application/json') {
                
                if(!$order) {
                    abort(404);
                }

                return response()->json($order, 200);
            } else {
                $xml = new \SimpleXMLElement('<order/>');

                $xml->addChild('order_id', $order->order_id);
                $xml->addChild('customer_name', $order->customer_name);
                $xml->addChild('product_id', $order->product_id);
                $xml->addChild('total_amount', $order->total_amount);
                $xml->addChild('payment_status', $order->payment_status);
                $xml->addChild('order_date', $order->order_date);
                $xml->addChild('users_id', $order->users_id);
                $xml->addChild('created_at', $order->created_at);
                $xml->addChild('updated_at', $order->updated_at);

                return $xml->asXML();
            }
        } else {
            return response('Not Acceptable!', 406);
        }
    }

    public function update($order_id, Request $request)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
            
            $contentTypeHeader = $request->header('Content-Type');
            $input = $request->all();
            $order = Order::find($order_id);
            if ($contentTypeHeader === 'application/json') {

                if(!$order) {
                    abort(404);
                }

                $order->fill($input);
                $order->save();

                return response()->json($order, 200);

            } elseif ($contentTypeHeader === 'application/xml') {
                $xmlInput = $request->getContent();
                $xml = simplexml_load_string($xmlInput);

                if($xml) {
                    $data = [
                        'customer_name' => $xml->customer_name,
                        'product_id' => $xml->product_id,
                        'total_amount' => $xml->total_amount,
                        'payment_status' => $xml->payment_status,
                        'order_date' => $xml->order_date,
                        'users_id' => $xml->users_id,
                    ];
                    $order->update($data);

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

    public function destroy($order_id, Request $request)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
            $order = Order::find($order_id);

            if(!$order) {
                abort(404);
            }

            $order->delete();

            $message = ['message' => 'Deleted successfully', 'order_id' => $order_id];

            if ($acceptHeader === 'application/json') {
                return response()->json($message, 200);
            } else {
                $xml = new \SimpleXMLElement('<message/>');
                $xml->addChild('message', 'Deleted successfully');
                $xml->addChild('order_id', $order_id);

                return $xml->asXML();
            }
        } else {
            return response('Not Acceptable!', 406);
        }
    }
}