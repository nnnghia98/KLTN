<?php 

namespace app\modules\cms\controllers;

use app\modules\cms\CMSConfig;
use app\modules\cms\models\Destination;
use app\modules\cms\models\FileRef;
use app\modules\cms\models\FileRepo;
use app\modules\cms\models\Place;
use Yii;
use yii\httpclient\Client;
use yii\web\Controller;

class GetDataTriphunterController extends Controller
{
    public $api = [
        'destination' => 'https://triphunter.vn/api/v1/collections/1',
        'da-lat-visit' => 'https://triphunter.vn/api/v2/places/28/1/items?article_type=1&currency=VND&page=1&currency=VND', //destination: 2
        'da-lat-food' => 'https://triphunter.vn/api/v2/places/28/2/items?article_type=2&currency=VND&page=1&currency=VND', //destination: 2
        'ha-noi-visit' => 'https://triphunter.vn/api/v2/places/58/1/items?article_type=1&currency=VND&page=1&currency=VND', //destination: 13
        'ha-noi-food' => 'https://triphunter.vn/api/v2/places/58/2/items?article_type=2&currency=VND&page=1&currency=VND', //destination: 13
        'ha-noi-rest' => 'https://triphunter.vn/api/v2/places/58/4/items?article_type=4&currency=VND&page=1&currency=VND', //destination: 13
    ];
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

    public function actionGetPlace($place_type, $destination, $page) {
        $url = 'https://triphunter.vn/api/v2/places/58/4/items?article_type=4&currency=VND&page=' .$page. '&currency=VND';
        $response = self::GetData($url);
        if($response->isOk) {
            $transaction = Yii::$app->db->beginTransaction();
            try{
                $data = $response->data;
                foreach($data['data'] as $place) {
                    $object = self::SavePlace($place, $place_type, $destination);
                    if($object) {
                        self::SavePhotoRef($object, $place['photos']);
                    }
                }

                $transaction->commit();
                if($page < 40) {
                    $page++;
                    return $this->redirect(CMSConfig::getUrl('get-data-triphunter/get-place?place_type=' .$place_type. '&destination=' .$destination. '&page=' .$page));
                }
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
        $destination = Destination::findOne(['slug' => $data['slug']]);
        if(!$destination) {
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
        }
        return false;
    }

    public function SavePlace($data, $place_type, $destination) {
        $place = Place::findOne(['slug' => $data['slug']]);
        if(!$place) {
            $photo_name = $data['slug'] . '-thumb';
            $photo = self::SavePhoto($data['cover_photo_thumb']['image_url'], $photo_name);
            $place = new Place([
                'name' => $data['name'],
                'lat' => strval($data['lat']),
                'lng' => strval($data['lng']),
                'subtitle' => $data['name'],
                'slug' => $data['slug'],
                'status' => 1,
                'delete' => 1,
                'created_by' => Yii::$app->user->id,
                'viewed' => 0,
                'thumbnail' => $photo->path,
                'time_stay' => isset($data['stay_for']) ? $data['stay_for'] : 0,
                'place_type_id' => $place_type,
                'destination_id' => $destination,
                'open_times' => self::FormatOpenTimes($data['open_times']),
                'phone' => $data['contact_info']['phone_number'],
                'address' => $data['contact_info']['address'],
                'price' => $data['max_rate'],
                'description' => $data['content'],
            ]);
    
            if($place->save()) {
                return $place;
            }
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
            if($idx >= 10) {
                break;
            }
        }
    }

    public function SavePhoto($url, $name) {
        $slug = uniqid() . '-' . $name;
        $path = $slug . '.jpg';
        $DIR = 'uploads/';
        if(!preg_match('/^http/', $url)) {
            $url = 'https://triphunter.vn' . $url;
        }
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

    public function FormatOpenTimes($opentimes) {
        $weeks = [];
        foreach($opentimes as $date) {
            unset($date['id']);
            unset($date['created_at']);
            array_push($weeks, $date);
        }
        return json_encode($weeks, true);
    }
}