<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        // Validasi
        $validator = Validator::make($request->all(), [
            'email'     => 'required',
            'password'  => 'required'
        ]);

        // Jika Validasi Salah, Berikan Info Dalam Format JSON dan Status Code 422
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Credentials yang Berisi Email dan Password 
        $credentials = $request->only('email', 'password');

        // Jika Autentikasi Salah Maka Kirim Pesan Dalam Bentuk JSON dan Status Code 401
        if(!$token = auth()->guard('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau Password Anda salah'
            ], 401);
        }

        // Jika Autentikasi Berhasil, Tampilkan Data User dan Token JWT Dalam Bentuk JSON
        return response()->json([
            'success' => true,
            'user'    => auth()->guard('api')->user(),    
            'token'   => $token   
        ], 200);
    }
}
