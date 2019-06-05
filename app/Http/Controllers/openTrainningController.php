<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 28.03.2019
 * Time: 9:24
 */



namespace App\Http\Controllers;

use App\Models\trainning;
use App\VkontakteBot\MessageNewHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class openTrainningController extends Controller
{

    public function index(){

        $training = trainning::all();

        return view('opentraining.index',['training' => $training]);

    }
    public function delete($id){

         DB::table('training')
            ->where('id','=',$id)
            ->delete();

        return redirect('/opentraining',200);
    }

}