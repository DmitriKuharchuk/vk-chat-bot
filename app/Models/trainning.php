<?php


namespace App\Models;

use App\Models\User;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;


class trainning extends Model
{


    protected $table = 'trainning';

    protected $guarded = ['id'];



    /**
     * Validation rules for this model
     */
    static public $rules = [
        'name'       => 'required|min:3:max:255',
        'style'     => 'required|min:5:max:2000',
        'date'       => 'required|min:3:max:255',
        'coach'     => 'required|min:5:max:2000',
        'address'       => 'required|min:3:max:255',

    ];






}
