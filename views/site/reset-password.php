<?php
use kartik\form\ActiveForm;
?>

<style>
    .field-loginform-recaptcha .help-block-error{
        color: red;
    }
</style>

<div class="content d-flex justify-content-center align-items-center bg-white page-login">
    <?php $form = ActiveForm::begin([
        'id' => 'reset-password-form',
    ]); ?>
    <div class="card card-body login-form" style="width: 344px;">
        <div class="text-center">
            <div class="mb-3">
                <img src="<?= Yii::$app->homeUrl ?>resources/images/logo-site.png" style="max-width: 100px">
            </div>
            <b class="red">Travel Sharing GEOTAG</b>
            <h5 class="content-group text-grey text-bold">Reset password</h5>
        </div>

        <div class="text-center forgot-password">
            <div class="form-group my-4">
                <div class="form-group form-group-feedback form-group-feedback-right">
                    <input type="password" name="password" class="form-control" placeholder="New password">
                    <div class="form-control-feedback">
                        <i class="icon-lock text-muted"></i>
                    </div>
                </div>
                <div class="form-group form-group-feedback form-group-feedback-right">
                    <input type="password" name="password2" class="form-control" placeholder="Confirm new password">
                    <div class="form-control-feedback">
                        <i class="icon-lock text-muted"></i>
                    </div>
                </div>
                <input type="hidden" name="auth" value="<?= $auth ?>">
                <input type="hidden" name="token" value="<?= $token ?>">
                <div class="message-response text-left">
                    <span class="text-danger"></span>
                </div>

            </div>
            <div class="form-group">
                <button type="submit" class="btn bg-blue btn-block"><i class="icon-spinner11 mr-2"></i> Reset</button>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script>
    $(function(){
        $("#reset-password-form").submit(function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            $.ajax({
                data: $("#reset-password-form").serialize(),
                type: 'POST',
                url: '<?= Yii::$app->homeUrl ?>site/set-password',
                success: function (msg) {
                    $(".message-response span").empty().html('<span><i class="icon-spam"></i> ' + msg + '</span>');
                },
                error: function (error) {
                    console.log(error);
                }
            });
            return false;
        })
    })
</script>