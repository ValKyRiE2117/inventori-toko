<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BarangMasuk extends Model
{
    use SoftDeletes;

    protected $table = 'barang_masuk';

    protected $fillable = [
        'id_user',
        'id_barang',
        'jumlah',
        'tanggal_masuk',
    ];

    protected static function booted()
    {
        // Event ketika data barang_masuk dibuat
        static::created(function ($barangMasuk) {
            // Tambah stok barang
            $barang = $barangMasuk->barang;
            $barang->stok += $barangMasuk->jumlah;
            $barang->save();
        });

        // Event ketika data barang_masuk di-update
        static::updated(function ($barangMasuk) {
            // Jika jumlah diubah, sesuaikan stok
            if ($barangMasuk->isDirty('jumlah')) {
                $selisih = $barangMasuk->jumlah - $barangMasuk->getOriginal('jumlah');
                $barang = $barangMasuk->barang;
                $barang->stok += $selisih;
                $barang->save();
            }
        });

        // Event ketika data barang_masuk di-delete (soft delete)
        static::deleted(function ($barangMasuk) {
            // Kurangi stok ketika data dihapus
            $barang = $barangMasuk->barang;
            $barang->stok -= $barangMasuk->jumlah;
            $barang->save();
        });

        // Event ketika data barang_masuk di-restore
        static::restored(function ($barangMasuk) {
            // Tambah stok kembali ketika data di-restore
            $barang = $barangMasuk->barang;
            $barang->stok += $barangMasuk->jumlah;
            $barang->save();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }
}
