<?php

namespace app\modules\cms\services;

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

    public static function GetListAppPage($page, $perpage, $keyword, $comment, $rating) {
        $interactive = InteractiveService::GetQueryInteractive(self::$OBJECT_TYPE);
        $query = (new Query())
                        ->select(['d.*', 'i.*'])
                        ->from(['d' => 'destination'])
                        ->leftJoin(['i' => $interactive], 'd.id = i.object_id')
                        ->where(['and', ['status' => self::$STATUS['ACTIVE']], ['delete' => self::$DELETE['ALIVE']]])
                        ->andWhere(['like', 'name', $keyword]);
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
}