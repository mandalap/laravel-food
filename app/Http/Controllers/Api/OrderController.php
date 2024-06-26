<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    //User: create new order
    public function createOrder(Request $request)
    {
        $request->validate([
            'order_items' => 'required|array',
            'order_items.*.product_id' => 'required|exists:products,id',
            'order_items.*.quantity' => 'required|integer|min:1',
            'restaurant_id' => 'required|integer|exists:users,id',
            'shipping_cost' => 'required|integer',
        ]);

        $totalPrice = 0;
        foreach ($request->order_items as $item) {
            $product = Product::find($item['product_id']);
            $totalPrice += $product->price * $item['quantity'];
        }
        $totalBill  = $totalPrice + $request->shipping_cost;

        $user = $request->user();
        $data = $request->all();
        $data['user_id'] = $user->id;
        $shippingAddress = $user->address;
        $data['shipping_address'] = $shippingAddress;
        $shippingLatLong = $user->latlong;
        $data['shipping_latlong'] = $shippingLatLong;
        $data['status'] = 'pending';
        $data['total_price'] = $totalPrice;
        $data['total_bill'] = $totalBill;

        $order = Order::create($data);

        foreach ($request->order_items as $item) {
            $product = Product::find($item['product_id']);

            $orderItem = new OrderItem([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $product->price,
            ]);
            $order->orderItems()->save($orderItem);
        }

       return response()->json(
            [
                'status' => 'success',
                'message' => 'Order created successfully',
                'order' => $order,
            ],
       );
    }

    //update purchase status
    public function updatePurchaseStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:pending,processing,completed,cancelled',
        ]);

        $order = Order::find($id);
        $order->status = $request->status;
        $order->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Purchase status updated successfully',
            'order' => $order,
        ]);
    }

    //order history
    public function orderHistory(Request $request)
    {
        $user = $request->user();
        $orders = Order::where('user_id', $user->id)->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Order history retrieved successfully',
            'orders' => $orders,
        ]);
    }

    //cancel order
    public function cancelOrder(Request $request, $id)
    {
        $order = Order::find($id);
        $order->status = 'cancelled';
        $order->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Order cancelled successfully',
            'order' => $order,
        ]);
    }

    //get orders by status for restaurant
    public function getOrdersByStatusRestaurant(Request $request, $status)
    {
        $request->validate([
            'status' => 'required|string|in:pending,processing,completed,cancelled',
        ]);

        $user = $request->user();
        $orders = Order::where('restaurant_id', $user->id)->where('status', $status)->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Orders retrieved successfully',
            'orders' => $orders,
        ]);
    }

    //update order status for restaurant
    public function updateOrderStatusRestaurant(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:pending,processing,completed,cancelled,ready_to_deliver,preparing,'
        ]);

        $order = Order::find($id);
        $order->status = $request->status;
        $order->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Order status updated successfully',
            'order' => $order,
        ]);
    }

    //get order by status for driver
    public function getOrderByStatusForDriver(Request $request)
    {
        $request->validate([
            'status' => 'required|string|in:pending,processing,completed,cancelled,',
        ]);

        $user = $request->user();
        $orders = Order::where('driver_id', $user->id)->where('status', $request->status)->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Order retrieved successfully',
            'orders' => $orders,
        ]);
    }

    //get order status ready for delivery
    public function getOrderReadyForDelivery(Request $request)
    {
        $orders = Order::with('restaurant')
                ->where('status', 'ready_to_deliver')
                ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Order retrieved successfully',
            'orders' => $orders,
        ]);
    }

    //update status for driver
    public function updateOrderStatusForDriver(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:pending,processing,completed,cancelled,on_the_way,delivered',
        ]);

        $order = Order::find($id);
        $order->status = $request->status;
        $order->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Order status updated successfully',
            'order' => $order,
        ]);
    }
}
