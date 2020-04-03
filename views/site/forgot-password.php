<?php
use kartik\form\ActiveForm;
use app\modules\contrib\gxassets\GxLaddaAsset;

GxLaddaAsset::register($this);
?>

<style>
    .field-loginform-recaptcha .help-block-error{
        color: red;
    }
</style>

<div class="content d-flex justify-content-center align-items-center bg-white page-login">
    <?php $form = ActiveForm::begin([
        'id' => 'forgot-password-form',
    ]); ?>
    <div class="card card-body login-form" style="width: 344px;">
        <div class="text-center">
            <div class="mb-3">
                <img src="<?= Yii::$app->homeUrl ?>resources/images/logo-site.png" style="max-width: 100px">
            </div>
            <b class="red">Travel Sharing GEOTAG</b>
            <h5 class="content-group text-grey text-bold">Forgot password</h5>
        </div>

        <div class="text-center forgot-password">
            <div class="form-group my-4">
                <div class="form-group form-group-feedback form-group-feedback-right">
                    <input type="email" name="email" class="form-control px-1" placeholder="Email">
                    <div class="form-control-feedback">
                        <i class="icon-mail5 text-muted"></i>
                    </div>
                </div>
                <div class="message-response text-left">
                    <span class="text-danger"></span>
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block ladda-button" data-style="zoom-in" id="btn-send-email"><i class="icon-spinner11 mr-2"></i> Request new password</button>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script>
    $(function(){
        $("#forgot-password-form").submit(function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var l = Ladda.create($("#btn-send-email")[0]);
            l.start();
            $.ajax({
                data: $("#forgot-password-form").serialize(),
                type: 'POST',
                url: $(this).attr('action'),
                success: function (msg) {
                    l.stop();
                    $(".message-response span").empty().html(msg);
                },
                error: function (error) {
                    l.stop();
                    console.log(error);
                }
            });
            return false;
        });
    });
</script>