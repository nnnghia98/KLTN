<?php

namespace app\modules\cms\models;

use Yii;

/**
 * This is the model class for table "destination".
 *
 * @property int $id
 * @property string $name
 * @property string $subtitle
 * @property string $description
 * @property string $lat
 * @property string $lng
 * @property string $slug
 * @property int $status
 * @property int $delete
 * @property string $created_at
 * @property int $created_by
 * @property int $viewed
 * @property string $thumbnail
 */
class Destination extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'destination';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description'], 'string'],
            [['status', 'delete', 'created_by', 'viewed'], 'default', 'value' => null],
            [['status', 'delete', 'created_by', 'viewed'], 'integer'],
            [['created_at'], 'safe'],
            [['name', 'subtitle', 'lat', 'lng', 'slug', 'thumbnail'], 'string', 'max' => 255],
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
            'subtitle' => 'Subtitle',
            'description' => 'Description',
            'lat' => 'Lat',
            'lng' => 'Lng',
            'slug' => 'Slug',
            'status' => 'Status',
            'delete' => 'Delete',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'viewed' => 'Viewed',
            'thumbnail' => 'Thumbnail',
        ];
    }
}
