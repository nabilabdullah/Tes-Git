<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
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
            'nama'      => 'required',
            'email'     => 'required|email|unique:users',
            'no_hp'     => 'required|min:10|unique:users',
            'password'  => 'required|min:8|confirmed'
        ]);

        // Jika Validasi Salah, Berikan Info Dalam Format JSON dan Status Code 422
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Jika Validasi Benar Maka INSERT Data User
        $user = User::create([
            'nama'      => $request->nama,
            'email'     => $request->email,
            'no_hp'     => $request->no_hp,
            'password'  => bcrypt($request->password)
        ]);

        // Jika INSERT Berhasil, Berikan Info Dalam Format JSON Beserta Data User dan Status Code 201
        if($user) {
            return response()->json([
                'success' => true,
                'user'    => $user,  
            ], 201);
        }

        // Jika INSERT Gagal, Berikan Info Dalam Format JSON Serta 'success' bernilai false dan Status Code 409
        return response()->json([
            'success' => false,
        ], 409);
    }
}
