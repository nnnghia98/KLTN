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
        $userid = Yii::$app->user->id;
        $model = PlanService::GetPlanBySlug($slug);
        if ($model && $model['created_by'] == $userid) {
            $model = ArrayHelper::toArray($model);
            $model['detail'] = json_decode($model['detail'], true);
            $model['routes'] = json_decode($model['routes'], true);
            return $this->render('edit', compact('model'));
        }
        throw new NotFoundHttpException();
    }

    public function actionDetail($slug) {
        $model = PlanService::GetPlanBySlug($slug);
        $model = ArrayHelper::toArray($model);
        $model['detail'] = json_decode($model['detail'], true);
        $model['routes'] = json_decode($model['routes'], true);
        return $this->render('detail', compact('model'));
    }

    public function actionDuplicate($slug = null) {
        return $this->render('duplicate');
    }

    public function actionSave() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $result = PlanService::SaveDetail($request->post());
            if($result) { 
                return $this->asJson(['status' => true]);
            }

            return $this->asJson([
                'status' => false,
                'message' => PlanService::$RESPONSE['EDIT_ERROR']
            ]);
        }

        throw new NotFoundHttpException();
    }

    public function actionDelete() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $result = PlanService::Delete($request->post());
            if($result) {
                Yii::$app->session->setFlash('success', PlanService::$RESPONSE['DELETE_SUCCESS']);
                return $this->asJson(['status' => true]);
            }
            return $this->asJson(['status' => false, 'message' => PlanService::$RESPONSE['ACTION_ERROR']]);
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

    public function actionGetComments($id, $page) {
        $comments = PlanService::GetComments($id, $page, 10);
        $response = [
            'status' => true, 
            'comments' => $comments
        ];
        return $this->asJson($response);
    }

    public function actionGetInteractive($id) {
        $interactive = PlanService::GetInteractiveOfCurrentUser($id);
        $response = [
            'status' => true, 
            'interactive' => $interactive
        ];
        return $this->asJson($response);
        
    }
    public function actionSubmitComment() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $result = PlanService::SubmitComment($request->post());
            if($result === true) {
                return $this->asJson(['status' => true]);
            }
            
            return $this->asJson(['status' => true, 'message' => $result]);
        }

        throw new NotFoundHttpException();
    }
}