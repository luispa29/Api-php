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
    /**
     * @OA\Get(
     *     path="/api/orders",
     *     summary="Obtener una lista paginada de órdenes",
     *     description="Retorna una lista de órdenes con filtros opcionales por estado, rango de fechas y cliente.",
     *     operationId="getOrders",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filtrar por estado de la orden",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="dateFrom",
     *         in="query",
     *         description="Fecha de inicio para filtrar órdenes (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="dateTo",
     *         in="query",
     *         description="Fecha de fin para filtrar órdenes (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="customer",
     *         in="query",
     *         description="ID del cliente para filtrar órdenes",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Cantidad de resultados por página",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista paginada de órdenes",
     *         @OA\JsonContent(
     *             @OA\Property(property="current_page", type="integer"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="Id", type="integer"),
     *                     @OA\Property(property="Name", type="string"),
     *                     @OA\Property(property="Description", type="string"),
     *                     @OA\Property(property="Status", type="string"),
     *                     @OA\Property(property="Date", type="string", format="date"),
     *                     @OA\Property(property="Customer", type="string")
     *                 )
     *             ),
     *             @OA\Property(property="last_page", type="integer"),
     *             @OA\Property(property="per_page", type="integer"),
     *             @OA\Property(property="total", type="integer")
     *         )
     *     )
     * )
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
    /**
     * @OA\Post(
     *     path="/api/orders",
     *     summary="Crear una nueva orden",
     *     description="Crea una nueva orden con los datos proporcionados.",
     *     tags={"Orders"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","description","price","weight","customer_id"},
     *             @OA\Property(property="name", type="string", example="Producto A", description="Nombre del producto"),
     *             @OA\Property(property="description", type="string", maxLength=100, example="Descripción breve", description="Descripción de la orden"),
     *             @OA\Property(property="price", type="number", format="float", example=99.99, description="Precio del producto"),
     *             @OA\Property(property="weight", type="number", format="float", example=1.5, description="Peso del producto"),
     *             @OA\Property(property="customer_id", type="integer", example=1, description="ID del cliente existente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Orden creada con éxito",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Creado con éxito")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string"))
     *         )
     *     )
     * )
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

    /**
     * @OA\Put(
     *     path="/api/orders/{id}",
     *     summary="Actualizar un pedido existente",
     *     description="Actualiza los datos de un pedido existente por su ID.",
     *     operationId="updateOrder",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del pedido a actualizar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","description","price","weight","customer_id"},
     *             @OA\Property(property="name", type="string", example="Pedido actualizado"),
     *             @OA\Property(property="description", type="string", maxLength=100, example="Descripción del pedido"),
     *             @OA\Property(property="price", type="number", format="float", example=99.99),
     *             @OA\Property(property="weight", type="number", format="float", example=2.5),
     *             @OA\Property(property="customer_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Actualizado con éxito",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Actualizado con éxito")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación o pedido no existe",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="message", type="string", example="El pedido no existe")
     *         )
     *     )
     * )
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
    /**
     * Remove the specified order from storage.
     *
     * Elimina un pedido existente por su ID.
     *
     * @OA\Delete(
     *     path="/api/orders/{id}",
     *     summary="Eliminar un pedido",
     *     description="Elimina un pedido existente por su identificador único.",
     *     operationId="destroyOrder",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del pedido a eliminar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Eliminado con éxito",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Eliminado con éxito")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="El pedido no existe",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="El pedido no existe")
     *         )
     *     )
     * )
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

     /**
     * @OA\Put(
     *     path="/api/orders/updateStatus/{id}",
     *     summary="Actualizar el estado de un pedido",
     *     description="Actualiza el estado de un pedido existente por su ID.",
     *     operationId="updateOrderStatus",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del pedido",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", example="enviado", description="Nuevo estado del pedido")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Estado actualizado con éxito",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Actualizado con éxito")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación o pedido no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="message", type="string", example="El pedido no existe")
     *         )
     *     )
     * )
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
