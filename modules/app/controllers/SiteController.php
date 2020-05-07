<?php 

namespace app\modules\app\controllers;

use app\modules\cms\services\DestinationService;
use app\modules\cms\services\PlaceService;
use app\modules\cms\services\PlanService;
use yii\web\Controller;

class SiteController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionIndex() {
        return $this->render('index');
    }

    public function actionGetDestinations() {
        $destinations = DestinationService::GetTopDestinations(3);
        if(is_array($destinations)) {
            return $this->asJson(['status' => true, 'destinations' => $destinations]);
        }
        return $this->asJson(['status' => false]);
    }

    public function actionGetPlaces() {
        $places = PlaceService::GetTopPlaces(8);
        if(is_array($places)) {
            return $this->asJson(['status' => true, 'places' => $places]);
        }
        return $this->asJson(['status' => false]);
    }

    public function actionNewestPlans() {
        $plans = PlanService::GetNewestPlans(8);
        if(is_array($plans)) {
            return $this->asJson(['status' => true, 'plans' => $plans]);
        }
        return $this->asJson(['status' => false]);
    }
}