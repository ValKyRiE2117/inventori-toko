<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Barang extends Model
{
    use SoftDeletes;

    protected $table = 'barang';

    protected $fillable = [
        'supplier_id',
        'kategori_id',
        'kode',
        'nama',
        'gambar',
        'stok',
        'deskripsi',
    ];

    protected static function booted()
    {
        static::saving(function ($barang) {
            if ($barang->stok < 0) {
                throw new \Exception("Stok barang tidak boleh minus");
            }
        });
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function barangMasuk()
    {
        return $this->hasMany(BarangMasuk::class, 'id_barang');
    }

    public function barangKeluar()
    {
        return $this->hasMany(BarangKeluar::class, 'id_barang');
    }
}
