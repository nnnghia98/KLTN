<?php

use app\modules\app\APPConfig;
use app\modules\app\PathConfig;

$pageData = [
    'pageTitle' => 'Địa điểm nghỉ ngơi',
    'pageBreadcrumb' => 'Nghỉ ngơi',
    'backgoundHeader' => Yii::$app->homeUrl . 'resources/images/destination-header.jpg'
];
?>
<?= $this->render(PathConfig::getAppViewPath('pageListHeader'), $pageData); ?>