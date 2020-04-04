<?php 

namespace app\modules\cms\controllers;

use app\modules\cms\CMSConfig;
use app\modules\cms\models\AuthRole;
use app\modules\cms\models\AuthUser;
use app\modules\cms\models\Destination;
use app\modules\cms\services\AuthService;
use app\modules\cms\services\SiteService;
use Yii;
use yii\db\Query;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class DestinationController extends Controller
{
    public $enableCsrfValidation = false;
    /**-------------VIEWS-----------------*/
    public function actionIndex() {
        return $this->render('index');
    }

    public function actionCreate() {
        return $this->render('create');
    }
 
    /**-------------API-----------------*/
    public function actionGetList() {
        $destinations = Destination::find()->where(['and', ['status' => 1], ['delete' => 1]])->all();
        dd($destinations);
        //dd(): hàm này để dump giá trị ra coi trước

        if($destination) {
            $count = count($destination);
            $response = [
                'status' => true,
                'destinations' => $destination,
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

 
}