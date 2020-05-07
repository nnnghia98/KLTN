<?php
namespace app\modules\contrib\gxassets;

class GxVueperSlidesAsset extends \yii\web\AssetBundle {
    public $sourcePath = '@app/modules/contrib/gxassets/assets/vueperslides';

    public $css = [
        'vueperslides.min.css'
    ];

    public $js = [
        'vueperslides.min.js'
    ];

    public $depends = [
        '\app\modules\contrib\gxassets\GxVueAsset',
    ];

    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}