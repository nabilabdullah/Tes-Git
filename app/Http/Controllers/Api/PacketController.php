<?php

namespace App\Http\Controllers\Api;

use App\Models\Packet;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\PacketResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PacketController extends Controller
{
    public function index()
    {
        // Ambil Data Paket Foto
        $packets = Packet::latest()->paginate(5);

        // Kembalikan Data Dalam Bentuk JSON (status, message, resource)
        return new PacketResource(true, 'Daftar Paket Foto', $packets);
    }

    // INSERT DATA
    public function store(Request $request)
    {
        // Validasi Data
        $validator = Validator::make($request->all(), [
            'nama'          => 'required',
            'deskripsi'     => 'required',
            'waktu_foto'    => 'required',
            'gambar'        => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            
        ]);

        // Jika Validasi Salah, Berikan Info Dalam Format JSON dan Status Code 422
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Jika Validasi Benar, Upload Gambar Kedalam Server
        $gambar = $request->file('gambar');
        $gambar->storeAs('public/packets', $gambar->hashName());

        // Jika Validasi Benar, Insert Data Kedalam Database
        $packets = Packet::create([
            'nama'          => $request->nama,
            'deskripsi'     => $request->deskripsi,
            'waktu_foto'    => $request->waktu_foto,
            'gambar'         => $gambar->hashName(), 
        ]);

        // Informasi Dalam Format JSON Setelah Insert Data Berhasil
        return new PacketResource(true, 'Data Paket Foto Berhasil Ditambahkan!', $packets);
    }

    // DETAIL DATA
    public function show(Packet $paket)
    {
        // Informasi Paket Dalam Format JSON 
        return new PacketResource(true, 'Data Paket Foto Ditemukan!', $paket);
    }

    // UPDATE DATA
    public function update(Request $request, Packet $paket)
    {
        // Validasi Data
        $validator = Validator::make($request->all(), [
            'nama'          => 'required',
            'deskripsi'     => 'required',
            'waktu_foto'    => 'required',
        ]);

        // Jika Validasi Salah, Berikan Info Dalam Format JSON dan Status Code 422
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Jika Validasi Benar, Dan Ada File Gambar Baru
        if ($request->hasFile('gambar')) {

            // Upload File Gambar Baru Ke Server
            $gambar = $request->file('gambar');
            $gambar->storeAs('public/packets', $gambar->hashName());

            // Hapus File Gambar Lama Di Server
            Storage::delete('public/packets/'.$paket->gambar);
            // File::delete(public_path('img/qrcodes/'.$data['kode_barang'].'.svg'));

            // Update Data Paket Foto Dengan Gambar Baru
            $paket->update([
                'nama'          => $request->nama,
                'deskripsi'     => $request->deskripsi,
                'waktu_foto'    => $request->waktu_foto,
                'gambar'        => $gambar->hashName(), 
            ]);

        // Update Data Foto Jika Tidak Ada Gambar Baru
        } else {
            $paket->update([
                'nama'          => $request->nama,
                'deskripsi'     => $request->deskripsi,
                'waktu_foto'    => $request->waktu_foto,
            ]);
        }

        // Informasi Paket Dalam Format JSON
        return new PacketResource(true, 'Data Paket Foto Berhasil Diubah!', $paket);
    }

    public function destroy(Packet $paket)
    {
        // Hapus File Gambar Pada Server
        Storage::delete('public/packets/'.$paket->gambar);

        // Hapus Data Pada Database
        $paket->delete();

        //return response
        return new PacketResource(true, 'Data Paket Foto Berhasil Dihapus!', null);
    }
}
