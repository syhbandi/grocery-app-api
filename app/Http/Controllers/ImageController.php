<?php

namespace App\Http\Controllers;

use App\Http\Resources\ImageResource;
use App\Models\Image;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
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

        return new ImageResource($image);
    }

    public function get(Request $request)
    {
        $query = Image::query();
        $images = $query->orderBy('created_at', 'desc')->get();

        return ImageResource::collection($images);
    }

    public function delete($id)
    {
        $image = Image::find($id);

        if (!$image) {
            throw new HttpResponseException(response()->json([
                'message' => 'Image not found',
            ], Response::HTTP_NOT_FOUND));
        }

        if (Storage::disk('public')->exists($image->url)) {
            // Hapus file dari storage
            Storage::disk('public')->delete($image->url);
        }

        $image->delete();
        return response()->json(['message' => 'Image deleted'], Response::HTTP_OK);
    }
}
