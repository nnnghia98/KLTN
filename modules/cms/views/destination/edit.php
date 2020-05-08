<?php 
use app\modules\cms\CMSConfig;
use app\modules\cms\PathConfig;
use kartik\form\ActiveForm;

$pageData = [
    'pageTitle' => 'Chỉnh sửa điểm đến',
    'headerElements' => [],
];
?>
<?= $this->render(PathConfig::getAppViewPath('tagPageHeader', true), $pageData); ?>

<div class="content">
    <div class="card overflow-hidden">
        <div class="card-header bg-light">
            <h4 class="card-title text-uppercase">#Chỉnh sửa điểm đến</h4>
        </div>
        <div class="card-body">
            <?php $form = ActiveForm::begin() ?>
            <div class="row">
                <div class="col-md-6 form-group">
                    <?= $form->field($model, 'name')->textInput()->label('Tên điểm đến') ?>
                </div>
                <div class="col-md-6 form-group">
                    <?= $form->field($model, 'subtitle')->textInput()->label('Mô tả ngắn') ?>
                </div>
                <div class="col-md-12 form-group">
                    <?= $form->field($model, 'description')->textarea(['rows' => 10])->label('Mô tả') ?>
                </div>
                <div class="col-md-12 form-group text-right">
                    <button type="submit" class="btn bg-pink-400 rounded-round">Cập nhật</button>
                </div>
            </div>
            <?php ActiveForm::end() ?>
        </div>
    </div>
</div>