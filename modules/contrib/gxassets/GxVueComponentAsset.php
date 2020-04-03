<?php
namespace app\modules\contrib\gxassets;

class GxVueComponentAsset extends \yii\web\AssetBundle {
    public $sourcePath = '@app/modules/contrib/gxassets/assets/vue-component';

    public $css = [
    ];

    public $js = [
        'component.js',
    ];

    public $depends = [
        '\app\modules\contrib\gxassets\GxVueAsset',
    ];

    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}