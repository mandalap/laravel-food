<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    //index
    public function index(Request $request)
    {
        //get product by request user id
        $products = Product::with('user')->where('user_id', $request->user()->id)->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Products get successfully',
            'data' => $products
        ]);
    }

    //get product by user id
    public function getProductByUserId(Request $request)
    {
        $products = Product::with('user')->where('user_id', $request->user()->id)->get();
        return response()->json([
            'status' => 'success',
            'message' => 'Products get successfully',
            'data' => $products
        ]);
    }

    //store
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|integer',
            'stock' => 'required|integer',
            'is_available' => 'required|boolean',
            'is_favorite' => 'required|boolean',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user = $request->user();
        $request->merge(['user_id' => $user->id]);
        $data = $request->all();

        $product = Product::create($data);

        //check if image is uploaded
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image_name = time() . '.' . $image->getClientOriginalExtension();
            $image->move('uploads/prodcuts', $image_name);

            $product->image = $image_name;
            $product->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Product created successfully',
            'data' => $product
        ]);
    }

    //update
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|integer',
            'stock' => 'required|integer',
            'is_available' => 'required|boolean',
            'is_favorite' => 'required|boolean',
        ]);

        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found'
            ], 404);
        }

        $data = $request->all();
        $product->update($data);

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Product updated successfully',
                'data' => $product
            ]
        );
    }


    //delete
    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found']);
        }

        $product->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Product deleted successfully'
        ]);
    }



}
