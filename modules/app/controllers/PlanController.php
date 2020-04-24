<?php 

namespace app\modules\app\controllers;

use app\modules\app\APPConfig;
use app\modules\cms\services\PlanService;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class PlanController extends Controller
{
    public $enableCsrfValidation = false;
    /**-------------VIEWS-----------------*/
    public function actionIndex() {
        return $this->render('index');
    }

    public function actionCreate() {
        $request = Yii::$app->request;

        if($request->isPost) {
            $slug = PlanService::Create($request->post());

            if($slug) {
                return $this->redirect(APPConfig::getUrl('plan/edit/' . $slug));
            } else {
                Yii::$app->session->setFlash('error', PlanService::$RESPONSE['CREATE_ERROR']);
            }
        }
        return $this->render('create');
    }

    public function actionEdit($slug) {
        $model = PlanService::GetPlanBySlug($slug);
        $model = ArrayHelper::toArray($model);
        $model['detail'] = json_decode($model['detail'], true);
        return $this->render('edit', compact('model'));
    }

    public function actionDuplicate($slug = null) {
        return $this->render('duplicate');
    }

    public function actionSave() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $result = PlanService::SaveDetail($request->post());
            if($result) { 
                return $this->asJson(['staus' => true]);
            }

            return $this->asJson([
                'status' => false,
                'message' => PlanService::$RESPONSE['EDIT_ERROR']
            ]);
        }

        throw new NotFoundHttpException();
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

    public function actionGetDetail() {
        
    }
}