<?php

namespace app\modules\cms\models;

use Yii;

/**
 * This is the model class for table "plan".
 *
 * @property int $id
 * @property string $name
 * @property string $date_start
 * @property string $date_end
 * @property string $total_day
 * @property int $destination_id
 * @property string $note
 * @property string $slug
 * @property int $status
 * @property int $delete
 * @property int $created_by
 * @property string $created_at
 * @property string $routes
 * @property int $viewed
 * @property string $thumbnail
 * @property string $detail
 */
class Plan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'plan';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date_start', 'date_end', 'created_at'], 'safe'],
            [['destination_id', 'status', 'delete', 'created_by', 'viewed'], 'default', 'value' => null],
            [['destination_id', 'status', 'delete', 'created_by', 'viewed'], 'integer'],
            [['note', 'routes', 'detail'], 'string'],
            [['name', 'total_day', 'slug', 'thumbnail'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'date_start' => 'Date Start',
            'date_end' => 'Date End',
            'total_day' => 'Total Day',
            'destination_id' => 'Destination ID',
            'note' => 'Note',
            'slug' => 'Slug',
            'status' => 'Status',
            'delete' => 'Delete',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'routes' => 'Routes',
            'viewed' => 'Viewed',
            'thumbnail' => 'Thumbnail',
            'detail' => 'Detail',
        ];
    }
}
