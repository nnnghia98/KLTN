<?php 

namespace app\modules\cms\controllers;

use app\modules\cms\services\FileService;
use Yii;
use yii\web\Controller;

class FileController extends Controller
{
    public $enableCsrfValidation = false;
    public $layout = 'admin';


    /**-------------API-----------------*/
    public function actionUpload() {
        $files = $_FILES['Files'];
        $uploadedids = [];
        $uploadedpaths = [];
        $uploadfails = [];
        foreach($files['tmp_name'] as $index => $file_tmp) {
            if(!in_array($files['type'][$index], ['image/jpg', 'image/jpeg']) || $files['size'][$index] > 2097152) {
                array_push($uploadfails, $files['name'][$index]);
            } else {
                $image = FileService::Upload($file_tmp, $files['name'][$index]);
                if($image) {
                    array_push($uploadedids, $image->id);
                    array_push($uploadedpaths, $image->path);
                } else {
                    array_push($uploadfails, $files['name'][$index]);
                }
            }
        }

        $response = [
            'status' => true,
            'ids' => $uploadedids,
            'paths' => $uploadedpaths,
            'fails' => $uploadfails
        ];

        return $this->asJson($response);
    }
}