<?php 

namespace app\modules\cms\controllers;

use app\modules\cms\models\Destination;
use app\modules\cms\models\FileRef;
use app\modules\cms\models\FileRepo;
use Yii;
use yii\httpclient\Client;
use yii\web\Controller;

class GetDataTriphunterController extends Controller
{
    public function actionGetDestination() {
        $url = 'https://triphunter.vn/api/v1/collections/1';
        $response = self::GetData($url);
        if($response->isOk) {
            $transaction = Yii::$app->db->beginTransaction();
            try{
                $data = $response->data;
                foreach($data['object']['places'] as $dest) {
                    $object = self::SaveDestination($dest);
                    if($object) {
                        self::SavePhotoRef($object, $dest['photos']);
                    }
                }

                $transaction->commit();
            }  catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            } catch (\Throwable $e) {
                $transaction->rollBack();
                throw $e;
            }
            
        }
    }

    public function GetData($url) {
        
        $client = new Client();
        $response = $client->createRequest()
                ->setMethod('GET')
                ->setUrl($url)
                ->send();
        return $response;
    }

    public function SaveDestination($data) {
        $photo_name = $data['slug'] . '-thumb';
        $photo = self::SavePhoto($data['cover_photo_thumb']['image_url'], $photo_name);
        $destination = new Destination([
            'name' => $data['name'],
            'lat' => strval($data['lat']),
            'lng' => strval($data['lng']),
            'subtitle' => $data['subtitle'],
            'slug' => $data['slug'],
            'status' => 1,
            'delete' => 1,
            'created_by' => Yii::$app->user->id,
            'viewed' => 0,
            'thumbnail' => $photo->path
        ]);

        if($destination->save()) {
            return $destination;
        }

        return false;
    }

    public function SavePhotoRef($object, $photos) {
        $object_type = $object->className();
        $object_id = $object->id;
        $object_slug = $object->slug;

        foreach($photos as $idx => $p) {
            $photo_name = $object_slug . '-' . $idx;
            $photo = self::SavePhoto($p['image_url'], $photo_name);
            $ref = new FileRef([
                'file_id' => $photo->id,
                'object_type' => $object_type,
                'object_id' => $object_id
            ]);

            $ref->save();
        }
    }

    public function SavePhoto($url, $name) {
        $slug = uniqid() . '-' . $name;
        $path = $slug . '.jpg';
        $DIR = 'uploads/';
        $url = 'https://triphunter.vn' . $url;
        $content = file_get_contents($url);
        if(file_put_contents($DIR . $path, $content)) {
            $photo = new FileRepo([
                'slug' => $slug,
                'path' => $path,
                'name' => $path,
                'created_by' => Yii::$app->user->id,
                'type' => 'jpg',
                'delete' => 1
            ]);

            if($photo->save()) return $photo;
        }

        return false;
    }
}