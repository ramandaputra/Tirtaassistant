<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function reply(Request $request)
    {
        $message = strtolower(trim($request->input('message')));

        // Contoh logika jawaban sederhana
        if (str_contains($message, 'halo') || str_contains($message, 'hai')) {
            $reply = 'Halo! Ada yang bisa saya bantu hari ini?';
        } elseif (str_contains($message, 'harga') || str_contains($message, 'layanan')) {
            $reply = 'Untuk informasi harga dan layanan, Anda bisa cek halaman Produk kami.';
        } else {
            $reply = 'Maaf, saya belum mengerti pertanyaan Anda. Bisa jelaskan lebih detail?';
        }

        return response()->json(['reply' => $reply]);
    }
}
