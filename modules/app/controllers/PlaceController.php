<?php 

namespace app\modules\app\controllers;

use app\modules\cms\services\PlaceService;
use app\modules\cms\services\FileService;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

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

    public function actionDetail($slug) {
        $place = PlaceService::GetPlaceBySlug($slug);
        return $this->render('detail', compact('place'));
    }
 
    /**-------------API-----------------*/
    public function actionGetPlaceList($page = 1, $perpage = 9, $keyword = '', $destination = 13, $order = 'rating-desc', $type = 1) {
        list($places, $pagination) = PlaceService::GetPlaceListAppPage($page, $perpage, $keyword, $destination, $order, $type);
        $response = [
            'status' => true,
            'places' => $places,
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

    public function actionGetImages($id) {
        $images = FileService::GetObjectImages(PlaceService::$OBJECT_TYPE, $id);
        $response = [
            'status' => true, 
            'images' => $images
        ];
        return $this->asJson($response);
    }

    public function actionGetNearbyPlaces($lat, $lng, $type) {
        $places = PlaceService::GetNearbyPlaces($lat, $lng, $type);
        $response = [
            'status' => true, 
            'places' => $places
        ];
        return $this->asJson($response);
    }

    public function actionGetRelatePlans($id) {
        $plans = PlaceService::GetRelatePlans($id);
        $response = [
            'status' => true, 
            'plans' => $plans
        ];
        return $this->asJson($response);
    }

    public function actionGetComments($id, $page) {
        $comments = PlaceService::GetComments($id, $page, 10);
        $response = [
            'status' => true, 
            'comments' => $comments
        ];
        return $this->asJson($response);
    }

    public function actionGetInteractive($id) {
        $interactive = PlaceService::GetInteractiveOfCurrentUser($id);
        $response = [
            'status' => true, 
            'interactive' => $interactive
        ];
        return $this->asJson($response);
    }

    public function actionSubmitComment() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $result = PlaceService::SubmitComment($request->post());
            if($result === true) {
                return $this->asJson(['status' => true]);
            }
            
            return $this->asJson(['status' => true, 'message' => $result]);
        }

        throw new NotFoundHttpException();
    }
}