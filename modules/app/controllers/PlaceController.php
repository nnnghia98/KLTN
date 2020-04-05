<?php 

namespace app\modules\app\controllers;

use app\modules\cms\services\DestinationService;
use app\modules\cms\services\PlaceService;
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
    public function actionGetFoodList($page = 1, $perpage = 9, $keyword = '', $comment = 1, $rating = 0) {
        list($foods, $pagination) = PlaceService::GetFoodListAppPage($page, $perpage, $keyword, $comment, $rating);
        $response = [
            'status' => true,
            'foods' => $foods,
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

    public function actionGetVisitList($page = 1, $perpage = 9, $keyword = '', $comment = 1, $rating = 0) {
        list($visits, $pagination) = PlaceService::GetVisitListAppPage($page, $perpage, $keyword, $comment, $rating);
        $response = [
            'status' => true,
            'visits' => $visits,
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

    public function actionGetRestList($page = 1, $perpage = 9, $keyword = '', $comment = 1, $rating = 0) {
        list($rests, $pagination) = PlaceService::GetRestListAppPage($page, $perpage, $keyword, $comment, $rating);
        $response = [
            'status' => true,
            'rests' => $rests,
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