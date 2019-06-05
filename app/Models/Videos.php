<?php


namespace App\Models;

use App\Models\User;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;


class Videos extends Model
{


    protected $table = 'videos';

    protected $guarded = ['id'];



    /**
     * Validation rules for this model
     */
    static public $rules = [
        'release'       => 'required|min:3:max:255',
        'id_videos'     => 'required|min:5:max:2000',
        'category_id' => 'required|exists:categories_videos,id',
    ];




    public function category()
    {
        return $this->belongsTo(VideoCategory::class, 'category_id', 'id');
    }


}
