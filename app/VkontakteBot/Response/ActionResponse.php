<?php
namespace App\VkontakteBot\Response;

use App\User;
use App\VkontakteBot\BotKeyboard\Button;
use App\VkontakteBot\BotKeyboard\ButtonRowFactory;
use App\VkontakteBot\BotKeyboard\KeyboardFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\Types\Null_;
use VK\Client\VKApiClient;

class ActionResponse
{

    private $vkApiClient;
    private $request;
    private $accessToken;
    private $botStandartMessages;

    public function __construct(
        Request $request,
        VKApiClient $vkApiClient


    ) {
        $this->botStandartMessages    = config('bot_messages');
        $this->botStandartAttachments = config('bot_vk_media_attachments');
        $this->botButtonLabels        = config('bot_button_names');
        $this->request                = $request;
        $this->vkApiClient            = $vkApiClient;
        //$this->accessToken            = '7a7e19314a4acc2ce0bd84e3a66194af0149ac39baa45fa4842ecbe88f8c3a7e2e579833d8b7068033163';
        $this->accessToken   = '3519a04135d5d6e9e36452370130ecee0f88d6fdb11cd320e83aff96c2d5e8e82df1cda2a23cbf9fd4557';
    }



    public function start( ) //Стартовая функция
    {
        $user_id = $this->request->object['from_id'];





        $json = file_get_contents('https://api.vk.com/method/users.get?user_ids='.$user_id.'&fields=sex%2Cbdate%2Cphoto_id%2Ccity&access_token='.$this->accessToken.'&v=5.92');
        $user_info = json_decode($json);


        $user = DB::table('users')
            ->where('user_id', '=' , $user_info->response[0]->id)
            ->get();

        if (isset($user_info->response[0]->city->title)){
            $city = $user_info->response[0]->city->title;
        }
        else{
            $city = '';
        }

        if (isset($user_info->response[0]->photo_id)){
            $photo_id = $user_info->response[0]->photo_id;
        }
        else{
            $photo_id = '';
        }

        $age = Null;





        if (!isset($user[0]->user_id)) {
            DB::table('users')->insert([
                ['user_id' => $user_info->response[0]->id,
                    'name' => $user_info->response[0]->first_name,
                    'sex' => $user_info->response[0]->sex,
                    'age' => $age,
                    'city' => $city,
                    'photo' => $photo_id
                ],
            ]);
        }


        $btngetGirl = Button::create(['button' => 'getGirl'], $this->botButtonLabels['1'], 'primary');
        $btngetBoy = Button::create(['button' => 'getBoy'], $this->botButtonLabels['2'], 'primary');
        $btngetAll = Button::create(['button' => 'getAll'], $this->botButtonLabels['3'], 'primary');
        $currnetOpenTraining = Button::create(['button' => 'OpenTraining'], $this->botButtonLabels['OpenTraining'], 'primary');
        $video = Button::create(['button' => 'video'], $this->botButtonLabels['video'], 'primary');
        $btnRow1 = ButtonRowFactory::createRow()
            ->addButton($btngetGirl)
            ->addButton($btngetBoy)
            ->addButton($btngetAll)
            ->getRow();

        $btnRow2 = ButtonRowFactory::createRow()
            ->addButton($currnetOpenTraining)
            ->getRow();
        $btnRow3 = ButtonRowFactory::createRow()
            ->addButton($video)
            ->getRow();



        $kb = KeyboardFactory::createKeyboard()
            ->addRow($btnRow1)
            ->addRow($btnRow2)
            ->addRow($btnRow3)
            ->setOneTime(false)
            ->getKeyboardJson();



        $params = [
            'user_id'   => $this->request->object['from_id'],
            'random_id' => rand(0, 2147483647),
            'message'   => $this->botStandartMessages['start_message'],
            'keyboard'  => $kb,
        ];






        $this->vkApiClient->messages()->send($this->accessToken, $params);







    }



    public function getBoyClick()//Получить поиск парней
    {

        $userId = $this->request->object['from_id'];

        Cache::put("dialog_step_$userId", 'getBoy', 10080);


        DB::table('users')->where('user_id','=', $userId)
            ->update(['whom_search' => 2]);


        $btnaddInfo = Button::create(['button' => 'addInfo'], $this->botButtonLabels['1'], 'primary');
        $btnSkip = Button::create(['button' => 'skip'], $this->botButtonLabels['2'], 'primary');

        $currnetOpenTraining = Button::create(['button' => 'OpenTraining'], $this->botButtonLabels['OpenTraining'], 'primary');
        $video = Button::create(['button' => 'video'], $this->botButtonLabels['video'], 'primary');


        $btnRow1 = ButtonRowFactory::createRow()
            ->addButton($btnaddInfo)
            ->addButton($btnSkip)
            ->getRow();
        $btnRow2 = ButtonRowFactory::createRow()
            ->addButton($currnetOpenTraining)
            ->getRow();
        $btnRow3 = ButtonRowFactory::createRow()
            ->addButton($video)
            ->getRow();


        $kb = KeyboardFactory::createKeyboard()
            ->addRow($btnRow1)
            ->addRow($btnRow2)
            ->addRow($btnRow3)
            ->setOneTime(true)
            ->getKeyboardJson();

        $params = [
            'user_id'   => $userId,
            'random_id' => rand(0, 2147483647),
            'message'   => $this->botStandartMessages['aboutYou'],
            'keyboard'  => $kb,
        ];

        $this->vkApiClient->messages()->send($this->accessToken, $params);
    }



    public function getGirlClick() //Получить поиск девушек
    {

        $userId = $this->request->object['from_id'];

        Cache::put("dialog_step_$userId", 'getGirl', 10080);


        DB::table('users')->where('user_id','=', $userId)
            ->update(['whom_search' => 1]);


        $btnaddInfo = Button::create(['button' => 'addInfo'], $this->botButtonLabels['1'], 'primary');
        $btnSkip = Button::create(['button' => 'skip'], $this->botButtonLabels['2'], 'primary');

        $currnetOpenTraining = Button::create(['button' => 'OpenTraining'], $this->botButtonLabels['OpenTraining'], 'primary');
        $video = Button::create(['button' => 'video'], $this->botButtonLabels['video'], 'primary');


        $btnRow1 = ButtonRowFactory::createRow()
            ->addButton($btnaddInfo)
            ->addButton($btnSkip)
            ->getRow();



        $btnRow2 = ButtonRowFactory::createRow()
            ->addButton($currnetOpenTraining)
            ->getRow();
        $btnRow3 = ButtonRowFactory::createRow()
            ->addButton($video)
            ->getRow();


        $kb = KeyboardFactory::createKeyboard()
            ->addRow($btnRow1)
            ->addRow($btnRow2)
            ->addRow($btnRow3)
            ->setOneTime(true)
            ->getKeyboardJson();

        $params = [
            'user_id'   => $userId,
            'random_id' => rand(0, 2147483647),
            'message'   => $this->botStandartMessages['aboutYou'],
            'keyboard'  => $kb,
        ];

        $this->vkApiClient->messages()->send($this->accessToken, $params);

    }





    public function getAllSearchClick() //Получить поиск всех
    {

        $userId = $this->request->object['from_id'];

        Cache::put("dialog_step_$userId", 'AllSearch', 10080);


        DB::table('users')->where('user_id','=', $userId)
            ->update(['whom_search' => 3]);


        $btnAddInfo = Button::create(['button' => 'addInfo'], $this->botButtonLabels['1'], 'primary');
        $btnSkip = Button::create(['button' => 'skip'], $this->botButtonLabels['2'], 'primary');



        $btnRow1 = ButtonRowFactory::createRow()
            ->addButton($btnAddInfo)
            ->addButton($btnSkip)
            ->getRow();

        $currnetOpenTraining = Button::create(['button' => 'OpenTraining'], $this->botButtonLabels['OpenTraining'], 'primary');
        $video = Button::create(['button' => 'video'], $this->botButtonLabels['video'], 'primary');


        $btnRow2 = ButtonRowFactory::createRow()
            ->addButton($currnetOpenTraining)
            ->getRow();
        $btnRow3 = ButtonRowFactory::createRow()
            ->addButton($video)
            ->getRow();


        $kb = KeyboardFactory::createKeyboard()
            ->addRow($btnRow1)
            ->addRow($btnRow2)
            ->addRow($btnRow3)
            ->setOneTime(true)
            ->getKeyboardJson();

        $params = [
            'user_id'   => $userId,
            'random_id' => rand(0, 2147483647),
            'message'   => $this->botStandartMessages['aboutYou'],
            'keyboard'  => $kb,
        ];

        $this->vkApiClient->messages()->send($this->accessToken, $params);
    }



    public function addInfoUser() //Информация поле about_user
    {
        $userId = $this->request->object['from_id'];


        Cache::put("dialog_step_$userId", 'addInfo', 10080);


        $btnSkip = Button::create(['button' => 'skip'], $this->botButtonLabels['skip'], 'primary');



        $btnRow1 = ButtonRowFactory::createRow()
            ->addButton($btnSkip)
            ->getRow();



        $kb = KeyboardFactory::createKeyboard()
            ->addRow($btnRow1)
            ->setOneTime(true)
            ->getKeyboardJson();

        $params = [
            'user_id'   => $userId,
            'random_id' => rand(0, 2147483647),
            'message'   => $this->botStandartMessages['addInfoText'],
            'keyboard'  => $kb,

        ];


        if ($this->request->object['text'] !== '1'){
            DB::table('users')->where('user_id','=', $userId)
                ->update(['about_user' => $this->request->object['text']]);
                $this->viewProfile();
        }
        else{
            $this->vkApiClient->messages()->send($this->accessToken, $params);
        }




    }





    public function viewProfile()
    {
        $userId = $this->request->object['from_id'];



        Cache::put("dialog_step_$userId", 'addInfoUser', 10080);

        $btnTrueProfile = Button::create(['button' => 'trueProfile'], $this->botButtonLabels['1'], 'primary');
        $btnChangeProfile = Button::create(['button' => 'editProfile'], $this->botButtonLabels['2'], 'primary');

        $btnCheckCouples = Button::create(['button' => 'getCouples'], $this->botButtonLabels['getCouples'], 'primary');


        $searchCoach = Button::create(['button' => 'searchCoach'], $this->botButtonLabels['searchCoach'], 'primary');
        $likesCoach = Button::create(['button' => 'likesCoach'], $this->botButtonLabels['likesCoach'], 'primary');


        $getUsersTrainning = Button::create(['button' => 'getUsersCoach'], $this->botButtonLabels['getUsersCoach'], 'primary');

        $currnetOpenTraining = Button::create(['button' => 'OpenTraining'], $this->botButtonLabels['OpenTraining'], 'primary');
        $video = Button::create(['button' => 'video'], $this->botButtonLabels['video'], 'primary');


        $user = DB::table('users')
            ->where('user_id', '=' , $this->request->object['from_id'])
            ->get();


        $coach = DB::table('coach')
            ->where('user_id','=',$userId)
            ->first();



        $btnRow1 = ButtonRowFactory::createRow()
        ->addButton($btnTrueProfile)

        ->addButton($btnChangeProfile)
        ->getRow();




        $getInfo = DB::table('users')
            ->select('name','age','city','photo','about_user')
            ->where('user_id','=',$userId)
            ->get();


        $btnRow2 = ButtonRowFactory::createRow()
            ->addButton($btnCheckCouples)

            ->getRow();

        if (isset($coach)){
            $btnRow3 = ButtonRowFactory::createRow()
                ->addButton($getUsersTrainning)
                ->getRow();
        }else{
            $btnRow3 = ButtonRowFactory::createRow()
                ->addButton($searchCoach)
                ->addButton($likesCoach)
                ->getRow();
        }


        $btnRow4 = ButtonRowFactory::createRow()
            ->addButton($currnetOpenTraining)
            ->getRow();
        $btnRow5 = ButtonRowFactory::createRow()
            ->addButton($video)
            ->getRow();




        $kb = KeyboardFactory::createKeyboard()
            ->addRow($btnRow1)
            ->addRow($btnRow2)
            ->addRow($btnRow3)
            ->addRow($btnRow4)
            ->addRow($btnRow5)
            ->setOneTime(true)
            ->getKeyboardJson();


        $photo = $getInfo[0]->photo;







        if (($getInfo[0]->age == Null) || ($getInfo[0]->city =='')){
            $this->editAge();
        }
        else{
            $params = [
                'user_id'   => $userId,
                'random_id' => rand(0, 2147483647),
                'message'   => 'Так выглядит твоя анкета:
Имя -'.$getInfo[0]->name.'
Возраст - '.$user[0]->age.'
Город - '.$getInfo[0]->city.'
О себе - '.$getInfo[0]->about_user,

                'keyboard'  => $kb,
                'attachment' => 'photo'.$getInfo[0]->photo,

            ];


            $secondMessage = [
                'user_id'   => $userId,
                'random_id' => rand(0, 2147483647),
                'message'   => $this->botStandartMessages['menuProfile'],
                'keyboard'  => $kb,


            ];

            $this->vkApiClient->messages()->send($this->accessToken, $params);
            $this->vkApiClient->messages()->send($this->accessToken, $secondMessage);

        }


    }

    public function startSearch(){
        $userId = $this->request->object['from_id'];

        Cache::put("dialog_step_$userId", 'startSearch', 10080);

        $btnLike = Button::create(['button' => 'like'], $this->botButtonLabels['like'], 'primary');
        $btnDislike = Button::create(['button' => 'dislike'], $this->botButtonLabels['dislike'], 'primary');
        $btnZzZz = Button::create(['button' => 'ZzZz'], $this->botButtonLabels['ZzZz'], 'primary');

        $btnSkip = Button::create(['button' => 'startAgain'], $this->botButtonLabels['2'], 'primary');

        $getUserData = DB::table('users')
            ->where('user_id','=',$userId)
            ->get();



        $ageBefore = $getUserData[0]->age - 7;
        $ageAfter = $getUserData[0]->age + 7;


        if ($getUserData[0]->whom_search == 3){
            $couple = DB::table('users')
                ->where('user_id','!=',$userId)
                ->whereBetween('age',[$ageBefore,$ageAfter])

                ->leftJoin('likes', function($join) use($userId) {
                    $join->on('users.user_id', '=', 'likes.to_id')
                        ->where('likes.from_id', '=', $userId);

                })
                ->whereNull('likes.to_id')

                ->leftJoin('dislikes', function($join) use($userId) {
                    $join->on('users.user_id', '=', 'dislikes.to_id')
                        ->where('dislikes.from_id', '=', $userId);

                })
                ->whereNull('dislikes.to_id')


                ->where('city','=',$getUserData[0]->city)
                ->inRandomOrder()
                ->first();
        }else{
            $couple = DB::table('users')
                ->where('user_id','!=',$userId)

                ->whereBetween('age',[$ageBefore,$ageAfter])
                ->leftJoin('likes', function($join) use($userId) {
                    $join->on('users.user_id', '=', 'likes.to_id')
                        ->where('likes.from_id', '=', $userId);

                })
                ->whereNull('likes.to_id')

                ->leftJoin('dislikes', function($join) use($userId) {
                    $join->on('users.user_id', '=', 'dislikes.to_id')
                        ->where('dislikes.from_id', '=', $userId);

                })
                ->whereNull('dislikes.to_id')


                ->where('city','=',$getUserData[0]->city)
                ->where('sex','=',$getUserData[0]->whom_search)
                ->inRandomOrder()
                ->first();
        }




       if ((!isset($couple))
        ){


            $btnCheckProfile = Button::create(['button' => 'CheckProfile'], $this->botButtonLabels['1'], 'primary');
           $btnZzZz = Button::create(['button' => 'ZzZz'], $this->botButtonLabels['ZzZz'], 'primary');

           $currnetOpenTraining = Button::create(['button' => 'OpenTraining'], $this->botButtonLabels['OpenTraining'], 'primary');
           $video = Button::create(['button' => 'video'], $this->botButtonLabels['video'], 'primary');





           $btnRow2 = ButtonRowFactory::createRow()
               ->addButton($currnetOpenTraining)
               ->getRow();
           $btnRow3 = ButtonRowFactory::createRow()
               ->addButton($video)
               ->getRow();

            $btnRow1 = ButtonRowFactory::createRow()

                ->addButton($btnCheckProfile)
                ->addButton($btnSkip)
                ->addButton($btnZzZz)
                ->getRow();



            $kb = KeyboardFactory::createKeyboard()
                ->addRow($btnRow1)
                ->addRow($btnRow2)
                ->addRow($btnRow3)
                ->setOneTime(true)
                ->getKeyboardJson();


            $params = [
                'user_id'    => $this->request->object['from_id'],
                'random_id'  => rand(0, 2147483647),
                'message'    => 'Анкет подходящих нет
1. Посмотреть/редактировать мою анкету.
2. Снова проверить анкеты
3. Я больше не хочу никого искать.',
                'keyboard' => $kb,
            ];
            $this->vkApiClient->messages()->send($this->accessToken, $params);

        }
        else
        {

            $btnRow1 = ButtonRowFactory::createRow()
                ->addButton($btnLike)
                ->addButton($btnDislike)
                ->addButton($btnZzZz)
                ->getRow();


            $kb = KeyboardFactory::createKeyboard()
                ->addRow($btnRow1)
                ->setOneTime(true)
                ->getKeyboardJson();






            $params = [
                'user_id'    => $this->request->object['from_id'],
                'random_id'  => rand(0, 2147483647),
                'message'    => 'Нашел кое-кого для тебя, смотри:
Имя - '.$couple->name.'
Возраст - '.$couple->age.'
Город - '.$couple->city.'
Информация - '.$couple->about_user,
                'keyboard'  => $kb,
                'attachment' => 'photo'.$couple->photo,
            ];

            $this->vkApiClient->messages()->send($this->accessToken, $params);


            $fd = fopen($userId.".txt", 'w') or die("не удалось создать файл");
            $str = $couple->user_id;
            fwrite($fd, $str);
            fclose($fd);


        }



    }





    public function searchCoach(){
        $userId = $this->request->object['from_id'];

        Cache::put("dialog_step_$userId", 'startSearch', 10080);

        $btnLike = Button::create(['button' => 'likeCoach'], $this->botButtonLabels['like'], 'primary');
        $btnDislike = Button::create(['button' => 'dislikeCoach'], $this->botButtonLabels['dislike'], 'primary');
        $btnZzZz = Button::create(['button' => 'ZzZz'], $this->botButtonLabels['ZzZz'], 'primary');

        $btnSkip = Button::create(['button' => 'startAgain'], $this->botButtonLabels['2'], 'primary');

        $getUserData = DB::table('users')
            ->where('user_id','=',$userId)
            ->get();







            $couple = DB::table('coach')
                ->where('user_id','!=',$userId)


                ->leftJoin('likes_coach', function($join) use($userId) {
                    $join->on('coach.user_id', '=', 'likes_coach.to_id')
                        ->where('likes_coach.from_id', '=', $userId);

                })
                ->whereNull('likes_coach.to_id')

                ->leftJoin('dislikes_coach', function($join) use($userId) {
                    $join->on('coach.user_id', '=', 'dislikes_coach.to_id')
                        ->where('dislikes_coach.from_id', '=', $userId);

                })
                ->whereNull('dislikes_coach.to_id')


                ->where('city','=',$getUserData[0]->city)
                ->inRandomOrder()
                ->first();



        if ((!isset($couple))
        ){


            $btnCheckProfile = Button::create(['button' => 'CheckProfile'], 'Просмотр профиля', 'primary');


            $currnetOpenTraining = Button::create(['button' => 'OpenTraining'], $this->botButtonLabels['OpenTraining'], 'primary');
            $video = Button::create(['button' => 'video'], $this->botButtonLabels['video'], 'primary');





            $btnRow2 = ButtonRowFactory::createRow()
                ->addButton($currnetOpenTraining)
                ->getRow();
            $btnRow3 = ButtonRowFactory::createRow()
                ->addButton($video)
                ->getRow();

            $btnRow1 = ButtonRowFactory::createRow()

                ->addButton($btnCheckProfile)
                ->getRow();



            $kb = KeyboardFactory::createKeyboard()
                ->addRow($btnRow1)
                ->addRow($btnRow2)
                ->addRow($btnRow3)
                ->setOneTime(true)
                ->getKeyboardJson();


            $params = [
                'user_id'    => $this->request->object['from_id'],
                'random_id'  => rand(0, 2147483647),
                'message'    => 'Тренеров на данный момент нет',
                'keyboard' => $kb,
            ];
            $this->vkApiClient->messages()->send($this->accessToken, $params);

        }
        else
        {

            $btnRow1 = ButtonRowFactory::createRow()
                ->addButton($btnLike)
                ->addButton($btnDislike)
                ->addButton($btnZzZz)
                ->getRow();


            $kb = KeyboardFactory::createKeyboard()
                ->addRow($btnRow1)
                ->setOneTime(true)
                ->getKeyboardJson();






            $params = [
                'user_id'    => $this->request->object['from_id'],
                'random_id'  => rand(0, 2147483647),
                'message'    => 'Нашел кое-кого для тебя, смотри:
Имя - '.$couple->name.'
Возраст - '.$couple->age.'
Город - '.$couple->city.'
Информация - '.$couple->about_coach,
                'keyboard'  => $kb,
                'attachment' => 'photo'.$couple->photo_id,
            ];

            $this->vkApiClient->messages()->send($this->accessToken, $params);


            $fd = fopen($userId.'-coach'.".txt", 'w') or die("не удалось создать файл");
            $str = $couple->user_id;
            fwrite($fd, $str);
            fclose($fd);


        }



    }



    public function likeCoach(){

        $userId = $this->request->object['from_id'];
        $fd = fopen($userId.'-coach'.".txt", 'r') or die("не удалось открыть файл");
        while(!feof($fd))
        {
            $getCouple = htmlentities(fgets($fd));
        }

        DB::table('likes_coach')->insert([
            ['from_id' => $userId,
                'to_id' => intval($getCouple),
            ],
        ]);

        $getYouLike = DB::table('likes')
            ->where('from_id' ,'=', $userId)
            ->where('to_id' ,'=', $getCouple)
            ->first();



        $user = DB::table('users')
            ->where('user_id','=',$userId)
            ->first();



        if (isset($getYouLike)){
            $params = [
                'user_id'    => $getCouple,
                'random_id'  => rand(0, 2147483647),
                'message'    => 'Пользователь заинтересован в тренировках'."\n\r".
'[id'.$user->user_id.'|'.$user->name.'-'.$user->age.'-'.$user->city.']',
            ];



            $this->vkApiClient->messages()->send($this->accessToken, $params);
        }




        $this->searchCoach();
    }



    public function dislikeCoach(){

        $userId = $this->request->object['from_id'];
        $fd = fopen($userId.'-coach'.".txt", 'r') or die("не удалось открыть файл");
        while(!feof($fd))
        {
            $getCouple = htmlentities(fgets($fd));
        }

        DB::table('dislikes_coach')->insert([
            ['from_id' => $userId,
                'to_id' => intval($getCouple),
            ],
        ]);








        $this->searchCoach();
    }



    public function like(){

        $userId = $this->request->object['from_id'];
        $fd = fopen($userId.".txt", 'r') or die("не удалось открыть файл");
        while(!feof($fd))
        {
            $getCouple = htmlentities(fgets($fd));
        }

        DB::table('likes')->insert([
            ['from_id' => $userId,
                'to_id' => intval($getCouple),
            ],
        ]);

        $getYouLike = DB::table('likes')
            ->where('from_id' ,'=', $userId)
            ->where('to_id' ,'=', $getCouple)
            ->first();
        $getLikesYou = DB::table('likes')
            ->where('from_id' ,'=', $getCouple)
            ->where('to_id' ,'=', $userId)
            ->first();



        if (isset($getYouLike) && isset($getLikesYou)){
            $getCoupleInfo = DB::table('users')
                ->where('user_id','=',$getCouple)
                ->first();
            $getYouInfo = DB::table('users')
                ->where('user_id','=',$userId)
                ->first();
            $params = [
                'user_id'    => $getCouple,
                'random_id'  => rand(0, 2147483647),
                'message'    => $this->botStandartMessages['pair']."\n\r".'[id'.$getYouInfo->user_id.'|'.$getYouInfo->name.'-'.$getYouInfo->age.'-'.$getYouInfo->city.']',
                'attachment'  =>'photo'.$getYouInfo->photo ,

            ];
            $params2 = [
                'user_id'    => $userId,
                'random_id'  => rand(0, 2147483647),
                'message'    => $this->botStandartMessages['pair']."\n\r".'[id'.$getCoupleInfo->user_id.'|'.$getCoupleInfo->name.'-'.$getCoupleInfo->age.'-'.$getCoupleInfo->city.']',
                'attachment'  => 'photo'.$getCoupleInfo->photo,

            ];



            $this->vkApiClient->messages()->send($this->accessToken, $params);
            $this->vkApiClient->messages()->send($this->accessToken, $params2);



        }
        elseif (isset($getYouLike) &&  !isset($getLikesYou)){
            $params = [
                'user_id'    => $getCouple,
                'random_id'  => rand(0, 2147483647),
                'message'    => $this->botStandartMessages['likeMessageCouple'],
            ];



            $this->vkApiClient->messages()->send($this->accessToken, $params);
        }




        $this->startSearch();
    }

    public function dislike(){


        $userId = $this->request->object['from_id'];






        $fd = fopen($userId.".txt", 'r') or die("не удалось открыть файл");
        while(!feof($fd))
        {
            $getCouple = htmlentities(fgets($fd));
        }


        DB::table('dislikes')->insert([
            ['from_id' => $userId,
                'to_id' => intval($getCouple),
            ],
        ]);





        $this->startSearch();
    }


    public function changePhotoProfile()
    {
        $userId = $this->request->object['from_id'];

        Cache::put("dialog_step_$userId", 'notChangeAbout', 10080);

        $Photo = DB::table('users')
            ->select('photo')
            ->where('user_id','=',$userId)
            ->first();


        $btnBackProfile = Button::create(['button' => 'skipPhoto'], $this->botButtonLabels['skipPhoto'], 'primary');




        $btnRow1 = ButtonRowFactory::createRow()
            ->addButton($btnBackProfile)
            ->getRow();



        $kb = KeyboardFactory::createKeyboard()
            ->addRow($btnRow1)
            ->setOneTime(true)
            ->getKeyboardJson();



        if(isset($this->request->object['attachments'][0]['photo']['id']) && ($this->request->object['attachments'][0]['photo']['album_id'] !== -3) ){

            DB::table('users')->where('user_id','=', $userId)
                ->update(['photo' => $userId.'_'.$this->request->object['attachments'][0]['photo']['id']]);
             $this->editAge();
        }
        else{
            $params = [
                'user_id'    => $this->request->object['from_id'],
                'random_id'  => rand(0, 2147483647),
                'message'    => $this->botStandartMessages['changePhotoProfile'],
                'attachment'  => 'photo'.$Photo->photo,
                'keyboard'   => $kb,
            ];
            $this->vkApiClient->messages()->send($this->accessToken, $params);
        }






    }






    public function editName(){
        $userId = $this->request->object['from_id'];


        $btnSkip = Button::create(['button' => 'skipEditName'], $this->botButtonLabels['trueName'], 'primary');



        $btnRow1 = ButtonRowFactory::createRow()
            ->addButton($btnSkip)
            ->getRow();



        $kb = KeyboardFactory::createKeyboard()
            ->addRow($btnRow1)
            ->setOneTime(true)
            ->getKeyboardJson();


        Cache::put("dialog_step_$userId", 'editProfile', 18000);


        $name = DB::table('users')
            ->select('name')
            ->where('user_id','=',$userId)
            ->first();


            if ($this->request->object['text'] == '2'){
                $params = [
                'user_id'    => $this->request->object['from_id'],
                'random_id'  => rand(0, 2147483647),
                'message'    =>$this->botStandartMessages['editName'].$name->name,
                'keyboard'   => $kb,
            ];
                    $this->vkApiClient->messages()->send($this->accessToken, $params);
            }
            else{
                DB::table('users')->where('user_id','=', $userId)
                    ->update(['name' => $this->request->object['text']]);

                $this->editSex();
            }




        }






    public function editAge(){
        $userId = $this->request->object['from_id'];


        $btnSkip = Button::create(['button' => 'skipEditAge'], $this->botButtonLabels['trueAge'], 'primary');



        $btnRow1 = ButtonRowFactory::createRow()
            ->addButton($btnSkip)
            ->getRow();



        $kb = KeyboardFactory::createKeyboard()
            ->addRow($btnRow1)
            ->setOneTime(true)
            ->getKeyboardJson();


        Cache::put("dialog_step_$userId", 'skipPhoto', 10080);


        $getDigitsAge = [
            'user_id'    => $this->request->object['from_id'],
            'random_id'  => rand(0, 2147483647),
            'message'    => $this->botStandartMessages['errorAge'],
            'keyboard'   => $kb,
        ];

        $params = [
            'user_id'    => $this->request->object['from_id'],
            'random_id'  => rand(0, 2147483647),
            'message'    => $this->botStandartMessages['editAge'],

            'keyboard'   => $kb,
        ];


        if ($this->request->object['text'] == 'Пропустить ввод фото') {
            $this->vkApiClient->messages()->send($this->accessToken, $params);
        }
        if((is_int($this->request->object['text'])) || (intval($this->request->object['text']<=15)) || (intval($this->request->object['text']>=60))){
            $this->vkApiClient->messages()->send($this->accessToken, $params);
            $this->vkApiClient->messages()->send($this->accessToken, $getDigitsAge);
        }
        else{
            DB::table('users')->where('user_id','=', $userId)
                ->update(['age' => $this->request->object['text']]);
            $this->changeCity();
        }



    }


    public function editSex(){
        $userId = $this->request->object['from_id'];


        $btnBoy = Button::create(['button' => 'iBoy'], $this->botButtonLabels['boy'], 'primary');
        $btnGirl = Button::create(['button' => 'iGirl'], $this->botButtonLabels['girl'], 'primary');



        $btnRow1 = ButtonRowFactory::createRow()
            ->addButton($btnGirl)
            ->addButton($btnBoy)
            ->getRow();



        $kb = KeyboardFactory::createKeyboard()
            ->addRow($btnRow1)
            ->setOneTime(true)
            ->getKeyboardJson();
        Cache::put("dialog_step_$userId", 'skipEditAge', 10080);

        $params = [
            'user_id'    => $this->request->object['from_id'],
            'random_id'  => rand(0, 2147483647),
            'message'    => $this->botStandartMessages['editSex'],
            'keyboard'  => $kb,
        ];

        $this->vkApiClient->messages()->send($this->accessToken, $params);

    }




    public function editAboutUser(){
        $userId = $this->request->object['from_id'];

        Cache::put("dialog_step_$userId", 'searchAll', 10080);


        $btnNotChange = Button::create(['button' => 'notChangeAbout'], $this->botButtonLabels['notChangeAbout'], 'primary');




        $btnRow1 = ButtonRowFactory::createRow()
            ->addButton($btnNotChange)

            ->getRow();



        $kb = KeyboardFactory::createKeyboard()
            ->addRow($btnRow1)
            ->setOneTime(true)
            ->getKeyboardJson();


        $about = DB::table('users')
            ->select('about_user')
            ->where('user_id','=',$userId)
            ->first();

        $params = [
            'user_id'    => $this->request->object['from_id'],
            'random_id'  => rand(0, 2147483647),
            'message'    => $this->botStandartMessages['editAboutUser'].'-'.$about->about_user,
            'keyboard'   => $kb,
        ];



        switch ($this->request->object['text']){
            case '1':
                DB::table('users')->where('user_id','=', $userId)
                    ->update(['whom_search' => 1]);
                $this->vkApiClient->messages()->send($this->accessToken, $params);
                break;
            case '2':
                DB::table('users')->where('user_id','=', $userId)
                    ->update(['whom_search' => 2]);
                $this->vkApiClient->messages()->send($this->accessToken, $params);
                break;
            case '3':
                DB::table('users')->where('user_id','=', $userId)
                    ->update(['whom_search' => 3]);
                $this->vkApiClient->messages()->send($this->accessToken, $params);
                break;
            default:
                DB::table('users')->where('user_id','=', $userId)
                    ->update(['about_user' => $this->request->object['text']]);
                $this->changePhotoProfile();
                break;

        }



    }


    public function editWhomSearch(){
        $userId = $this->request->object['from_id'];

        Cache::put("dialog_step_$userId", 'editWhomSearch', 10080);

        $btngetGirl = Button::create(['button' => 'searchGirl'], $this->botButtonLabels['1'], 'primary');
        $btngetBoy = Button::create(['button' => 'searchBoy'], $this->botButtonLabels['2'], 'primary');
        $btngetAll = Button::create(['button' => 'searchAll'], $this->botButtonLabels['3'], 'primary');

        $btnRow1 = ButtonRowFactory::createRow()
            ->addButton($btngetGirl)
            ->addButton($btngetBoy)
            ->addButton($btngetAll)
            ->getRow();




        $kb = KeyboardFactory::createKeyboard()
            ->addRow($btnRow1)

            ->setOneTime(false)
            ->getKeyboardJson();


        $params = [
            'user_id'    => $this->request->object['from_id'],
            'random_id'  => rand(0, 2147483647),
            'message'    => $this->botStandartMessages['editWhomSearch'],
            'keyboard'   => $kb,
        ];


        if ($this->request->object['text'] == 'Девушка'){
            DB::table('users')->where('user_id','=', $userId)
                ->update(['sex' => 1]);
            $this->vkApiClient->messages()->send($this->accessToken, $params);
        }
        elseif ($this->request->object['text'] == 'Парень'){
            DB::table('users')->where('user_id','=', $userId)
                ->update(['sex' => 2]);
            $this->vkApiClient->messages()->send($this->accessToken, $params);
        }




    }



    public function dontSearch(){
        $userId = $this->request->object['from_id'];

        Cache::put("dialog_step_$userId", 'dontSearch', 10080);

        $btnSearchAgain = Button::create(['button' => 'SearchAgain'], $this->botButtonLabels['1'], 'primary');
        $btnCheckProfile = Button::create(['button' => 'CheckProfile'], $this->botButtonLabels['2'], 'primary');
        $btnInactive = Button::create(['button' => 'inactive'], $this->botButtonLabels['3'], 'primary');
        $btnRow1 = ButtonRowFactory::createRow()
            ->addButton($btnSearchAgain)
            ->addButton($btnCheckProfile)
            ->addButton($btnInactive)
            ->getRow();



        $kb = KeyboardFactory::createKeyboard()
            ->addRow($btnRow1)
            ->setOneTime(true)
            ->getKeyboardJson();


        $params = [
            'user_id'    => $this->request->object['from_id'],
            'random_id'  => rand(0, 2147483647),
            'message'    => $this->botStandartMessages['dontSearch'],
            'keyboard'  => $kb,
        ];

        $this->vkApiClient->messages()->send($this->accessToken, $params);

    }



    public function changeCity(){
        $userId = $this->request->object['from_id'];

        Cache::put("dialog_step_$userId", 'skipEditAge', 10080);

        $btngetCity = Button::create(['button' => 'skipCity'], $this->botButtonLabels['skipCity'], 'primary');


        $btnRow1 = ButtonRowFactory::createRow()
            ->addButton($btngetCity)
            ->getRow();


        $city = DB::table('users')
            ->select('city')
            ->where('user_id','=' ,$userId )
            ->first();

        $kb = KeyboardFactory::createKeyboard()
            ->addRow($btnRow1)

            ->setOneTime(false)
            ->getKeyboardJson();





        $params = [
            'user_id'    => $this->request->object['from_id'],
            'random_id'  => rand(0, 2147483647),
            'message'    => $this->botStandartMessages['getCity'].$city->city,
            'keyboard'   => $kb,
        ];

        if (($this->request->object['text'] =='Пропустить ввод возраста') || intval($this->request->object['text'])   ){
            $this->vkApiClient->messages()->send($this->accessToken, $params);
        }

        else{
            DB::table('users')->where('user_id','=', $userId)
                ->update(['city' => $this->request->object['text']]);
                $this->viewProfile();
        }



    }

    public function inactive(){
        $userId = $this->request->object['from_id'];

        Cache::put("dialog_step_$userId", 'inactive', 10080);

        $btnStart = Button::create(['button' => 'getProfile'], $this->botButtonLabels['getProfile'], 'primary');
        $btnOpenTrainning = Button::create(['button' => 'OpenTraining'], $this->botButtonLabels['OpenTraining'], 'primary');
        $delete = Button::create(['button' => 'delete'], 'Удалить Анкету', 'primary');
        $btnRow1 = ButtonRowFactory::createRow()
            ->addButton($btnStart)

            ->getRow();

        $btnRow2 = ButtonRowFactory::createRow()
            ->addButton($delete)
            ->getRow();

        $btnRow3 = ButtonRowFactory::createRow()
            ->addButton($btnOpenTrainning)
            ->getRow();

        $kb = KeyboardFactory::createKeyboard()
            ->addRow($btnRow1)
            ->addRow($btnRow2)
            ->addRow($btnRow3)
            ->setOneTime(true)
            ->getKeyboardJson();


        $params = [
            'user_id'    => $this->request->object['from_id'],
            'random_id'  => rand(0, 2147483647),
            'message'    => $this->botStandartMessages['inactive'],
            'keyboard'  => $kb,
        ];

        $this->vkApiClient->messages()->send($this->accessToken, $params);

    }

    public function delete()
    {
        $userId = $this->request->object['from_id'];




        Cache::put("dialog_step_$userId", 'delete', 10080);
        DB::table('users')
            ->where('user_id','=',$userId)
            ->delete();

        $btnStart = Button::create(['button' => 'start'], $this->botButtonLabels['start'], 'primary');
        $btnRow1 = ButtonRowFactory::createRow()
            ->addButton($btnStart)
            ->getRow();
        $kb = KeyboardFactory::createKeyboard()
            ->addRow($btnRow1)
            ->setOneTime(true)
            ->getKeyboardJson();


        $params = [
            'user_id'    => $this->request->object['from_id'],
            'random_id'  => rand(0, 2147483647),
            'message'    => 'Надеюсь ты нашел кого-то благодаря мне! Рад был с тобой пообщаться, будет скучно – пиши, обязательно найдем тебе кого-нибудь',
            'keyboard'   => $kb,
        ];

        $this->vkApiClient->messages()->send($this->accessToken, $params);
    }



    public function OpenTraining()
    {
        $userId = $this->request->object['from_id'];

        Cache::put("dialog_step_$userId", '', 10080);

        $btnStart = Button::create(['button' => 'start'], $this->botButtonLabels['start'], 'primary');
        $btnRow1 = ButtonRowFactory::createRow()
            ->addButton($btnStart)
            ->getRow();

        $video = Button::create(['button' => 'video'], $this->botButtonLabels['video'], 'primary');
        $btnView = Button::create(['button' => 'getProfile'], $this->botButtonLabels['getProfile'], 'primary');

        $btnRow2 = ButtonRowFactory::createRow()
            ->addButton($video)
            ->getRow();

        $btnRow3 = ButtonRowFactory::createRow()
            ->addButton($btnView)
            ->getRow();



        $kb = KeyboardFactory::createKeyboard()
            ->addRow($btnRow1)
            ->addRow($btnRow2)
            ->addRow($btnRow3)
            ->setOneTime(true)
            ->getKeyboardJson();


        $getMessage = DB::table('trainning')
            ->OrderBy('date')
            ->first();



        if (isset($getMessage->name)){
            $params = [
                'user_id'    => $this->request->object['from_id'],
                'random_id'  => rand(0, 2147483647),
                'message'    => 'Ближайшая открытая тренировка
Название тренировки - '.$getMessage->name.'
Дата - '.$getMessage->date.'
Стиль тренировки - '.$getMessage->style.'
Тренер - '.$getMessage->coach.'
Адрес - '.$getMessage->address,
                'keyboard'  => $kb,
                'lat'       => $getMessage->lat_map,
                'long'      => $getMessage->long_map,
            ];

            $this->vkApiClient->messages()->send($this->accessToken, $params);
        }
        else{
            $params = [
                'user_id'    => $this->request->object['from_id'],
                'random_id'  => rand(0, 2147483647),
                'message'    => 'Ближайших тренировок нет',
            ];

            $this->vkApiClient->messages()->send($this->accessToken, $params);
        }







    }



    public function getVideoCategory(){
        $userId = $this->request->object['from_id'];

        Cache::put("dialog_step_$userId", 'video', 10080);


        $video = DB::table('categories_videos')
            ->select('name')
            ->get();


        $buttons = array();

        for ($index = 0; $index<count($video); $index++){

        $id = $index+1;
            $btn[] = Button::create(['button' => 'Category'.$id], $video[$index]->name, 'primary');
        array_push($buttons,$btn[$index]);
        }






        $btnOpenTrainning = Button::create(['button' => 'OpenTraining'], $this->botButtonLabels['OpenTraining'], 'primary');
        $btnView = Button::create(['button' => 'getProfile'], $this->botButtonLabels['getProfile'], 'primary');

        $btnRow1 = ButtonRowFactory::createRow()
            ->addButton($buttons)
            ->getRow();



        $btnRow2 = ButtonRowFactory::createRow()
            ->addButton($btnOpenTrainning)

            ->getRow();

        $btnRow3 = ButtonRowFactory::createRow()
            ->addButton($btnView)
            ->getRow();



        $kb = KeyboardFactory::createKeyboard()
            ->addRow($buttons)
            ->addRow($btnRow2)
            ->addRow($btnRow3)
            ->setOneTime(true)
            ->getKeyboardJson();


        $params = [
            'user_id'    => $this->request->object['from_id'],
            'random_id'  => rand(0, 2147483647),
            'message'    => $this->botStandartMessages['video'],
            'keyboard'  => $kb,
        ];



            $this->vkApiClient->messages()->send($this->accessToken, $params);

    }


    public function getVideo(int $category){

        $userId = $this->request->object['from_id'];

        Cache::put("dialog_step_$userId",'getCategory' , 10080);

        $video = DB::table('videos')
            ->where('category_id','=',$category)
            ->get();

        $videoCategory = DB::table('categories_videos')
            ->where('name','=',$this->request->object['text'])
            ->first();


        $btnOpenTrainning = Button::create(['button' => 'OpenTraining'], $this->botButtonLabels['OpenTraining'], 'primary');
        $btnView = Button::create(['button' => 'getProfile'], $this->botButtonLabels['getProfile'], 'primary');

        $buttons = array();

        for ($index = 0; $index<count($video); $index++){

            $id = $index+1;
            $btn[] = Button::create(['button' => 'video'.'-'.$category.'-'.$id], $index+1, 'primary');
            array_push($buttons,$btn[$index]);
        }

        log::info(print_r($buttons,true));



        $btnRow2 = ButtonRowFactory::createRow()
            ->addButton($btnOpenTrainning)

            ->getRow();

        $btnRow3 = ButtonRowFactory::createRow()
            ->addButton($btnView)
            ->getRow();



        $kb = KeyboardFactory::createKeyboard()
            ->addRow($buttons)
            ->addRow($btnRow2)
            ->addRow($btnRow3)
            ->setOneTime(true)
            ->getKeyboardJson();





            $params = [
                'user_id'   => $this->request->object['from_id'],
                'random_id' => rand(0, 2147483647),
                'message'   => 'Видео курса- '.$videoCategory->name.'
Количество выпусков -'.count($video).'
Для просмотра введите номер выпуска
            ',
                'keyboard' => $kb,

            ];
            $this->vkApiClient->messages()->send($this->accessToken, $params);

       }



        public function getVideoAttach($categoryId, $videos){
            $userId = $this->request->object['from_id'];
            Cache::put("dialog_step_$userId",'getAttachVideo' , 10080);
            $btnOpenTrainning = Button::create(['button' => 'OpenTraining'], $this->botButtonLabels['OpenTraining'], 'primary');
            $btnView = Button::create(['button' => 'getProfile'], $this->botButtonLabels['getProfile'], 'primary');

            $video = DB::table('videos')
                ->where('category_id','=',$categoryId)
                ->where('release','=',$videos)
                ->first();

            log::info(print_r($video,true));

            $btnRow2 = ButtonRowFactory::createRow()
                ->addButton($btnOpenTrainning)

                ->getRow();

            $btnRow3 = ButtonRowFactory::createRow()
                ->addButton($btnView)
                ->getRow();



            $kb = KeyboardFactory::createKeyboard()

                ->addRow($btnRow2)
                ->addRow($btnRow3)
                ->setOneTime(true)
                ->getKeyboardJson();



            $params1 = [
                'user_id'   => $userId,
                'random_id' => rand(0, 2147483647),
                'message'   => '',
                'attachment' => 'video-'.$video->id_videos,
                'keyboard' => $kb,

            ];
            $this->vkApiClient->messages()->send($this->accessToken, $params1);
        }


    public function getCouples(){
        $userId = $this->request->object['from_id'];
        $btnView = Button::create(['button' => 'getProfile'], $this->botButtonLabels['getProfile'], 'primary');


        $likesYou = DB::table('users')
            ->select('user_id','name','age')
            ->leftJoin('likes', 'users.user_id', '=', 'likes.from_id')
            ->where('likes.to_id', $userId)
            ->get();


        $couples = DB::table('users')
            ->select('user_id','name','age')
            ->leftJoin('likes', function($join) {
                $join->on('users.user_id', '=', 'likes.to_id');
            })
            ->where('likes.from_id','=',$userId)
            ->limit(10)
            ->get();

        $fd = fopen('couples'.$userId.".txt", 'w') or die("не удалось создать файл");



          for ($index = 0; $index < count($likesYou);$index++ ){
              for ($indexLikes = 0; $indexLikes < count($couples);$indexLikes++ ){
                  if ($likesYou[$index]->user_id == $couples[$indexLikes]->user_id){
                      $str = '[id'.$likesYou[$index]->user_id.'|'.$likesYou[$index]->name.'-'.$likesYou[$index]->age.']'."\r\n";
                      fwrite($fd, $str);
                  }
              }
          }
        fclose($fd);










        $btnRow1 = ButtonRowFactory::createRow()
            ->addButton($btnView)
            ->getRow();

        $currnetOpenTraining = Button::create(['button' => 'OpenTraining'], $this->botButtonLabels['OpenTraining'], 'primary');
        $video = Button::create(['button' => 'video'], $this->botButtonLabels['video'], 'primary');




        $btnRow2 = ButtonRowFactory::createRow()
            ->addButton($currnetOpenTraining)
            ->getRow();
        $btnRow3 = ButtonRowFactory::createRow()
            ->addButton($video)
            ->getRow();



        $kb = KeyboardFactory::createKeyboard()
            ->addRow($btnRow1)
            ->addRow($btnRow2)
            ->addRow($btnRow3)
            ->setOneTime(false)
            ->getKeyboardJson();

        log::info(print_r($couples,true));

        if (isset($couples[0]) ){
            $params = [
                'user_id'   => $this->request->object['from_id'],
                'random_id' => rand(0, 2147483647),
                'message'   => 'Ваши пары :
'.file_get_contents('couples'.$userId.".txt"),
                'keyboard'  => $kb,
            ];






            $this->vkApiClient->messages()->send($this->accessToken, $params);
        }
        else{
            $params = [
                'user_id'   => $this->request->object['from_id'],
                'random_id' => rand(0, 2147483647),
                'message'   => 'Пар нет',
                'keyboard'  => $kb,
            ];


            $this->vkApiClient->messages()->send($this->accessToken, $params);
        }







    }



    public function getLikesCoach(){
        $userId = $this->request->object['from_id'];
        $btnView = Button::create(['button' => 'getProfile'], $this->botButtonLabels['getProfile'], 'primary');





        $btnRow1 = ButtonRowFactory::createRow()
            ->addButton($btnView)
            ->getRow();

        $currnetOpenTraining = Button::create(['button' => 'OpenTraining'], $this->botButtonLabels['OpenTraining'], 'primary');
        $video = Button::create(['button' => 'video'], $this->botButtonLabels['video'], 'primary');




        $btnRow2 = ButtonRowFactory::createRow()
            ->addButton($currnetOpenTraining)
            ->getRow();
        $btnRow3 = ButtonRowFactory::createRow()
            ->addButton($video)
            ->getRow();



        $kb = KeyboardFactory::createKeyboard()
            ->addRow($btnRow1)
            ->addRow($btnRow2)
            ->addRow($btnRow3)
            ->setOneTime(false)
            ->getKeyboardJson();


            $couples = DB::table('coach')
                ->select('user_id','name','age')
                ->limit(10)
                ->leftJoin('likes_coach', function($join) {
                    $join->on('coach.user_id', '=', 'likes_coach.to_id');
                })
                ->where('likes_coach.from_id','=',$userId)

                ->get();

        $fd = fopen('likes-coach'.$userId.".txt", 'w') or die("не удалось создать файл");
            for ($index = 0; $index<count($couples);$index++){
                $str = '[id'.$couples[$index]->user_id.'|'.$couples[$index]->name.'-'.$couples[$index]->age.']'."\r\n";
                fwrite($fd, $str);
            }

        fclose($fd);



if (isset($couples[0])){
    $params1 = [
        'user_id'   => $this->request->object['from_id'],
        'random_id' => rand(0, 2147483647),
        'message'   => 'Ваши фаворитные тренеры : 
'.file_get_contents('likes-coach'.$userId.".txt"),
        'keyboard'  => $kb,
    ];

    $this->vkApiClient->messages()->send($this->accessToken, $params1);
}
else{
    $params1 = [
        'user_id'   => $this->request->object['from_id'],
        'random_id' => rand(0, 2147483647),
        'message'   => 'Фаворитных тренеров нет',
        'keyboard'  => $kb,
    ];

    $this->vkApiClient->messages()->send($this->accessToken, $params1);
}






    }





    public function getUsersCoach(){
        $userId = $this->request->object['from_id'];
        $btnView = Button::create(['button' => 'getProfile'], $this->botButtonLabels['getProfile'], 'primary');





        $btnRow1 = ButtonRowFactory::createRow()
            ->addButton($btnView)
            ->getRow();

        $currnetOpenTraining = Button::create(['button' => 'OpenTraining'], $this->botButtonLabels['OpenTraining'], 'primary');
        $video = Button::create(['button' => 'video'], $this->botButtonLabels['video'], 'primary');




        $btnRow2 = ButtonRowFactory::createRow()
            ->addButton($currnetOpenTraining)
            ->getRow();
        $btnRow3 = ButtonRowFactory::createRow()
            ->addButton($video)
            ->getRow();



        $kb = KeyboardFactory::createKeyboard()
            ->addRow($btnRow1)
            ->addRow($btnRow2)
            ->addRow($btnRow3)
            ->setOneTime(false)
            ->getKeyboardJson();


        $couples = DB::table('likes_coach')
                ->where('to_id','=',$userId)
                 ->get();


        log::info(print_r($couples,true));

        $fd = fopen('likes-coach'.$userId.".txt", 'w') or die("не удалось создать файл");
        for ($index = 0; $index<count($couples);$index++){
            $getUsers = DB::table('users')
                ->where('user_id','=',$couples[$index]->from_id)
                ->first();
            $str = '[id'.$getUsers->user_id.'|'.$getUsers->name.'-'.$getUsers->age.']'."\r\n";
            fwrite($fd, $str);
        }

        fclose($fd);



        if (isset($couples[0])){
            $params1 = [
                'user_id'   => $this->request->object['from_id'],
                'random_id' => rand(0, 2147483647),
                'message'   => 'Люди которым понравилась Ваша анкета тренера : 
'.file_get_contents('likes-coach'.$userId.".txt"),
                'keyboard'  => $kb,
            ];

            $this->vkApiClient->messages()->send($this->accessToken, $params1);
        }
        else{
            $params1 = [
                'user_id'   => $this->request->object['from_id'],
                'random_id' => rand(0, 2147483647),
                'message'   => 'Людей,которым понравилась Ваша анкета тренера - нет',
                'keyboard'  => $kb,
            ];

            $this->vkApiClient->messages()->send($this->accessToken, $params1);
        }






    }








        public function defaultResponse()
    {



        $params = [
            'user_id'   => $this->request->object['from_id'],
            'random_id' => rand(0, 2147483647),
            'message'   => $this->botStandartMessages['default_message'],
        ];

        $this->vkApiClient->messages()->send($this->accessToken, $params);
    }
}