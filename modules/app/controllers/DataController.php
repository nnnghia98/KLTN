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
        // $url = 'https://www.foody.vn/ho-chi-minh/binh-luan';
        $url = 'https://gody.vn/chau-a/viet-nam/lam-dong/ho-xuan-huong';
        // // Open foody.vn with ssl
        // $opts = array('http'=>array('header' => "User-Agent:MyAgent/1.0\r\n")); 
        // // Basically adding headers to the request
        // $context = stream_context_create($opts);
        // $html = file_get_contents($url, false, $context);
        // $html = htmlspecialchars($html);
        $html = file_get_html($url);
        return $this->render('index', compact('html'));
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