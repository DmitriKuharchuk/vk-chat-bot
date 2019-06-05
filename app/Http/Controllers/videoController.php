<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 28.03.2019
 * Time: 9:24
 */



namespace App\Http\Controllers;

use App\Models\Videos;
use App\VkontakteBot\MessageNewHandler;
use Illuminate\Http\Request;

class videoCategoryController extends Controller
{

    public function index(){

        $video = Videos::all();

        return view('video.index',['videosCategory' => $video]);

    }


}