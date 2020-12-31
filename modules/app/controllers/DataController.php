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

use app\modules\cms\CMSConfig;
use app\modules\cms\models\Destination;
use app\modules\cms\models\FileRef;
use app\modules\cms\models\FileRepo;
use app\modules\cms\models\Place;
use simple_html_dom;
use yii\httpclient\Client;

use VnCoreNLP;
include('simple_html_dom.php');

class DataController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionIndex() {
        return $this->render('index');
    }

    // public function actionGetDestinations() {
    //     $destinations = DestinationService::GetTopDestinations(3);
    //     if(is_array($destinations)) {
    //         return $this->asJson(['status' => true, 'destinations' => $destinations]);
    //     }
    //     return $this->asJson(['status' => false]);
    // }

    // public function actionGetPlaces() {
    //     $places = PlaceService::GetTopPlaces(8);
    //     if(is_array($places)) {
    //         return $this->asJson(['status' => true, 'places' => $places]);
    //     }
    //     return $this->asJson(['status' => false]);
    // }

    // public function actionNewestPlans() {
    //     $plans = PlanService::GetNewestPlans(8);
    //     if(is_array($plans)) {
    //         return $this->asJson(['status' => true, 'plans' => $plans]);
    //     }
    //     return $this->asJson(['status' => false]);
    // }
}