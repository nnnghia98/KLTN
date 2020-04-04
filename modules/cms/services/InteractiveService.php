<?php

namespace app\modules\cms\services;

use app\modules\cms\models\Interactive;
use Yii;
use yii\db\Query;

class InteractiveService
{
    public static function GetQueryInteractive($object_type) {
        $query = (new Query())
                    ->select(['object_id', 'COUNT(NULLIF(comment, NULL)) as count_comment', 'COUNT(NULLIF(rating, 0)) as count_rating', 'AVG(NULLIF(rating, 0)) as avg_rating', 'COUNT(NULLIF(is_like, 0)) as count_like'])
                    ->from('interactive')
                    ->where(['object_type' => $object_type])
                    ->groupBy('object_id');
        return $query;
    }
}