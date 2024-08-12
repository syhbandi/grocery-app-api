<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Dapatkan file yang diupload
        $file = $request->file('file');

        // Generate nama file baru yang unik
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

        // Simpan file di direktori storage/public/photos dengan nama baru
        $path = $file->storeAs('images', $filename, 'public');

        // Buat instance Photo di database
        $image = Image::create(['url' => $path]);

        return response()->json($image, 201);
    }
}
