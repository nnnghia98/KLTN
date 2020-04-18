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

    public function actionVisitMap() {
        return $this->render('visit-map');
    }

    public function actionFood() {
        return $this->render('food');
    }

    public function actionFoodMap() {
        return $this->render('food-map');
    }

    public function actionRest() {
        return $this->render('rest');
    }

    public function actionRestMap() {
        return $this->render('rest-map');
    }
 
    /**-------------API-----------------*/
    public function actionGetFoodList($page = 1, $perpage = 9, $keyword = '', $destination = 13, $comment = 1, $rating = 0) {
        list($foods, $pagination) = PlaceService::GetFoodListAppPage($page, $perpage, $keyword, $comment, $rating, $destination);
        $response = [
            'status' => true,
            'foods' => $foods,
            'pagination' => $pagination
        ];

        return $this->asJson($response);
    }

    public function actionGetVisitList($page = 1, $perpage = 9, $keyword = '', $destination = 13, $comment = 1, $rating = 0) {
        list($visits, $pagination) = PlaceService::GetVisitListAppPage($page, $perpage, $keyword, $comment, $rating, $destination);
        $response = [
            'status' => true,
            'visits' => $visits,
            'pagination' => $pagination
        ];

        return $this->asJson($response);
    }

    public function actionGetRestList($page = 1, $perpage = 9, $keyword = '', $destination = 13, $comment = 1, $rating = 0) {
        list($rests, $pagination) = PlaceService::GetRestListAppPage($page, $perpage, $keyword, $comment, $rating, $destination);
        $response = [
            'status' => true,
            'rests' => $rests,
            'pagination' => $pagination
        ];

        return $this->asJson($response);
    }

    public function actionGetList($page = 1, $perpage = 9, $keyword = '', $destination = 13, $type = '', $sort = 'avg_rating', $lat = '', $lng = '') {
        list($places, $pagination) = PlaceService::GetPlacesAppPage($page, $perpage, $keyword, $destination, $type, $sort, $lat, $lng);
        $response = [
            'status' => true,
            'places' => $places,
            'pagination' => $pagination
        ];

        return $this->asJson($response);
    }
}