<?php 

namespace app\modules\app\controllers;

use app\modules\cms\models\Destination;
use app\modules\cms\services\DestinationService;
use app\modules\cms\services\FileService;
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

    public function actionMap() {
        return $this->render('map');
    }

    public function actionDetail($slug) {
        $destination = DestinationService::GetDestinationBySlug($slug);
        return $this->render('detail', compact('destination'));
    }

    /**-------------API-----------------*/
    public function actionGetList($page = 1, $perpage = 9, $keyword = '', $order = 'rating-desc') {
        list($destinations, $pagination) = DestinationService::GetListAppPage($page, $perpage, $keyword, $order);
        $response = [
            'status' => true,
            'destinations' => $destinations,
            'pagination' => $pagination
        ];
        return $this->asJson($response);
    }

    public function actionGetCategories() {
        $categories = DestinationService::GetCategories();
        $response = [
            'status' => true,
            'categories' => $categories
        ];
        return $this->asJson($response);
    }

    public function actionGetImages($id) {
        $images = FileService::GetObjectImages(DestinationService::$OBJECT_TYPE, $id);
        $response = [
            'status' => true, 
            'images' => $images
        ];
        return $this->asJson($response);
    }

    public function actionGetTopPlaces($id, $type) {
        $places = DestinationService::GetTopPlaces($id, $type);
        $response = [
            'status' => true, 
            'places' => $places
        ];
        return $this->asJson($response);
    }

    public function actionGetNewestPlans($id) {
        $plans = DestinationService::GetNewestPlans($id);
        $response = [
            'status' => true, 
            'plans' => $plans
        ];
        return $this->asJson($response);
    }

    public function actionGetComments($id, $page) {
        $comments = DestinationService::GetComments($id, $page, 10);
        $response = [
            'status' => true, 
            'comments' => $comments
        ];
        return $this->asJson($response);
    }
   
    public function actionGetInteractive($id) {
        $interactive = DestinationService::GetInteractiveOfCurrentUser($id);
        $response = [
            'status' => true, 
            'interactive' => $interactive
        ];
        return $this->asJson($response);
    }

    public function actionSubmitComment() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $result = DestinationService::SubmitComment($request->post());
            if($result === true) {
                return $this->asJson(['status' => true]);
            }
            
            return $this->asJson(['status' => true, 'message' => $result]);
        }

        throw new NotFoundHttpException();
    }
}