<?php

use app\assets\AppAsset;
use app\modules\contrib\gxassets\GxLimitlessTemplateAsset;
use yii\bootstrap\ActiveForm;
use app\modules\cms\CMSConfig;
use yii\authclient\widgets\AuthChoice;

$this->title = 'Login';

GxLimitlessTemplateAsset::register($this);
AppAsset::register($this);
?>

<style>
    .navbar,
    footer {
        display: none !important;
    }
</style>

<div class="content d-flex justify-content-center align-items-center page-login flex-column">
    <?php if($referrer == 'register'): ?>
        <div class="alert alert-primary border-0">
            Chúc mừng bạn đã đăng ký tài khoản thành công! Vui lòng kiểm tra email và làm theo hướng dẫn để xác nhận tài khoản trước khi đăng nhập.
        </div>
    <?php elseif($referrer == 'confirm-email'): ?>
        <div class="alert alert-primary border-0">
            Chúc mừng bạn đã xác nhận tài khoản thành công. Bạn có thể đăng nhập và trải nghiệm Travel Sharing ngay bây giờ.
        </div>
    <?php endif; ?>
    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
    ]); ?>
    <div class="card card-body login-form border-top-primary" style="width: 364px;">
        <div class="text-center my-3">
            <a href="<?= Yii::$app->homeUrl ?>" class="d-block">
                <img src="<?= Yii::$app->homeUrl ?>resources/images/logo-color.png" style="max-width: 300px">
            </a>
            <h4 class="font-weight-bold text-uppercase text-indigo-400 mt-3">Đăng nhập</h4>
        </div>

        <div class="form-group text-left">
            <?= $form->field($model, 'username')->textInput(['placeholder' => 'Email'])->label(false) ?>
        </div>

        <div class="form-group text-left">
            <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Mật khẩu'])->label(false) ?>
        </div>

        <!-- <div class="form-group d-flex align-items-center">
            <a href="<?= Yii::$app->homeUrl . 'site/forgot-password' ?>" class="ml-auto">Quên mật khẩu?</a>
        </div> -->

        <div class="form-group mt-3">
            <button type="submit" class="btn btn-block bg-pink-400 rounded-round">Đăng nhập<i class="icon-circle-right2 ml-2"></i></button>
        </div>

        <div class="form-group text-center text-muted content-divider">
            <span class="px-2">HOẶC</span>
        </div>

        <div class="form-group text-center">
            <?php $authChoice = AuthChoice::begin([
                'baseAuthUrl' => ['site/auth']
            ]); ?>
            <?php foreach($authChoice->getClients() as $client) :?>
                    <a href="<?= $authChoice->createClientUrl($client) ?>" 
                        class="btn btn-icon rounded-round border-2 mx-1 <?= $client->getName() === 'google' ? 'btn-outline-danger' : 'btn-outline-primary' ?>"
                        data-popup-width="800" data-popup-height="500">
                        <i class="<?= $client->getName() === 'google' ? 'icon-google' : 'icon-facebook' ?>"></i>
                    </a>
            <?php endforeach; ?>
            <?php AuthChoice::end() ?>
        </div>

        <div class="content-group">
            <div class="text-center">
                <h6 class="display-block">Chưa có tài khoản? <a href="register" class="font-weight-bold">Đăng ký</a></h6>
            </div>
        </div>

        <h6 class="help-block text-center no-margin"> © 2020 Travel Sharing</h6>
    </div>
    <?php ActiveForm::end(); ?>
</div>