<?php 

namespace app\modules\app\controllers;

use app\modules\app\APPConfig;
use app\modules\cms\models\AuthUser;
use app\modules\cms\services\AuthService;
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
        throw new NotFoundHttpException();
    }
    
    public function actionPlan($slug) {
        $user = AuthService::GetUserBySlug($slug);
        if($user) {
            if($user['id'] == Yii::$app->user->id) {
                return $this->redirect(APPConfig::getUrl('user/my-plan'));
            }

            $user['following'] = AuthService::CheckFollowingUser($user['id']);
            $points = TravelSharingService::GetTravelSharingByUserId($user['id']);
            return $this->render('pointclound', compact('user', 'points'));
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

    public function actionFollow() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $message = AuthService::FollowUser($request->post());

            if($message === true) {
                return $this->asJson([
                    'status' => true,
                    'message' => AuthService::$AUTH_RESPONSES['FOLLOW_USER']
                ]);
            }

            return $this->asJson([
                'status' => false,
                'message' => $message
            ]);
        }
        throw new NotFoundHttpException();
    }

    public function actionUnfollow() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $message = AuthService::UnfollowUser($request->post());

            if($message === true) {
                return $this->asJson([
                    'status' => true,
                    'message' => AuthService::$AUTH_RESPONSES['UNFOLLOW_USER']
                ]);
            }

            return $this->asJson([
                'status' => false,
                'message' => $message
            ]);
        }
        throw new NotFoundHttpException();
    }

    public function actionGetFollowing() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $following = AuthService::GetFollowing();
            return $this->asJson([
                'status' => true,
                'following' => $following
            ]);
        }
        throw new NotFoundHttpException();
    }

    public function actionGetFollower() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $follower = AuthService::GetFollower();
            return $this->asJson([
                'status' => true,
                'follower' => $follower
            ]);
        }
        throw new NotFoundHttpException();
    }
}