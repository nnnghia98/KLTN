<?php

namespace app\modules\cms\services;

use app\modules\cms\models\AuthUser;
use app\modules\cms\models\AuthUserInfo;
use app\modules\cms\models\FileRepo;
use Yii;
use yii\db\Query;

class FileService
{
    public static $UPLOAD_DIR = 'uploads/';
    public static $FILE_DELETE = [
        'ALIVE' => 1,
        'DELETED' => 0
    ];

    public static function Upload($file_tmp, $filename) {
        $imgInfo = self::InitFileInformation($filename);
        $path = self::$UPLOAD_DIR . $imgInfo['path'];
        if (move_uploaded_file($file_tmp, $path)) {
            $image = new FileRepo([
                'name' => $imgInfo['name'],
                'slug' => $imgInfo['slug'],
                'path' => $imgInfo['path'],
                'type' => $imgInfo['type'],
                'delete' => self::$FILE_DELETE['ALIVE'],
                'delete' => self::$FILE_DELETE['ALIVE'],
                'created_by' => Yii::$app->user->id,
            ]);

            if ($image->save()) {
                return $image;
            }
        }

        return false;
    }

    public static function InitFileInformation($name)
    {
        list($newname, $ext) = self::ParseFileNameToNameAndExtension($name);
        $slug = $newname . '_'. uniqid();

        $imgInfo = [
            'name' => $name,
            'slug' => $slug,
            'path' => $slug . '.' . $ext,
            'type' => $ext,
        ];

        return $imgInfo;
    }

    public static function ParseFileNameToNameAndExtension($name) {
        $parseImgname = explode('.', $name);
        $ext = end($parseImgname);
        array_pop($parseImgname);
        $newname = implode('_', $parseImgname);
        $newname = SiteService::ConvertStringToSlug($newname, '_');
        return [$newname, $ext];
    }
}