<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Request_form extends Model
{

    protected $table = 'form_request';
    protected $fillable = [
        'user_id','property_type','no_of_bedroom','uni_college','transportation','distance_of_uni','price_range_per_month','length_of_lease','move_in_date','parking','additional_comments'
    ];


}
