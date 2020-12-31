<!-- <meta http-equiv="Content-Type" content="text/html; charset=utf-8"> -->
<?php
header("Content-type: text/html; charset=utf-8");
use app\modules\app\APPConfig;
use app\modules\app\PathConfig;
use app\modules\cms\CMSConfig;
use app\modules\contrib\gxassets\GxLaddaAsset;

GxLaddaAsset::register($this);
include('site_ext.php');
$pageData = [
    'pageTitle' => 'Bộ dữ liệu bình luận',
    'pageBreadcrumb' => [['Dữ liệu bình luận']],
    'backgoundHeader' => Yii::$app->homeUrl . 'resources/images/plan-header.jpg'
]; ?>
<?= $this->render(PathConfig::getAppViewPath('pageHeader'), $pageData); ?>

<?php

?>
<?php
    
?>


