<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BarangKeluar extends Model
{
    use SoftDeletes;

    protected $table = 'barang_keluar';

    protected $fillable = [
        'id_user',
        'id_barang',
        'jumlah',
        'tanggal_keluar',
        'penerima',
        'keterangan'
    ];

    protected static function booted()
    {
        // Event ketika data barang_keluar dibuat
        static::created(function ($barangKeluar) {
            DB::transaction(function () use ($barangKeluar) {
                $barang = $barangKeluar->barang;
                $barang->lockForUpdate(); // Lock row untuk prevent race condition

                // Validasi stok cukup
                if ($barang->stok < $barangKeluar->jumlah) {
                    throw new \Exception("Stok barang tidak mencukupi. Stok tersedia: {$barang->stok}");
                }

                $barang->stok -= $barangKeluar->jumlah;
                $barang->save();
            });
        });

        // Event ketika data barang_keluar di-update
        static::updated(function ($barangKeluar) {
            DB::transaction(function () use ($barangKeluar) {
                // Jika jumlah diubah, sesuaikan stok
                if ($barangKeluar->isDirty('jumlah')) {
                    $barang = $barangKeluar->barang;
                    $barang->lockForUpdate();

                    $selisih = $barangKeluar->jumlah - $barangKeluar->getOriginal('jumlah');

                    // Validasi stok cukup untuk pengurangan
                    if ($selisih > 0 && $barang->stok < $selisih) {
                        throw new \Exception("Stok barang tidak mencukupi untuk penambahan jumlah keluar. Stok tersedia: {$barang->stok}");
                    }

                    $barang->stok -= $selisih;
                    $barang->save();
                }
            });
        });

        // Event ketika data barang_keluar di-delete (soft delete)
        static::deleted(function ($barangKeluar) {
            DB::transaction(function () use ($barangKeluar) {
                // Kembalikan stok ketika data dihapus
                $barang = $barangKeluar->barang;
                $barang->lockForUpdate();
                $barang->stok += $barangKeluar->jumlah;
                $barang->save();
            });
        });

        // Event ketika data barang_keluar di-restore
        static::restored(function ($barangKeluar) {
            DB::transaction(function () use ($barangKeluar) {
                // Kurangi stok kembali ketika data di-restore
                $barang = $barangKeluar->barang;
                $barang->lockForUpdate();

                // Validasi stok cukup
                if ($barang->stok < $barangKeluar->jumlah) {
                    throw new \Exception("Stok barang tidak mencukupi untuk restore. Stok tersedia: {$barang->stok}");
                }

                $barang->stok -= $barangKeluar->jumlah;
                $barang->save();
            });
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
