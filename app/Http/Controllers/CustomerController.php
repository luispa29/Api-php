<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Info(
 *     title="API de Productos",
 *     version="1.0.0"
 * )
 */
class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Get(
     *     path="/api/customers",
     *     tags={"Customers"},
     *     summary="Listar clientes",
     *     description="Devuelve una lista de clientes",
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
     */
    public function index()
    {
        $customers = Customer::all();
        return  response()->json([
            'status' => true,
            'data' => $customers
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    /**
     * @OA\Post(
     *     path="/api/customers",
     *     summary="Crear Cliente",
     *     tags={"Customers"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "phone"},
     *             @OA\Property(property="name", type="string", example="Pepe"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="phone", type="string", example="0123456789")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cliente creado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Revisar mensaje"
     *     )
     * )
     */

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|min:1',
            'email' => 'required|email|max:100',
            'phone' => 'required|max:40',

        ];

        $validator = Validator::make($request->input(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $customer = new Customer($request->input());

        $customer->save();

        return response()->json([
            'status' => true,
            'message' => 'Creado con éxito'
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /**
     * @OA\Get(
     *     path="/api/customers/{id}",
     *     summary="Obtener cliente por ID",
     *     tags={"Customers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del cliente",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cliente encontrado"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cliente no encontrado"
     *     )
     * )
     */



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Put(
     *     path="/customers/{customer}",
     *     summary="Actualizar un cliente existente",
     *     description="Actualiza la información de un cliente existente por ID.",
     *     operationId="updateCustomer",
     *     tags={"Customers"},
     *     @OA\Parameter(
     *         name="customer",
     *         in="path",
     *         description="ID del cliente a actualizar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","phone"},
     *             @OA\Property(property="name", type="string", example="Juan Perez"),
     *             @OA\Property(property="email", type="string", format="email", example="juan.perez@email.com"),
     *             @OA\Property(property="phone", type="string", example="555-1234")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cliente actualizado con éxito",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Actualizado con éxito")
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
    public function update(Request $request, int $id)
    {
        $rules = [
            'name' => 'required|string|min:1',
            'email' => 'required|email|max:100',
            'phone' => 'required|max:40',
        ];

        $validator = Validator::make($request->input(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $customer = Customer::find($id);
        if (!$customer = Customer::find($id)) {
            return response()->json([
                'status' => false,
                'message' => 'El cliente no existe'
            ], 400);
        }
        $customer->fill($request->only(['name', 'email', 'phone'] ));
        $customer->save();
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
    // public function destroy(Customer $customer)
    /**
     * Remove the specified customer from storage.
     *
     * Elimina un cliente por su ID.
     *
     * @OA\Delete(
     *     path="/api/customers/{id}",
     *     summary="Eliminar un cliente",
     *     description="Elimina un cliente existente por su ID.",
     *     operationId="destroyCustomer",
     *     tags={"Customers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del cliente a eliminar",
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
     *         description="El cliente no existe",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="El cliente no existe")
     *         )
     *     )
     * )
     */
    public function destroy(int $id)
    {
        $customer =  Customer::find($id);
        if ($customer) {

            $customer->delete();

            return response()->json([
                'status' => true,
                'message' => 'Eliminado con éxito'
            ], 200);
        }
        return response()->json([
            'status' => false,
            'message' => 'El cliente no existe'
        ], 400);
    }
}
