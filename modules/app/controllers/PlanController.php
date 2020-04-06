<?php 

namespace app\modules\app\controllers;

use app\modules\cms\services\PlanService;
use yii\web\Controller;

class PlanController extends Controller
{
    public $enableCsrfValidation = false;
    /**-------------VIEWS-----------------*/
    public function actionIndex() {
        return $this->render('index');
    }

    /**-------------API-----------------*/
    public function actionGetList($page = 1, $perpage = 9, $destination = 13, $comment = 1, $rating = 0) {
        list($plans, $pagination) = PlanService::GetListAppPage($page, $perpage, $destination, $comment, $rating);
        $response = [
            'status' => true,
            'plans' => $plans,
            'pagination' => $pagination
        ];
        return $this->asJson($response);
    }
}