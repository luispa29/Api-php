<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use function Psy\debug;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customers = Customer::all();
        return  response()->json($customers);
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
    public function show(Customer $customer)
    {
        return response()->json([
            'status' => true,
            'data' => $customer
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
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

        $customer->update($request->input());
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
