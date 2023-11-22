<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = array('order_id','customer_name', 'product_id', 'total_amount',  'payment_status', 'order_date', 'users_id');
    protected $primaryKey = 'order_id';
    public $timestamps = true;
}