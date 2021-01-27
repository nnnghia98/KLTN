<?php

namespace app\modules\cms\services;

use app\modules\cms\models\Interactive;
use app\modules\cms\services\InteractiveService;
use Yii;
use yii\db\Query;

class PlaceService
{
    public static $DELETE = [
        'ALIVE' => 1,
        'DELETED' => 0
    ];

    public static $STATUS = [
        'ACTIVE' => 1,
        'DEACTIVE' => 0
    ];

    public static $TYPE = [
           'VISIT' => 1,
           'FOOD'  => 2,
           'REST'  => 3
    ];

    public static $RESPONSE = [
        'ERROR_LIST' => 'Không thể lấy danh sách địa điểm '
    ];

    public static $ORDERMAP = [
        'rating-desc' => [
            'column' => 'avg_rating',
            'sort' => 'DESC'
        ],
        'rating-asc' => [
            'column' => 'avg_rating',
            'sort' => 'ASC'
        ]
    ];

    public static $OBJECT_TYPE = 'app\modules\cms\models\Place';

    public static function GetTopPlaces($limit) {
        $interactive = InteractiveService::GetQueryInteractive(self::$OBJECT_TYPE);
        $places = (new Query())
                        ->select(['p.name', 'p.thumbnail', 'p.address', 'p.slug', 'i.avg_rating', 'i.count_rating'])
                        ->from(['p' => 'place'])
                        ->leftJoin(['i' => $interactive], 'p.id = i.object_id')
                        ->where(['and', ['status' => self::$STATUS['ACTIVE']], ['delete' => self::$DELETE['ALIVE']]])
                        ->orderBy('i.avg_rating DESC')
                        ->limit($limit)
                        ->all();
        return $places;
    }

    public static function GetPlaceListAppPage($page, $perpage, $keyword, $destination, $order, $type) {
        $order = self::$ORDERMAP[$order];
        $interactive = InteractiveService::GetQueryInteractive(self::$OBJECT_TYPE);
        $query = (new Query())
                        ->select(['p.*', 'i.*'])
                        ->from(['p' => 'place'])
                        ->leftJoin(['i' => $interactive], 'p.id = i.object_id')
                        ->where(['and', ['status' => self::$STATUS['ACTIVE']], ['delete' => self::$DELETE['ALIVE']]])
                        ->andWhere(['place_type_id' => $type])
                        ->andWhere(['like', 'LOWER(name)', strtolower($keyword)]);
        if($destination) {
            $query->andWhere(['destination_id' => $destination]);
        }

        $total = $query->select('COUNT(*)')->column();
        
        list($limit, $offset) = SiteService::GetLimitAndOffset($page, $perpage);
                        
        $places = $query->select(['p.*', 'i.*'])
                        ->orderBy($order['column'] . ' ' . $order['sort'])
                        ->limit($limit)
                        ->offset($offset)
                        ->all();
        $pagination = SiteService::CreatePaginationMetadata($total[0], $page, $perpage, count($places));
        return [$places, $pagination];
    }

    public static function GetPlacesAppPage($page, $perpage, $keyword, $destination, $type, $sort, $lat, $lng) {
        list($limit, $offset) = SiteService::GetLimitAndOffset($page, $perpage);
        $interactive = InteractiveService::GetStringQueryInteractive(self::$OBJECT_TYPE);
        if(!$lat || !$lng) {
            $query = "SELECT *
                        FROM (
                            SELECT *
                            FROM place
                            WHERE name LIKE '%$keyword%' AND destination_id = $destination AND place_type_id = $type AND status = 1 AND delete = 1
                            ) as p
                        LEFT JOIN ($interactive) i ON i.object_id = p.id
                        ORDER BY $sort 
                        LIMIT $limit OFFSET $offset";
        } else {
            $latlngs = "SELECT ST_GeographyFromText('SRID=4326;POINT($lng $lat)')";
            $query = "SELECT *
                        FROM (
                            SELECT place.*, ST_Distance(t.x, ST_SetSRID(ST_MakePoint(place.lng::double precision, place.lat::double precision),4326)::geography) AS dist
                            FROM place, ($latlngs) t(x)
                            WHERE destination_id = $destination AND place_type_id = $type AND name LIKE '%$keyword%' AND status = 1 AND delete = 1  AND place.lat != '$lat' AND place.lng != '$lng'
                            AND ST_DWithin(t.x, ST_SetSRID(ST_MakePoint(place.lng::double precision, place.lat::double precision),4326)::geography, 200000)
                            ) as p
                        LEFT JOIN ($interactive) i ON i.object_id = p.id
                        ORDER BY dist 
                        LIMIT $limit OFFSET $offset";
        }

        $places = SiteService::CommandQueryAll($query);
        $total = self::CountTotalPlaceOfList($destination, $type, $keyword, $lat, $lng);
        $pagination = SiteService::CreatePaginationMetadata($total, $page, $perpage, count($places));

        return [$places, $pagination];
    }

    public static function CountTotalPlaceOfList($destination, $type, $keyword, $lat, $lng) {
        if($lat === '' || $lng === '') {
            $query = "SELECT COUNT(*)
                        FROM place
                        WHERE destination_id = $destination AND place_type_id = $type AND name LIKE '%" . $keyword . "%' AND status = 1 AND delete = 1";
        } else {
            $latlngs = "SELECT ST_GeographyFromText('SRID=4326;POINT($lng $lat)')";
            $query = "SELECT COUNT(*)
                        FROM place, ($latlngs) t(x)
                        WHERE destination_id = $destination AND place_type_id = $type AND name LIKE '%" . $keyword . "%' AND status = 1 AND delete = 1
                        AND ST_DWithin(t.x, ST_SetSRID(ST_MakePoint(place.lng::double precision, place.lat::double precision),4326)::geography, 200000)";
        }

        $total = SiteService::CommandQueryColumn($query);
        return $total[0];
    }

    public static function GetPlaceBySlug($slug) {
        $interactive = InteractiveService::GetQueryInteractive(self::$OBJECT_TYPE);
        $place = (new Query())
                        ->select(['d.*', 'i.*'])
                        ->from(['d' => 'place'])
                        ->leftJoin(['i' => $interactive], 'd.id = i.object_id')
                        ->where(['and', ['status' => self::$STATUS['ACTIVE']], ['delete' => self::$DELETE['ALIVE']]])
                        ->andWhere(['slug' => $slug])
                        ->one();

        return $place;
    }

    public static function GetNearbyPlaces($lat, $lng, $type) {
        $interactive = InteractiveService::GetStringQueryInteractive(PlaceService::$OBJECT_TYPE);
        $latlngs = "SELECT ST_GeographyFromText('SRID=4326;POINT($lng $lat)')";
        $query = "SELECT p.name, p.thumbnail, p.address, p.slug, i.avg_rating, i.count_rating
                        FROM (
                            SELECT place.id, place.name, place.thumbnail, place.address, place.slug, ST_Distance(t.x, ST_SetSRID(ST_MakePoint(place.lng::double precision, place.lat::double precision),4326)::geography) AS dist
                            FROM place, ($latlngs) t(x)
                            WHERE place_type_id = $type AND status = 1 AND delete = 1  AND place.lat != '$lat' AND place.lng != '$lng'
                            AND ST_DWithin(t.x, ST_SetSRID(ST_MakePoint(place.lng::double precision, place.lat::double precision),4326)::geography, 200000)
                            ) as p
                        LEFT JOIN ($interactive) i ON i.object_id = p.id
                        ORDER BY dist LIMIT 3";
                        
        $places = SiteService::CommandQueryAll($query);
        return $places;
    }

    public static function GetRelatePlans($id) {
        $planids = (new Query())
                        ->select(['DISTINCT(plan_id)'])
                        ->from('plan_detail')
                        ->where(['place_id' => $id])
                        ->column();
        $plans = (new Query())
                        ->select(['p.name', 'p.thumbnail', 'p.slug', 'u.avatar as author_avatar', 'u.slug as author_slug', 'u.fullname as author'])
                        ->from(['p' => 'plan'])
                        ->leftJoin(['u' => 'auth_user'], 'p.created_by = u.id')
                        ->where(['and', ['p.status' => self::$STATUS['ACTIVE']], ['p.delete' => self::$DELETE['ALIVE']]])
                        ->andWhere(['p.id' => $planids])
                        ->orderBy('p.created_at DESC')
                        ->limit(3)
                        ->all();
        return $plans;
    }

    public static function GetComments($id, $page, $perpage) {
        list($limit, $offset) =  SiteService::GetLimitAndOffset($page, $perpage);
        $comments = (new Query())
                        ->select(['i.comment', 'i.rating', 'i.created_at', 'u.avatar as author_avatar', 'u.slug as author_slug', 'u.fullname as author'])
                        ->from('interactive as i')
                        ->leftJoin(['u' => 'auth_user'], 'i.created_by = u.id')
                        ->where(['and', ['i.object_type' => self::$OBJECT_TYPE], ['i.object_id' => $id]])
                        ->orderBy('i.created_at DESC')
                        ->limit($limit)
                        ->offset($offset)
                        ->all();
        return $comments;
    }

    public static function GetInteractiveOfCurrentUser($id) {
        if(!Yii::$app->user->isGuest) {
            $userid = Yii::$app->user->id;
            $interactive = (new Query())
                        ->select(['comment', 'rating'])
                        ->from('interactive')
                        ->where(['and', ['object_type' => self::$OBJECT_TYPE], ['object_id' => $id]])
                        ->andWhere(['created_by' => $userid])
                        ->one();
            if($interactive) {
                return [
                    'star' => $interactive['rating'],
                    'comment' => $interactive['comment']
                ];
            }
        }
        
        return [
            'star' => 0,
            'comment' => "",
        ];
    }

    public static function SubmitComment($data) {
        if(!Yii::$app->user->isGuest) {
            $id = $data['id'];
            $star = intval($data['star']);
            $comment = $data['comment'];
            $userid = Yii::$app->user->id;

            if(!$comment) {
                return 'Nội dung bình luận không được để trống';
            }

            if($star > 5 || $star < 1) {
                return 'Giá trị đánh giá không khả dụng';
            }

            $interactive = Interactive::find()
                        ->where(['and', ['object_id' => $id], ['object_type' => self::$OBJECT_TYPE]])
                        ->andWhere(['created_by' => $userid])
                        ->one();

            if($interactive) {
                $interactive->comment = $comment;
                $interactive->rating = $star;
            } else {
                $interactive = new Interactive([
                    'object_type' => self::$OBJECT_TYPE,
                    'object_id' => $id,
                    'created_by' => $userid,
                    'rating' => $star,
                    'comment' => $comment,
                ]);
            }

            if($interactive->save()) {
                return true;
            } else {
                return 'Không thể lưu bình luận của bạn';
            }
        }

        return 'Bạn cần đăng nhập để thực hiện chức năng này';
    }
}