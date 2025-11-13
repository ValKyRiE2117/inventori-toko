<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\BarangResources;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Barang::with(['supplier', 'kategori']);

            // Filter by supplier
            if ($request->has('supplier_id')) {
                $query->where('supplier_id', $request->supplier_id);
            }

            // Filter by kategori
            if ($request->has('kategori_id')) {
                $query->where('kategori_id', $request->kategori_id);
            }

            // Search by nama or kode
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('kode', 'like', "%{$search}%");
                });
            }

            // Sort
            $sortField = $request->get('sort_field', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortField, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 10);
            $barangs = $query->paginate($perPage);

            return BarangResources::collection($barangs);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data barang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:supplier,id',
            'kategori_id' => 'required|exists:kategori,id',
            'kode' => 'required|string|max:30|unique:barang,kode',
            'nama' => 'required|string|max:255',
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'stok' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::transaction(function () use ($request, &$barang) {
                $data = $request->all();

                // Upload gambar
                if ($request->hasFile('gambar')) {
                    $data['gambar'] = $request->file('gambar')->store('barang-images', 'public');
                }

                $barang = Barang::create($data);
                $barang->load(['supplier', 'kategori']);
            });

            return response()->json([
                'success' => true,
                'message' => 'Barang berhasil dibuat',
                'data' => new BarangResources($barang)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat barang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Barang $barang)
    {
        try {
            $barang->load(['supplier', 'kategori', 'barangMasuk', 'barangKeluar']);

            return response()->json([
                'success' => true,
                'data' => new BarangResources($barang)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data barang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Barang $barang)
    {
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'sometimes|required|exists:supplier,id',
            'kategori_id' => 'sometimes|required|exists:kategori,id',
            'kode' => 'sometimes|required|string|max:30|unique:barang,kode,' . $barang->id,
            'nama' => 'sometimes|required|string|max:255',
            'gambar' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'stok' => 'sometimes|required|integer|min:0',
            'deskripsi' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::transaction(function () use ($request, $barang) {
                $data = $request->all();

                // Upload gambar baru jika ada
                if ($request->hasFile('gambar')) {
                    // Hapus gambar lama
                    if ($barang->gambar) {
                        Storage::disk('public')->delete($barang->gambar);
                    }
                    $data['gambar'] = $request->file('gambar')->store('barang-images', 'public');
                }

                $barang->update($data);
                $barang->load(['supplier', 'kategori']);
            });

            return response()->json([
                'success' => true,
                'message' => 'Barang berhasil diupdate',
                'data' => new BarangResources($barang)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate barang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Barang $barang)
    {
        try {
            DB::transaction(function () use ($barang) {
                // Hapus gambar jika ada
                if ($barang->gambar) {
                    Storage::disk('public')->delete($barang->gambar);
                }

                $barang->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Barang berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus barang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore soft deleted barang
     */
    public function restore($id)
    {
        try {
            $barang = Barang::withTrashed()->findOrFail($id);
            $barang->restore();

            return response()->json([
                'success' => true,
                'message' => 'Barang berhasil dipulihkan',
                'data' => new BarangResources($barang)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memulihkan barang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get barang with low stock (stok kurang dari threshold)
     */
    public function lowStock(Request $request)
    {
        try {
            $threshold = $request->get('threshold', 10);

            $barangs = Barang::with(['supplier', 'kategori'])
                ->where('stok', '<=', $threshold)
                ->orderBy('stok', 'asc')
                ->paginate(10);

            return BarangResources::collection($barangs);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data barang dengan stok rendah',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
