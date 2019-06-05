<?php


namespace App\Models;

use App\Models\User;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;


class Like extends Model
{


    protected $table = 'like';

    protected $guarded = ['id'];



    /**
     * Validation rules for this model
     */
    static public $rules = [
        'from_id'       => 'required|min:3:max:255',
        'to_id'     => 'required|min:5:max:2000',

    ];






}
