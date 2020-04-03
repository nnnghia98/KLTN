<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use app\models\LoginForm;
use app\models\RegisterForm;
use app\modules\cms\services\AuthService;
use app\modules\cms\models\AuthUser;
use app\modules\cms\models\AuthUserInfo;
use app\modules\cms\services\AuthHandler;
use app\modules\cms\services\SiteService;
use yii\web\NotFoundHttpException;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'oAuthSuccess'],
            ],
        ];
    }

    public function oAuthSuccess($client) {
        $authHandler = new AuthHandler($client);
        $authHandler->handle();

        return $this->redirectSuccess();
    }

    public function redirectSuccess(){
        return $this->action->redirect(Yii::$app->user->getReturnUrl());
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $referrer = false;
        // if(Yii::$app->session->has('referrer')) {
        //     $referrer = Yii::$app->session->get('referrer');
        //     Yii::$app->session->remove('referrer');
        // }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        return $this->render('login', compact('model', 'referrer'));
    }

    public function actionRegister()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new RegisterForm();
        if(Yii::$app->request->isPost) {
            if($model->load(Yii::$app->request->post()) && $model->register()) {
                // SiteService::SendEmailConfirmEmail($model);
                // Yii::$app->session->set('referrer', 'register');
                return $this->redirect( Yii::$app->homeUrl . "site/login");
            }
        }

        return $this->render('register', [
            'model' => $model
        ]);
    }

    public function actionLogout()
    {
        SiteService::WriteLog(Yii::$app->user->id, SiteService::$ACTIVITIES['LOGOUT']);
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionForgotPassword() 
    {
        $request = Yii::$app->request;
        if($request->isPost){
            $email = $request->post('email');
            $user = AuthUser::findOne(['username' => $email]);
            if(!$user) {
                return $this->asJson('Email chưa được đăng ký cho bất kỳ tài khoản nào');
            }
            $user->removePasswordResetToken();
            $user->generatePasswordResetToken();
            if($user->save()) {
                $userinfo = AuthUserInfo::find(['auth_user_id' => $user->id])->one();
                $token = $user->password_reset_token;
                $auth_key = $user->auth_key;
                $urlReset = self::getSiteUrl() . Yii::$app->homeUrl . 'site/reset-password?token=' . $token . '&auth=' . $auth_key;

                $mail = Yii::$app->mailer->compose([
                    'html' => 'views/forgotpassword-html',
                    'text' => 'views/forgotpassword-text'
                ], ['urlReset' => $urlReset])
                    ->setTo([$user->username => $userinfo->fullname])
                    ->setSubject('Thay đổi mật khẩu cho Travel Sharing')
                    ->send()
                ;

                if(!$mail) {
                    return $this->asJson('Có lỗi sảy ra, vui lòng thử lại!');
                }
                return $this->asJson('Email hướng dẫn đặt lại mật khẩu đã được gửi, vui lòng kiểm tra và làm theo hướng dẫn.');
            }
        }
        return $this->render('forgot-password');
    }

    public function actionResetPassword()
    {
        $request = Yii::$app->request;
        $token = $request->get('token');
        $auth = $request->get('auth');
        if($token && $auth) {
            return $this->render('reset-password', compact('auth', 'token'));
        }
        return $this->redirect(ErrorFactory::getRidrectLinkNotfound());
    }

    public function actionConfirmEmail($auth, $token) 
    {
        $user = AuthUser::find()->where(['and', ['auth_key' => $auth], ['access_token' => $token]])->one();
        if($user && !$user->confirmed) {
            $user->confirmed = AuthService::$AUTH_CONFIRM['CONFIRMED'];
            $user->save();
            Yii::$app->session->set('referrer', 'confirm-email');
            return $this->redirect( Yii::$app->homeUrl . "site/login");
        }

        throw new NotFoundHttpException();
    }

    public function actionError() {
        return $this->render('error');
    }

    public function actionSetPassword()
    {
        $request = Yii::$app->request;
        if($request->isPost) {
            $auth = $request->post('auth');
            $token = $request->post('token');
            $password = $request->post('password');
            $password2 = $request->post('password2');

            $user = AuthUser::find()->where(['auth_key' => $auth])->andWhere(['password_reset_token' => $token])->one();
            if($user) {
                $validate = AuthService::validateResetPasswordForm($password, $password2);
                if($validate === 'success') {
                    $user->password = Yii::$app->getSecurity()->generatePasswordHash($password);
                    $user->save();
                    Yii::$app->session->setFlash('success', 'Đặt lại mật khẩu thành công');
                    return $this->redirect(Yii::$app->homeUrl . 'site/login');
                }
                return $this->asJson($validate);
            }
        }
        throw new NotFoundHttpException();
    }
}
