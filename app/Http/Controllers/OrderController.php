<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Order::select(
                'orders.id as Id',
                'orders.name as Name',
                'orders.description as Description',
                'orders.status as Status',
                'orders.date as Date',
                'customers.name as Customer'
            )
            ->join('customers', 'orders.customer_id', '=', 'customers.id');

        if ($request->filled('status')) {
            $query->where('orders.status', $request->status);
        }

        if ($request->filled('dateFrom') && $request->filled('dateTo')) {
            $query->whereBetween('orders.date', [$request->dateFrom, $request->dateTo]);
        }

        if ($request->filled('customer')) {
            $query->where('customers.id', $request->customer);
        }

        $orders = $query->paginate($request->input('per_page', 10));

        return response()->json($orders);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|min:1',
            'description' => 'required|max:100',
            'price' => 'required|numeric',
            'weight' => 'required|numeric',
            'customer_id' => 'required|exists:customers,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $order = new Order($request->only(['name', 'description', 'price', 'weight', 'customer_id']));
        $order->status = 'Pendiente';
        $order->date = now();
        $order->save();

        return response()->json([
            'status' => true,
            'message' => 'Creado con éxito'
        ], 201);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $rules = [
            'name' => 'required|string|min:1',
            'description' => 'required|max:100',
            'price' => 'required|numeric',
            'weight' => 'required|numeric',
            'customer_id' => 'required|numeric|exists:customers,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'El pedido no existe',
            ], 400);
        }

        $order->fill($request->only(['name', 'description', 'price', 'weight', 'customer_id']));

        $order->save();

        return response()->json([
            'status' => true,
            'message' => 'Actualizado con éxito'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order =  Order::find($id);
        if ($order) {

            $order->delete();

            return response()->json([
                'status' => true,
                'message' => 'Eliminado con éxito'
            ], 200);
        }
        return response()->json([
            'status' => false,
            'message' => 'El pedido no existe'
        ], 400);
    }

    /**
     * change status a order
     * @param  \Illuminate\Http\Request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, int $id)
    {
        $validator = Validator::make($request->only('status'), [
            'status' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'El pedido no existe'
            ], 400);
        }

        $order->status = $request->status;
        $order->save();

        return response()->json([
            'status' => true,
            'message' => 'Actualizado con éxito'
        ], 200);
    }
}
