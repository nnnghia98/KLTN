<?php 

namespace app\modules\cms\controllers;

use app\modules\cms\CMSConfig;
use app\modules\cms\models\AuthRole;
use app\modules\cms\models\AuthUser;
use app\modules\cms\services\AuthService;
use app\modules\cms\services\SiteService;
use Yii;
use yii\db\Query;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class UserController extends Controller
{
    public $enableCsrfValidation = false;
    public $layout = 'admin';
    /**-------------VIEWS-----------------*/
    public function actionIndex() {
        $roles = AuthRole::find()->select('role_name')->indexBy('id')->orderBy('id')->column();
        return $this->render('index', compact('roles'));
    }

    public function actionCreate() {
        return $this->render('create');
    }

    public function actionDetail($slug = null) {
        $user = AuthService::GetUserProfileBySlug($slug);
        $roles = AuthRole::find()->select('role_name')->indexBy('id')->orderBy('id')->column();
        return $this->render('detail', compact('roles', 'user'));
    }
 
    /**-------------API-----------------*/
    public function actionGetList($page = 1, $perpage = 20) {
        $query = (new Query())
                ->select(['fullname', 'username', 'slug', 'id', 'type', 'auth_role_id', 'confirmed'])
                ->from('auth_user')
                ->where(['delete' => AuthService::$AUTH_DELETE['ALIVE']]);

        $total = $query->select('COUNT(*)')->column();
        list($limit, $offset) = SiteService::GetLimitAndOffset($page, $perpage);
        $users = $query->select('*')->orderBy('created_at desc')->limit($limit)->offset($offset)->all();

        if($users) {
            $count = count($users);
            $paginations = SiteService::CreatePaginationMetadata($total, $page, $perpage, $count);
            $response = [
                'status' => true,
                'users' => $users,
                'paginations' => $paginations
            ];
        } else {
            $response = [
                'status' => false,
                'message' => AuthService::$AUTH_RESPONSES['EMPTY_LIST']
            ];
        }
        return $this->asJson($response);
    }

    public function actionSaveModel() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $message = AuthService::CreateUser($request->post());
            if($message === true) {
                Yii::$app->session->setFlash('success', AuthService::$AUTH_RESPONSES['CREATE_SUCCESS']);
                return $this->asJson([
                    'status' => true,
                    'message' => AuthService::$AUTH_RESPONSES['CREATE_SUCCESS']
                ]);
            } else {
                return $this->asJson([
                    'status' => false,
                    'message' => $message
                ]);
            }
        }
        throw new NotFoundHttpException();
    }

    public function actionResetPassword() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $message = false;
            // if($message === true) {
            //     Yii::$app->session->setFlash('success', AuthService::$AUTH_RESPONSES['CREATE_SUCCESS']);
            //     return $this->asJson([
            //         'status' => true,
            //         'message' => AuthService::$AUTH_RESPONSES['CREATE_SUCCESS']
            //     ]);
            // } else {
            //     return $this->asJson([
            //         'status' => false,
            //         'message' => $message
            //     ]);
            // }
        }
        throw new NotFoundHttpException();
    }

    public function actionDelete() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $id = $request->post('id');
            $user = AuthUser::find()->where(['id' => $id])->one();
            if($user) {
                if(AuthService::IsAdmin()) {
                    $user->delete = AuthService::$AUTH_DELETE['DELETED'];
                    $user->save();
                    
                    $response = [
                        'status' => true,
                        'message' => AuthService::$AUTH_RESPONSES['DELETE_SUCCESS']
                    ];
                } else {
                    $response = [
                        'status' => false,
                        'message' => AuthService::$AUTH_RESPONSES['NOT_ENOUGH_PERMISSION']
                    ];
                }
                return $this->asJson($response);
            }
        }
        throw new NotFoundHttpException();
    }

    public function actionChangeRole() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $id = $request->post('userid');
            $roleid = $request->post('roleid');
            $user = AuthUser::find()->where(['id' => $id])->one();
            if($user) {
                if(AuthService::IsAdmin()) {
                    $user->auth_role_id = $roleid;
                    $user->save();

                    $response = [
                        'status' => true,
                        'message' => AuthService::$AUTH_RESPONSES['CHANGE_ROLE_SUCCESS']
                    ];
                } else {
                    $response = [
                        'status' => false,
                        'message' => AuthService::$AUTH_RESPONSES['NOT_ENOUGH_PERMISSION']
                    ];
                }
                return $this->asJson($response);
            }
        } 
        throw new NotFoundHttpException();
    }

    public function actionChangeType() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $id = $request->post('userid');
            $type = $request->post('type');
            $user = AuthUser::find()->where(['id' => $id])->one();
            if($user) {
                if(AuthService::IsAdmin()) {
                    $user->type = $type;
                    $user->save();

                    $response = [
                        'status' => true,
                        'message' => AuthService::$AUTH_RESPONSES['CHANGE_TYPE_SUCCESS']
                    ];
                } else {
                    $response = [
                        'status' => false,
                        'message' => AuthService::$AUTH_RESPONSES['NOT_ENOUGH_PERMISSION']
                    ];
                }
                return $this->asJson($response);
            }
        } 
        throw new NotFoundHttpException();
    }
}