<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use App\Http\Resources\KategoriResources;

class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Hitung berapa barang di setiap kategori
        $kategori = Kategori::withCount('barang')->get();

        return KategoriResources::collection($kategori);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255'
        ]);

        $kategori = Kategori::create($validated);

        return new KategoriResources($kategori);
    }

    /**
     * Display the specified resource.
     */
    public function show(Kategori $kategori)
    {
        $kategori->loadCount('barang');

        return new KategoriResources($kategori);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kategori $kategori)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255'
        ]);

        $kategori->update($validated);

        return new KategoriResources($kategori);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kategori $kategori)
    {
        $kategori->delete();

        return response()->json([
            "success" => true,
            "message" => "Kategori berhasil dihapus"
        ]);
    }
}
