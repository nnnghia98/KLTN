<?php 

namespace app\modules\app\controllers;

use yii\web\Controller;

class SiteController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionIndex() {
        return $this->render('index');
    }
}