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
}