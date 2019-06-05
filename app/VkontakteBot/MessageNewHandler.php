<?php
namespace App\VkontakteBot;

use App\VkontakteBot\Response\ActionResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use VK\Client\VKApiClient;

abstract class MessageNewHandler implements RequestTypeHandlerInterface
{

    public static function handle(Request $request)
    {
        $userId = $request->object['from_id'];


        $dialogStep = Cache::remember("dialog_step_$userId", 10080,
            function () use ($userId) {
                return 'start';
            });

        if (isset($request->object['payload'])) {
            $payload = json_decode($request->object['payload'], true);
            if (isset($payload['button'])) {
                $dialogStep = $payload['button'];
            }
        }

        $actionResponse = new ActionResponse($request, new VKApiClient('5.92'),
            env('VK_SECRET_KEY_GROUP'));




        


       switch ($dialogStep) {
            case 'start':
                $actionResponse->start();
                break;





           case 'getGirl':
               $actionResponse->getGirlClick();
               break;

           case 'getBoy':
               $actionResponse->getBoyClick();
               break;

           case 'getAll':
               $actionResponse->getAllSearchClick();
               break;

           case 'addInfo':
               $actionResponse->addInfoUser();
               break;
           case 'skip':
               $actionResponse->viewProfile();
               break;



           case 'getCouples':
               $actionResponse->getCouples();
               break;

           case 'getLikesUser':
               $actionResponse->getLikesUser();
               break;

           case 'editProfile':
               $actionResponse->editName();
               break;
           case 'skipEditName':
               $actionResponse->editSex();
               break;
           case 'skipEditAge':
               $actionResponse->changeCity();
               break;

           case 'iGirl':
               $actionResponse->editWhomSearch();
               break;
           case 'iBoy':
               $actionResponse->editWhomSearch();
               break;

           case 'searchGirl':
               $actionResponse->editAboutUser();
               break;
           case 'searchBoy':
               $actionResponse->editAboutUser();
               break;
           case 'searchAll':
               $actionResponse->editAboutUser();
               break;

           case 'notChangeAbout':
               $actionResponse->changePhotoProfile();
               break;

           case 'skipPhoto':
               $actionResponse->editAge();
               break;



           case 'skipCity':
               $actionResponse->viewProfile();
               break;

           case 'trueProfile':

               $actionResponse->startSearch();

               break;
           case 'like':
               $actionResponse->like( );
               break;


           case 'video':
               $actionResponse->getVideoCategory();
               break;


           case 'Category1':
               $actionResponse->getVideo(1);
               break;
           case 'Category2':
               $actionResponse->getVideo(2);
               break;

           case 'Category3':
               $actionResponse->getVideo(3);
               break;

           case 'Category4':
               $actionResponse->getVideo(4);
               break;
           case 'Category5':
               $actionResponse->getVideo(5);
               break;

           case 'Category6':
               $actionResponse->getVideo(6);
               break;

           case 'Category7':
               $actionResponse->getVideo(7);
               break;
           case 'Category8':
               $actionResponse->getVideo(8);
               break;
           case 'Category9':
               $actionResponse->getVideo(9);
               break;

           case 'video-1-1':
               $actionResponse->getVideoAttach(1,1);
               break;

           case 'video-1-2':
               $actionResponse->getVideoAttach(1,2);
               break;
           case 'video-1-3':
               $actionResponse->getVideoAttach(1,3);
               break;

           case 'video-1-4':
               $actionResponse->getVideoAttach(1,4);
               break;

           case 'video-1-5':
               $actionResponse->getVideoAttach(1,5);
               break;

           case 'video-1-6':
               $actionResponse->getVideoAttach(1,6);
               break;
           case 'video-1-7':
               $actionResponse->getVideoAttach(1,7);
               break;

           case 'video-1-8':
               $actionResponse->getVideoAttach(1,8);
               break;

           case 'video-1-9':
               $actionResponse->getVideoAttach(1,9);
               break;


           case 'video-2-1':
               $actionResponse->getVideoAttach(2,1);
               break;

           case 'video-2-2':
               $actionResponse->getVideoAttach(2,2);
               break;
           case 'video-2-3':
               $actionResponse->getVideoAttach(2,3);
               break;

           case 'video-2-4':
               $actionResponse->getVideoAttach(2,4);
               break;

           case 'video-2-5':
               $actionResponse->getVideoAttach(2,5);
               break;

           case 'video-2-6':
               $actionResponse->getVideoAttach(2,6);
               break;
           case 'video-2-7':
               $actionResponse->getVideoAttach(2,7);
               break;

           case 'video-2-8':
               $actionResponse->getVideoAttach(2,8);
               break;

           case 'video-2-9':
               $actionResponse->getVideoAttach(2,9);
               break;

           case 'video-3-1':
               $actionResponse->getVideoAttach(3,1);
               break;

           case 'video-3-2':
               $actionResponse->getVideoAttach(3,2);
               break;
           case 'video-3-3':
               $actionResponse->getVideoAttach(3,3);
               break;

           case 'video-3-4':
               $actionResponse->getVideoAttach(3,4);
               break;

           case 'video-3-5':
               $actionResponse->getVideoAttach(3,5);
               break;

           case 'video-3-6':
               $actionResponse->getVideoAttach(3,6);
               break;
           case 'video-3-7':
               $actionResponse->getVideoAttach(3,7);
               break;

           case 'video-3-8':
               $actionResponse->getVideoAttach(3,8);
               break;

           case 'video-3-9':
               $actionResponse->getVideoAttach(3,9);
               break;
           case 'video-4-1':
               $actionResponse->getVideoAttach(4,1);
               break;

           case 'video-4-2':
               $actionResponse->getVideoAttach(4,2);
               break;
           case 'video-4-3':
               $actionResponse->getVideoAttach(4,3);
               break;

           case 'video-4-4':
               $actionResponse->getVideoAttach(4,4);
               break;

           case 'video-4-5':
               $actionResponse->getVideoAttach(4,5);
               break;

           case 'video-4-6':
               $actionResponse->getVideoAttach(4,6);
               break;
           case 'video-4-7':
               $actionResponse->getVideoAttach(4,7);
               break;

           case 'video-4-8':
               $actionResponse->getVideoAttach(4,8);
               break;

           case 'video-4-9':
               $actionResponse->getVideoAttach(4,9);
               break;

           case 'video-5-1':
               $actionResponse->getVideoAttach(5,1);
               break;

           case 'video-5-2':
               $actionResponse->getVideoAttach(5,2);
               break;
           case 'video-5-3':
               $actionResponse->getVideoAttach(5,3);
               break;

           case 'video-5-4':
               $actionResponse->getVideoAttach(5,4);
               break;

           case 'video-5-5':
               $actionResponse->getVideoAttach(5,5);
               break;

           case 'video-5-6':
               $actionResponse->getVideoAttach(5,6);
               break;
           case 'video-5-7':
               $actionResponse->getVideoAttach(5,7);
               break;

           case 'video-5-8':
               $actionResponse->getVideoAttach(5,8);
               break;

           case 'video-5-9':
               $actionResponse->getVideoAttach(5,9);
               break;



           case 'ZzZz':
               $actionResponse->inactive();

               break;

           case 'getProfile':
               $actionResponse->viewProfile();
               break;



           case 'dislike':
               $actionResponse->dislike();
               break;
           case 'OpenTraining':
               $actionResponse->OpenTraining();
               break;
           case 'CheckProfile':
               $actionResponse->viewProfile();
               break;
           case 'startAgain':
               $actionResponse->startSearch();
               break;
           case 'delete':
               $actionResponse->delete();
               break;

           case  'searchCoach':
               $actionResponse->searchCoach();
               break;

           case  'likeCoach':
               $actionResponse->likeCoach();
               break;
           case  'dislikeCoach':
               $actionResponse->dislikeCoach();
               break;
           case 'likesCoach':
               $actionResponse->getLikesCoach();
               break;

           case 'getUsersCoach':
               $actionResponse->getUsersCoach();
               break;



       }






    }
}