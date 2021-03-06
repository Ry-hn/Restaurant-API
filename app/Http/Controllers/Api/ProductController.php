<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Product;

class ProductController extends Controller
{
    //read
    public function index() {
        $products = Product::all();
        
        if(count($products) > 0) {
            return response()->json([
                'message' => 'Retrieve All Success',
                'data' => $products
            ], 200);
        }

        return response([
            'message' => 'Empty Data',
            'data' => null
        ], 404);
    }

    //search
    public function show($id) {
        $product = Product::find($id);
        if(!is_null($product)) {
            return response([
                'message' => 'Retrieve Product Success',
                'item' => $product
            ], 200);
        }

        return response([
            'message' => 'Product Not Found',
            'data' => null
        ], 404);
    }

    //create
    public function store(Request $request) {
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'nama_product' => 'required|max:60|unique:products',
            'deskripsi_product' => 'required',
            'harga_product' => 'required|numeric',
        ]);
        
        if($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $product = Product::create($storeData);
        
        return response(['message' => 'Add Product Success'], 200);
    }

    //delete
    public function destroy($id) {
        $product = Product::find($id);

        if(is_null($product)) {
            return response([
                'message' => 'Product Not Found',
                'item' => null
            ], 404);
        }

        if($product->delete()) {
            return response([
                'message' => 'Delete Product Success',
                'item' => $product
            ], 200);
        }

        return response([
            'message' => 'Delete Product Failed',
            'item' => null
        ], 400);
    }

    //update
    public function update(Request $request, $id) {
        $product = Product::find($id);

        if(is_null($product)) {
            return response([
                'message' => 'Product Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'nama_product' => 'max:60',
            'harga_product' => 'numeric',
        ]);
        

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);
        
        if(isset($updateData['nama_product']))
            $product->nama_product = $updateData['nama_product'];

        if(isset($updateData['deskripsi_product']))
            $product->deskripsi_product = $updateData['deskripsi_product'];

        if(isset($updateData['harga_product']))
            $product->harga_product = $updateData['harga_product'];

        if(isset($updateData['gambar_product']))
            $product->gambar_product = is_null($updateData['gambar_product']) 
                                    ? $product->gambar_product : $updateData['gambar_product'];
            
        if($product->save()) {
            return response([
                'message' => 'Update Product Success',
                'item' => $product
            ], 200);
        }

        return response([
            'message' => 'Update Product Failed',
            'item' => null
        ], 400);
    }
}
