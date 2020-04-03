<?php

namespace app\modules\cms\services;

use app\modules\cms\models\Interactive;
use Yii;
use yii\db\Query;

class InteractiveService
{
    public static function GetQueryInteractive($object_type) {
        $query = (new Query())
                    ->select(['object_id', 'COALESCE(COUNT(comment), 0) as count_comment', 'COALESCE(COUNT(rating), 0) as count_rating', 'COALESCE(AVG(rating), 0) as avg_rating', 'COALESCE(COUNT(is_like), 0) as count_like'])
                    ->from('interactive')
                    ->where(['object_type' => $object_type])
                    ->groupBy('object_id');
        return $query;
    }
}