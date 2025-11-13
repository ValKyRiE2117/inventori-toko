<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BarangResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode' => $this->kode,
            'nama' => $this->nama,
            'gambar' => $this->gambar ? asset('storage/' . $this->gambar) : null,
            'stok' => $this->stok,
            'deskripsi' => $this->deskripsi,
            'supplier' => $this->whenLoaded('supplier', function () {
                return [
                    'id' => $this->supplier->id,
                    'nama' => $this->supplier->nama,
                    'alamat' => $this->supplier->alamat,
                    'email' => $this->supplier->email,
                ];
            }),
            'kategori' => $this->whenLoaded('kategori', function () {
                return [
                    'id' => $this->kategori->id,
                    'nama' => $this->kategori->nama,
                ];
            }),
            'barang_masuk' => $this->whenLoaded('barangMasuk'),
            'barang_keluar' => $this->whenLoaded('barangKeluar'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
