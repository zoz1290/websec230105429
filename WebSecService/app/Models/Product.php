<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model  {

	protected $fillable = [
        'code',
        'name',
        'price',
        'stock',
        'discount_percentage',
        'discount_max_products',
        'model',
        'description',
        'photo'
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    

}