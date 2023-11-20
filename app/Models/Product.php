<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = array('product_id', 'product_name', 'description', 'price', 'stock', 'production_date', 'expiry_date', 'manufacturer', 'category_id', 'status');
    protected $primaryKey = 'product_id';
    public $timestamps = true;
}