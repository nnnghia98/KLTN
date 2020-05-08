<?php 

namespace app\modules\app\controllers;

use app\modules\app\APPConfig;
use app\modules\cms\models\AuthUser;
use app\modules\cms\services\AuthService;
use app\modules\cms\services\PlanService;
use app\modules\cms\services\TravelSharingService;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class UserController extends Controller
{
    public $enableCsrfValidation = false;
    /**-------------VIEWS-----------------*/
    public function actionMyProfile() {
        $profile = AuthService::GetUserProfile();
        return $this->render('my-profile', compact('profile'));
    }

    public function actionMyPlan() {
        $profile = AuthService::GetUserProfile();
        return $this->render('my-plan', compact('profile'));
    }
    
    public function actionPlan($slug) {
        $user = AuthService::GetUserBySlug($slug);
        if($user) {
            if($user['id'] == Yii::$app->user->id) {
                return $this->redirect(APPConfig::getUrl('user/my-plan'));
            }
            
            return $this->render('plan', compact('user'));
        }
        throw new NotFoundHttpException();
    }

    /**-------------API-----------------*/
    public function actionChangeAvatar() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $id = $request->post('auth_user_id');
            $avatar = $request->post('avatar');

            $user = AuthUser::findOne($id);
            if($user) {
                $user->avatar = $avatar;
                $user->save();
                return $this->asJson([
                    'status' => true,
                    'message' => AuthService::$AUTH_RESPONSES['CHANGE_AVATAR_SUCCESS']
                ]);
            }

            return $this->asJson([
                'status' => false,
                'message' => AuthService::$AUTH_RESPONSES['ERROR']
            ]);
        }
        throw new NotFoundHttpException();
    }

    public function actionChangeInformation() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $message = AuthService::ChangeInformation($request->post());

            if($message === true) {
                return $this->asJson([
                    'status' => true,
                    'message' => AuthService::$AUTH_RESPONSES['CHANGE_INFORMATION_SUCCESS']
                ]);
            }

            return $this->asJson([
                'status' => false,
                'message' => $message
            ]);
        }
        throw new NotFoundHttpException();
    }

    public function actionChangePassword() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $message = AuthService::ChangePassword($request->post());

            if($message === true) {
                return $this->asJson([
                    'status' => true,
                    'message' => AuthService::$AUTH_RESPONSES['CHANGE_PASSWORD_SUCCESS']
                ]);
            }

            return $this->asJson([
                'status' => false,
                'message' => $message
            ]);
        }
        throw new NotFoundHttpException();
    }

    public function actionGetUserPlans($id = null) {
        $userid = $id ? $id : Yii::$app->user->id;
        $plans = PlanService::GetPlansByUserId($userid);
        $response = [
            'status' => true, 
            'plans' => $plans
        ];
        return $this->asJson($response);
    }
}