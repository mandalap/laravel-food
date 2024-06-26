<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    //user register
    public function userRegister(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'required|string',
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        $data['roles'] = 'user';

        $user = User::create($data);
        return response()->json([
            'message' => 'User register successfully',
            'user' => $user
        ]);
    }

    //login
    public function login(Request $request)
    {
        $request->validate(
            [
                'email' => 'required|email',
                'password' => 'required|string',
            ]
        );
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'User login successfully',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ]);
    }

    //logout
    public function logout(Request $request)
    {

        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'User logout successfully'
        ]);
    }

    //restaurant register
    public function restaurantRegister(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'required|string',
            'restaurant_address' => 'required|string',
            'restaurant_name' => 'required|string',
            'latlong' => 'required|string',
            'photo' => 'required|image',

        ]);

        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        $data['roles'] = 'restaurant';


        $restaurant = User::create($data);

        //check if photo upload
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photo_name = time().'.'.$photo->getClientOriginalExtension();
            $photo->move(public_path('restaurant'), $photo_name);
            $restaurant->photo = $photo_name;
            $restaurant->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Restaurant register successfully',
            'data' => $restaurant
        ]);
    }

    //driver register
    public function driverRegister(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'required|string',
            'address' => 'required|string',
            'license_plate' => 'required|string',
            'photo' => 'required|image',
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        $data['roles'] = 'driver';

        $driver = User::create($data);

         //check if photo upload
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photo_name = time().'.'.$photo->getClientOriginalExtension();
            $photo->move(public_path('driver'), $photo_name);
            $driver->photo = $photo_name;
            $driver->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Driver register successfully',
            'data' => $driver
        ]);
    }

    //update latlong user
    public function updateLatLong(Request $request)
    {
        $request->validate([
            'latlong' => 'required|string',
        ]);
        $user = $request->user();
        $user->latlong = $request->latlong;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Latlong update successfully',
            'data' => $user
        ]);
    }

    //Get All Restaurant
    public function getAllRestaurant()
    {
        $restaurant = User::where('roles', 'restaurant')->get();
        return response()->json([
            'status' => 'success',
            'message' => 'Restaurant get successfully',
            'data' => $restaurant
        ]);
    }
}

