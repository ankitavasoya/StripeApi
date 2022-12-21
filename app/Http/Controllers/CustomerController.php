<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\User;
class CustomerController extends Controller
{
    public $stripe;
    public function __construct()
    {
        $this->stripe = new \Stripe\StripeClient(
            env("STRIPE_SECRET")
        );
    }
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        
        $customer = $this->stripe->customers->create([
            'name' => $request->name,
            'email' => $request->email
        ]);
        if($customer) {
            $user = User::create(array_merge(
                $validator->validated(),
                ['password' => bcrypt($request->password),
                'stripe_id' => $customer->id]
            ));
            return response()->json([
                'success'=>'true',
                'message' => 'Customer successfully created',
                'user' => $user
            ], 201);
        } else {
            return response()->json([
                'success'=>'false',
                'message' => 'Something went wrong',
            ], 201);
        }
    }

    public function list(Request $request)
    {
        $customer = $this->stripe->customers->all();
        return response()->json([
            'success'=>'true',
            'message' => 'Customer list',
            'user' => $customer
            
        ], 201);
    }
    public function retrive(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        $id = $request->id;
        $customer = User::find($id);
        if($customer) {
            $customer_retrive =  $this->stripe->customers->retrieve(
                $customer->stripe_id,
                []
            );
            return response()->json([
                'success'=>'true',
                'message' => 'Retrive customer',
                'user' => $customer_retrive
                
            ], 201);
        } else {
            return response()->json([
                'success'=>'false',
                'message' => 'User not found',
            ], 201);
        }
    }
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users,email,'.$request->id,
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        $data = $request->except(['id']);
        $id = $request->id;
       
        $customer_data = User::find($id);
        if($customer_data) {
            $result = $customer_data->update($data);
            $customer = $this->stripe->customers->update(
                $customer_data->stripe_id,
                $data   
            );
            if($customer) {
                return response()->json([
                    'success'=>'true',
                    'message' => 'User updated successfully',
                    'user' => $customer
                ], 201);
            } else {
                return response()->json([
                    'success'=>'false',
                    'message' => 'User not found',
                ], 201);
            }
        } else {
            return response()->json([
                'success'=>'false',
                'message' => 'User not found',
            ], 201);
        }
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        $id = $request->id;
        $customer = User::find($id);

        if($customer) {
            $this->stripe->customers->delete(
                $customer->stripe_id,
                []
              );
            $customer->delete();
            return response()->json([
                'success'=>'true',
                'message' => 'User deleted successfully',
            ], 201);
        } else {
            return response()->json([
                'success'=>'false',
                'message' => 'User not found',
            ], 201);
        }
    }
}
