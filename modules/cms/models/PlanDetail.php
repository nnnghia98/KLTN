<?php

namespace app\modules\cms\models;

use Yii;

/**
 * This is the model class for table "plan_detail".
 *
 * @property int $id
 * @property int $plan_id
 * @property int $place_id
 * @property string $place_name
 * @property int $date_index
 * @property string $lat
 * @property string $lng
 * @property int $time_start
 * @property int $time_move
 * @property int $time_stay
 * @property double $distance
 * @property int $move_type
 * @property int $time_free
 * @property string $note
 * @property string $thumbnail
 * @property string $experient
 * @property int $viewed
 */
class PlanDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'plan_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['plan_id', 'place_id', 'date_index', 'time_start', 'time_move', 'time_stay', 'move_type', 'time_free', 'viewed'], 'default', 'value' => null],
            [['plan_id', 'place_id', 'date_index', 'time_start', 'time_move', 'time_stay', 'move_type', 'time_free', 'viewed'], 'integer'],
            [['distance'], 'number'],
            [['note', 'experient'], 'string'],
            [['place_name', 'lat', 'lng', 'thumbnail'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'plan_id' => 'Plan ID',
            'place_id' => 'Place ID',
            'place_name' => 'Place Name',
            'date_index' => 'Date Index',
            'lat' => 'Lat',
            'lng' => 'Lng',
            'time_start' => 'Time Start',
            'time_move' => 'Time Move',
            'time_stay' => 'Time Stay',
            'distance' => 'Distance',
            'move_type' => 'Move Type',
            'time_free' => 'Time Free',
            'note' => 'Note',
            'thumbnail' => 'Thumbnail',
            'experient' => 'Experient',
            'viewed' => 'Viewed',
        ];
    }
}
