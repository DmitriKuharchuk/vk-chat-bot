<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 28.03.2019
 * Time: 9:24
 */



namespace App\Http\Controllers;

use App\Models\VideoCategory;
use App\VkontakteBot\MessageNewHandler;
use Illuminate\Http\Request;

class videoCategoryController extends Controller
{

    public function index(){

        $videoCategory = VideoCategory::all();

        return view('videocategory.index',['videosCategory' => $videoCategory]);

    }


}