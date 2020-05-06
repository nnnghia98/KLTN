<?php

namespace app\modules\cms\services;

use app\modules\cms\models\Destination;
use app\modules\cms\services\InteractiveService;
use yii\db\Query;

class DestinationService
{
    public static $DELETE = [
        'ALIVE' => 1,
        'DELETED' => 0
    ];

    public static $STATUS = [
        'ACTIVE' => 1,
        'DEACTIVE' => 0
    ];

    public static $RESPONSE = [
        'ERROR_LIST' => 'Không thể lấy danh sách điểm đến'
    ];

    public static $OBJECT_TYPE = 'app\modules\cms\models\Destination';

    public static function GetTopDestinations($limit) {
        $interactive = InteractiveService::GetQueryInteractive(self::$OBJECT_TYPE);
        $destinations = (new Query())
                        ->select(['d.name', 'd.thumbnail', 'd.subtitle', 'd.slug', 'i.avg_rating', 'i.count_rating'])
                        ->from(['d' => 'destination'])
                        ->leftJoin(['i' => $interactive], 'd.id = i.object_id')
                        ->where(['and', ['status' => self::$STATUS['ACTIVE']], ['delete' => self::$DELETE['ALIVE']]])
                        ->orderBy('i.avg_rating DESC')
                        ->limit($limit)
                        ->all();
        return $destinations;
    }

    public static function GetListAppPage($page, $perpage, $keyword, $comment, $rating) {
        $interactive = InteractiveService::GetQueryInteractive(self::$OBJECT_TYPE);
        $query = (new Query())
                        ->select(['d.*', 'i.*'])
                        ->from(['d' => 'destination'])
                        ->leftJoin(['i' => $interactive], 'd.id = i.object_id')
                        ->where(['and', ['status' => self::$STATUS['ACTIVE']], ['delete' => self::$DELETE['ALIVE']]])
                        ->andWhere(['like', 'LOWER(name)', strtolower($keyword)]);
        if($rating) {
            $query->andWhere(['>=', 'i.avg_rating', $rating]);
        }

        $total = $query->select('COUNT(*)')->column();
        
        list($limit, $offset) = SiteService::GetLimitAndOffset($page, $perpage);
                        
        $destinations = $query->select(['d.*', 'i.*'])
                        ->orderBy(['i.count_comment' => $comment ? SORT_DESC : SORT_ASC])
                        ->limit($limit)
                        ->offset($offset)
                        ->all();
        $pagination = SiteService::CreatePaginationMetadata($total[0], $page, $perpage, count($destinations));
        return [$destinations, $pagination];
    }

    public static function GetCategories() {
        $destinations = Destination::find()
                        ->select(['id', 'name'])
                        ->where(['and', ['status' => self::$STATUS['ACTIVE']], ['delete' => self::$DELETE['ALIVE']]])
                        ->orderBy('name')
                        ->all();

        $categories = [];
        foreach($destinations as $d) {
            array_push($categories, [
                'label' => $d['name'],
                'code' => $d['id']
            ]);
        }
        return $categories;
    }

    public static function GetDestinationById($id) {
        $destination = Destination::find()
                        ->where(['and', ['status' => self::$STATUS['ACTIVE']], ['delete' => self::$DELETE['ALIVE']]])
                        ->andWhere(['id' => $id])
                        ->one();

        return $destination;
    }
}