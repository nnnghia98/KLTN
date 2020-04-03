<?php 

namespace app\modules\app\controllers;

use app\modules\app\APPConfig;
use app\modules\cms\models\AuthUser;
use app\modules\cms\services\AuthService;
use app\modules\cms\services\DestinationService;
use app\modules\cms\services\TravelSharingService;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class DestinationController extends Controller
{
    public $enableCsrfValidation = false;
    /**-------------VIEWS-----------------*/
    public function actionIndex() {
        return $this->render('index');
    }

    /**-------------API-----------------*/
    public function actionGetList($page = 1, $perpage = 9, $keyword = '', $comment = 1, $rating = 0) {
        list($destinations, $pagination) = DestinationService::GetListAppPage($page, $perpage, $keyword, $comment, $rating);
        $response = [
            'status' => true,
            'destinations' => $destinations,
            'pagination' => $pagination
        ];
        // if($destinations) {
            
        // } else {
        //     $response = [
        //         'status' => false,
        //         'message' => DestinationService::$RESPONSE['ERROR_LIST'],
        //     ];
        // }

        return $this->asJson($response);
    }
}