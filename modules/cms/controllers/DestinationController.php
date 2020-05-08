<?php 

namespace app\modules\cms\controllers;

use app\modules\cms\CMSConfig;
use app\modules\cms\models\AuthRole;
use app\modules\cms\models\AuthUser;
use app\modules\cms\models\Destination;
use app\modules\cms\services\AuthService;   
use app\modules\cms\services\SiteService;
use app\modules\app\services\DestinationService;
use Yii;
use yii\db\Query;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class DestinationController extends Controller
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

    public function actionEdit($slug = null) {
        $model = Destination::findOne(['slug' => $slug]);
        $request = Yii::$app->request;
        if($request->isPost) {
            $model->load($request->post());
            if($model->save()) {
                Yii::$app->session->setFlash('success', 'Chỉnh sửa thông tin điểm đến thành công');
                return $this->redirect(CMSConfig::getUrl('destination'));
            }
        }
        return $this->render('edit', compact('model'));
    }

 
    /**-------------API-----------------*/
    public function actionGetList($page = 1, $perpage = 20) {
        $query = (new Query())
                ->select(['name', 'subtitle', 'slug'])
                ->from('destination')
                ->where(['delete' => AuthService::$AUTH_DELETE['ALIVE']]);

        $total = $query->select('COUNT(*)')->column();
        list($limit, $offset) = SiteService::GetLimitAndOffset($page, $perpage);
        $destinations = $query->select('*')->orderBy('created_at desc')->limit($limit)->offset($offset)->all();

        if($destinations ) {
            $count = count($destinations );
            $paginations = SiteService::CreatePaginationMetadata($total, $page, $perpage, $count);
            $response = [
                'status' => true,
                'destinations' => $destinations,
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

    public function actionDelete() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $id = $request->post('id');
            $destination = Destination::find()->where(['id' => $id])->one();
            if($destination) {
                if(AuthService::IsAdmin()) {
                    $destination->delete = AuthService::$AUTH_DELETE['DELETED'];
                    $destination->save();
                    
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
}