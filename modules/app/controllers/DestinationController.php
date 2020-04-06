<?php 

namespace app\modules\app\controllers;

use app\modules\cms\services\DestinationService;
use yii\web\Controller;

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

    /**-------------API-----------------*/
    public function actionGetList($page = 1, $perpage = 9, $keyword = '', $comment = 1, $rating = 0) {
        list($destinations, $pagination) = DestinationService::GetListAppPage($page, $perpage, $keyword, $comment, $rating);
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
}