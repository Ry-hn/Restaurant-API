<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

Use App\Pesanan;
Use DB;


class PesananController extends Controller{
    
    public function store(Request $request) {
        $reqData = $request->all();
        
        $pesanan = Pesanan::create($reqData);
        return response(['message' => 'Add Product Success'], 200);
    }

    public function search($id) {
        $reqData = DB::table('pesanans')->where('pesanans.id_user', $id)
                    ->join('products', 'products.id', '=', 'pesanans.id_product')
                    ->get();

        if($reqData->isEmpty()) 
            return response(['message' => 'Data tidak ditemukan'], 200);

        return response(['message' => 'Retrieve Success', 'pesanan' => $reqData], 200);
    }

    public function update(Request $request, $id) {
        $pesanan = Pesanan::findOrFail($id);

        $jml = $request->all();

        if(isset($jml['jumlah_pesan'])) {
            $pesanan->jumlah_pesan = $jml['jumlah_pesan'];
        }

        if($pesanan->save()) {
            return response(['message' => 'Update Pesanan Success'], 200);
        }

        return response(['message' => 'Update Pesanan Gagal'], 400);
    }

    public function destroy($id) {
        $pesanan = Pesanan::findOrFail($id);

        if(is_null($pesanan)) {
            return response([
                'message' => 'Product Not Found',
                'pesanan' => null
            ], 404);
        }

        if($pesanan->delete()) {
            return response([
                'message' => 'Delete Product Success',
                'pesanan' => $pesanan
            ], 200);
        }

        return response([
            'message' => 'Delete Product Failed',
            'pesanan' => null
        ], 400);
    }

}
