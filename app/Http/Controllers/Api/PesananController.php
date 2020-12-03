<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

Use App\Pesanan;
Use DB;


class PesananController extends Controller{
    
    public function store(Request $request) {
        $reqData = $request->all();
        
        // $data = DB::table('pesanans')->where('pesanans.id_user', $reqData['id_user'])
        //         ->where('pesanans.id_product', $reqData['id_product'])
        //         ->first();
        
        if(DB::table('pesanans')->where('pesanans.id_user', $reqData['id_user'])
            ->where('pesanans.id_product', $reqData['id_product'])->increment('jumlah_pesan', $reqData['jumlah_pesan'])) {

                return response(['message' => 'Tambah Jumlah Pesan Berhasil'], 200);
        }
        
        $pesanan = Pesanan::create($reqData);
        return response(['message' => 'Add Pesanan Success'], 200);
    }

    public function search($id) {
        $reqData = DB::table('pesanans')->where('pesanans.id_user', $id)
                    ->join('products', 'products.id', '=', 'pesanans.id_product')
                    ->get();

        if($reqData->isEmpty()) 
            return response(['message' => 'Data tidak ditemukan'], 200);

        return response(['message' => 'Retrieve Pesanan Success', 'pesanan' => $reqData], 200);
    }

    public function update(Request $request, $id) {

        $data = $request->all();

        try {
            if(DB::table('pesanans')->where('id_pesanan', $id)->update(['jumlah_pesan' => $data['jumlah_pesan']])) 
                return response(['message' => 'Update Pesanan Success'], 200);
        
            return response(['message' => 'Pesanan Not Found'], 404);
        }
        catch(Throwable $e) {
            return response(['message' => 'Pesanan Gagal di update'], 404);  
        }
    }

    public function destroy($id) {
        
        try {
            if(DB::table('pesanans')->where('id_pesanan', $id)->delete()) 
                return response(['message' => 'Delete Pesanan Success'], 200);
        
            return response(['message' => 'Pesanan Not Found'], 404);
        }
        catch(Throwable $e) {
            return response(['message' => 'Pesanan Gagal dihapus'], 404);  
        }
    }

}
