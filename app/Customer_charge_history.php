<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer_charge_history extends Model
{

    protected $table = 'customer_charge_history';
    protected $fillable = [
        'user_id','email','source','amount','currency'
    ];


}
