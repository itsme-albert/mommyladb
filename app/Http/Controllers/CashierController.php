<?php

namespace App\Http\Controllers;

use App\Models\Tables;
use App\Models\Orders;
use App\Models\OrderDetails;
use App\Models\Products;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashierController extends Controller
{
    public function dine()
    {
        $tables = DB::table('tables')
            ->select('tables.*')
            ->get();
    
        $orders = [];
    
        foreach ($tables as $table) {
            $tableOrders = DB::table('orders')
                ->where('table_no', $table->id)
                ->where('status', '!=', 'Payed')
                ->orderBy('orders.id', 'desc')
                ->first();
    
            if ($tableOrders) {
                $orderDetails = DB::table('order_details')
                    ->join('products', 'order_details.product_id', '=', 'products.id')
                    ->select(
                        'order_details.order_id as order_id',
                        'order_details.id as orderdetails_id',
                        'order_details.quantity',
                        'order_details.price as od_price',
                        'products.product_name',
                        'products.price as product_price'
                    )
                    ->where('order_details.order_id', $tableOrders->id)
                    ->get();
    
                $orders[] = [
                    'table_no' => $table->id,
                    'capacity' => $table->capacity,
                    'location' => $table->location,
                    'orderid' => $tableOrders->id,
                    'total' => $orderDetails->sum('od_price'),
                    'status' => $tableOrders->status,
                    'orders' => $orderDetails
                ];
            } else {
                $orders[] = [
                    'table_no' => $table->id,
                    'capacity' => $table->capacity,
                    'location' => $table->location,
                    'orders' => []
                ];
            }
        }
    
        return $orders;
    }

    public function online()
    {
        $users = DB::table('users')
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->join('payments', 'orders.id', '=', 'payments.order_id')
            ->select('users.*', 'payments.filename as filename', 'orders.status as status')
            ->where('users.role', '=', 'Customer')
            ->get();

        $orders = [];
        foreach ($users as $user) {
            $userOrders = DB::table('orders')
                ->join('users', 'orders.user_id', '=', 'users.id')
                ->join('order_details', 'orders.id', '=', 'order_details.order_id')
                ->join('products', 'order_details.product_id', '=', 'products.id')
                ->select(
                    'orders.id as order_id',
                    'order_details.id as order_details_id',
                    'order_details.quantity',
                    'order_details.price as order_details_price',
                    'products.product_name',
                    'products.price as product_price'
                )
                ->where('orders.user_id', $user->id)
                ->where('orders.status', '!=', 'Delivered')
                ->orderBy('orders.id', 'desc')
                ->get();

                $grouped = $userOrders->groupBy('order_id')->first();

            if (!is_null($userOrders) && count($userOrders) > 0) {
                $orders[] = [
                    'user_id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'address' => $user->address,
                    'phone' => $user->phone,
                    'payment' => $user->filename,
                    'status' => $user->status,
                    'total' => $grouped->sum('order_details_price'),
                    'orders' => $grouped
                ];
            }
        }

        return $orders;
    }

    public function payment(Request $request){
        $request->validate([
            'id' => 'required',
            'discount',
            'payment' => 'required'
        ]);

        $update = DB::table('orders')
            ->where('id', $request->id)
            ->update([
                'discount' => $request->discount,
                'status' => 'Payed'
            ]);

        $payment = DB::table('payments')
            ->insert([
                'order_id' => $request->id,
                'amount' => $request->payment
            ]);

        return $payment;
    }

    public function receipt($id){
        $order = DB::table('orders')
            ->where('id', $id)
            ->get();

        $orders = [];

        $orderDetails = DB::table('order_details')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->select(
                'order_details.order_id as order_id',
                'order_details.id as orderdetails_id',
                'order_details.quantity',
                'order_details.price as od_price',
                'products.product_name',
                'products.price as product_price'
            )
            ->where('order_details.order_id', $id)
            ->get();

        $payments = DB::table('payments')
            ->where('order_id', $id)
            ->get();

        $orders[] = [
            'discount' => $order,
            'total' => $orderDetails->sum('od_price'),
            'orders' => $orderDetails,
            'payments' => $payments
        ];

        return $orders;      
    }
}
