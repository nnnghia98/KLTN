<?php

use kartik\form\ActiveForm;
use app\modules\cms\CMSConfig;
use yii\authclient\widgets\AuthChoice;
?>

<style>
    .navbar,
    footer {
        display: none !important;
    }

    .input-group-addon {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        right: .5rem;
        color: #999;
        font-size: .75rem !important;
    }

    #registerform-password {
        border-radius: 1.25rem;
    }
</style>
<div class="content d-flex justify-content-center align-items-center page-register">
    <?php $form = ActiveForm::begin([
        'id' => 'register-form'
    ]) ?>
    <div class="card card-body login-form border-top-primary" style="width: 364px;">
        <div class="text-center my-3">
            <a href="<?= Yii::$app->homeUrl ?>" class="d-block">
                <img src="<?= Yii::$app->homeUrl ?>resources/images/logo-color.png" style="max-width: 300px">
            </a>
            <h4 class="font-weight-bold text-uppercase text-indigo-400 mt-3">Đăng ký tài khoản</h4>
        </div>

        <div class="form-group text-left">
            <?= $form->field($model, 'fullname')->textInput(['placeholder' => 'Họ và tên'])->label(false) ?>
        </div>

        <div class="form-group text-left">
            <?= $form->field($model, 'username')->textInput(['placeholder' => 'Email'])->label(false) ?>
        </div>

        <div class="form-group text-left">
            <?= $form->field($model, 'password', [
                'addon' => ['append' => ['content' => '6 - 15 ký tự']]
            ])->textInput(['placeholder' => 'Mật khẩu', 'type' => 'password'])->label(false) ?>
        </div>

        <div class="form-group text-left">
            <?= $form->field($model, 'cpassword')->textInput(['placeholder' => 'Xác nhận mật khẩu', 'type' => 'password'])->label(false) ?>
        </div>

        <div class="form-group text-center mt-3">
            <button type="submit" class="btn btn-block bg-pink-400 rounded-round">Đăng ký<i class="icon-user-plus ml-2"></i></button>
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
                <h6 class="display-block">Chưa có tài khoản? <a href="login" class="font-weight-bold">Đăng nhập</a></h6>
            </div>
        </div>

        <h6 class="help-block text-center no-margin"> © 2020 Travel Sharing</h6>
    </div>
    <?php ActiveForm::end() ?>
</div>