<?php 

namespace app\modules\app\controllers;

use app\modules\cms\services\DestinationService;
use yii\web\Controller;

class PlaceController extends Controller
{
    public $enableCsrfValidation = false;
    /**-------------VIEWS-----------------*/
    public function actionVisit() {
        return $this->render('visit');
    }

    public function actionFood() {
        return $this->render('food');
    }

    public function actionRest() {
        return $this->render('rest');
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