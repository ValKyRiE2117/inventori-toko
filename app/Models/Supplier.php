<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;

    protected $table = 'supplier';

    protected $fillable = [
        'nama',
        'alamat',
        'email',
    ];

    public function barang(): HasMany
    {
        return $this->hasMany(Barang::class, 'supplier_id');
    }
}
