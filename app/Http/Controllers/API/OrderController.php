<?php

namespace App\Http\Controllers\API;

use App\Models\Item;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function store(Request $request){
        $validation = $request->validate([
            'customer_name' => 'required|max:100',
            'table_numb' => 'required|max:5',
        ]);

        try{
            DB::beginTransaction();

                $data = $request->only(['customer_name', 'table_numb']);
                $data['order_date'] = date('Y-m-d H:s:i');
                // $data['order_time'] = date('H:s:i');
                $data['status'] = 'ordered';
                $data['total_price'] = '10000';
                $data['waitress_id'] = auth()->user()->id;
                $data['items'] = $request->items;

                $order = Order::create($data);

                //menggunakan foreach
                // foreach ($data['items'] as $item) {
                //     $itemOrder = Item::where('id', $item)->first();
                //     if ($itemOrder) {
                //         $orderDetail = OrderDetail::create([
                //             'order_id' => $order->id,
                //             'item_id' => $itemOrder->id,
                //             'price' => $itemOrder->price
                //         ]);
                //     }
                // }

                //menggunkan collect map
                collect($data['items'])->map(function($item) use($order) {
                    $itemOrder = Item::find($item);
                    $orderDetail = OrderDetail::create([
                        'order_id' => $order->id,
                        'item_id' => $itemOrder->id,
                        'price' => $itemOrder->price
                    ]);
                });

            DB::commit();
        }catch(\Throwable $th){
            DB::rollback();
            return response($th);
        }

        return response([
            'success' => true,
            'message' => 'Order Berhasil disimpan!',
            'data' => $order,
        ]);
    }
}
