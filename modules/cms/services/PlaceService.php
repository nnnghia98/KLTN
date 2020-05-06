<?php

namespace app\modules\cms\services;

use app\modules\cms\services\InteractiveService;
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

    public static $OBJECT_TYPE = 'app\modules\cms\models\Place';

    public static function GetTopPlaces($limit) {
        $interactive = InteractiveService::GetQueryInteractive(self::$OBJECT_TYPE);
        $places = (new Query())
                        ->select(['p.name', 'p.thumbnail', 'p.subtitle', 'p.slug', 'i.avg_rating', 'i.count_rating'])
                        ->from(['p' => 'place'])
                        ->leftJoin(['i' => $interactive], 'p.id = i.object_id')
                        ->where(['and', ['status' => self::$STATUS['ACTIVE']], ['delete' => self::$DELETE['ALIVE']]])
                        ->orderBy('i.avg_rating DESC')
                        ->limit($limit)
                        ->all();
        return $places;
    }

    public static function GetFoodListAppPage($page, $perpage, $keyword, $comment, $rating, $destination) {
        $interactive = InteractiveService::GetQueryInteractive(self::$OBJECT_TYPE);
        $query = (new Query())
                        ->select(['p.*', 'i.*'])
                        ->from(['p' => 'place'])
                        ->leftJoin(['i' => $interactive], 'p.id = i.object_id')
                        ->where(['and', ['status' => self::$STATUS['ACTIVE']], ['delete' => self::$DELETE['ALIVE']]])
                        ->andWhere(['place_type_id' => self::$TYPE['FOOD']])
                        ->andWhere(['like', 'LOWER(name)', strtolower($keyword)]);
        if($destination) {
            $query->andWhere(['destination_id' => $destination]);
        }
        if($rating) {
            $query->andWhere(['>', 'i.avg_rating', $rating]);
        }
        $total = $query->select('COUNT(*)')->column();
        
        list($limit, $offset) = SiteService::GetLimitAndOffset($page, $perpage);
                        
        $foods = $query->select(['p.*', 'i.*'])
                        ->orderBy(['i.count_comment' => $comment ? SORT_DESC : SORT_ASC])
                        ->limit($limit)
                        ->offset($offset)
                        ->all();
        $pagination = SiteService::CreatePaginationMetadata($total[0], $page, $perpage, count($foods));
        return [$foods, $pagination];
    }

    public static function GetVisitListAppPage($page, $perpage, $keyword, $comment, $rating, $destination) {
        $interactive = InteractiveService::GetQueryInteractive(self::$OBJECT_TYPE);
        $query = (new Query())
                        ->select(['p.*', 'i.*'])
                        ->from(['p' => 'place'])
                        ->leftJoin(['i' => $interactive], 'p.id = i.object_id')
                        ->where(['and', ['status' => self::$STATUS['ACTIVE']], ['delete' => self::$DELETE['ALIVE']]])
                        ->andWhere(['place_type_id' => self::$TYPE['VISIT']])
                        ->andWhere(['like', 'LOWER(name)', strtolower($keyword)]);
        if($destination) {
            $query->andWhere(['destination_id' => $destination]);
        }
        if($rating) {
            $query->andWhere(['>', 'i.avg_rating', $rating]);
        }
        $total = $query->select('COUNT(*)')->column();
        
        list($limit, $offset) = SiteService::GetLimitAndOffset($page, $perpage);
             
        $visits = $query->select(['p.*', 'i.*'])
                        ->orderBy(['i.count_comment' => $comment ? SORT_DESC : SORT_ASC])
                        ->limit($limit)
                        ->offset($offset)
                        ->all();
        $pagination = SiteService::CreatePaginationMetadata($total[0], $page, $perpage, count($visits));
        return [$visits, $pagination];
    }
    public static function GetRestListAppPage($page, $perpage, $keyword, $comment, $rating, $destination) {
        $interactive = InteractiveService::GetQueryInteractive(self::$OBJECT_TYPE);
        $query = (new Query())
                        ->select(['p.*', 'i.*'])
                        ->from(['p' => 'place'])
                        ->leftJoin(['i' => $interactive], 'p.id = i.object_id')
                        ->where(['and', ['status' => self::$STATUS['ACTIVE']], ['delete' => self::$DELETE['ALIVE']]])
                        ->andWhere(['place_type_id' => self::$TYPE['REST']])
                        ->andWhere(['like', 'LOWER(name)', strtolower($keyword)]);
        if($destination) {
            $query->andWhere(['destination_id' => $destination]);
        }
        if($rating) {
            $query->andWhere(['>', 'i.avg_rating', $rating]);
        }
        $total = $query->select('COUNT(*)')->column();
        
        list($limit, $offset) = SiteService::GetLimitAndOffset($page, $perpage);
        
        $rests = $query->select(['p.*', 'i.*'])
                        ->orderBy(['i.count_comment' => $comment ? SORT_DESC : SORT_ASC])
                        ->limit($limit)
                        ->offset($offset)
                        ->all();
        $pagination = SiteService::CreatePaginationMetadata($total[0], $page, $perpage, count($rests));
        return [$rests, $pagination];
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
}