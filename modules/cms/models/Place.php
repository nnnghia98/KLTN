<?php

namespace app\modules\cms\models;

use Yii;

/**
 * This is the model class for table "place".
 *
 * @property int $id
 * @property string $name
 * @property string $subtitle
 * @property string $description
 * @property string $lat
 * @property string $lng
 * @property int $time_open
 * @property int $time_close
 * @property int $destination_id
 * @property int $time_stay
 * @property int $place_type_id
 * @property string $phone
 * @property string $address
 * @property string $slug
 * @property int $status
 * @property int $delete
 * @property string $created_at
 * @property int $created_by
 * @property int $viewed
 * @property string $thumbnail
 * @property string $price
 * @property string $open_times
 */
class Place extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'place';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description', 'open_times'], 'string'],
            [['time_open', 'time_close', 'destination_id', 'time_stay', 'place_type_id', 'status', 'delete', 'created_by', 'viewed'], 'default', 'value' => null],
            [['time_open', 'time_close', 'destination_id', 'time_stay', 'place_type_id', 'status', 'delete', 'created_by', 'viewed'], 'integer'],
            [['created_at'], 'safe'],
            [['price'], 'number'],
            [['name', 'subtitle', 'lat', 'lng', 'phone', 'address', 'slug', 'thumbnail'], 'string', 'max' => 255],
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
            'time_open' => 'Time Open',
            'time_close' => 'Time Close',
            'destination_id' => 'Destination ID',
            'time_stay' => 'Time Stay',
            'place_type_id' => 'Place Type ID',
            'phone' => 'Phone',
            'address' => 'Address',
            'slug' => 'Slug',
            'status' => 'Status',
            'delete' => 'Delete',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'viewed' => 'Viewed',
            'thumbnail' => 'Thumbnail',
            'price' => 'Price',
            'open_times' => 'Open Times',
        ];
    }
}
