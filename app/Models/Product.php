<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'stock', 'price', 'image'];

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'products_id');
    }
    public function detail()
    {
        return $this->hasMany(Detail::class, 'products_id');
    }

}
