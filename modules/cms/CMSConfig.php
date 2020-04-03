<?php

namespace app\modules\cms;


class CMSConfig
{
    public static $CONFIG = [
        'siteName' => 'Travel Sharing',
        'adminSidebar' => [
            'system' => [
                'name' => 'Hệ thống',
                'icon' => 'icon-stack2',
                'url' => 'cms/system'
            ],
            'user' => [
                'name' => 'Người dùng',
                'icon' => 'icon-users',
                'url' => 'cms/user'
            ],
            'destination' => [
                'name' => 'Điểm đến',
                'icon' => 'icon-location4',
                'url' => 'cms/destination'
            ],
            'place' => [
                'name' => 'Địa điểm',
                'icon' => 'icon-grid52',
                'url' => 'cms/place'
            ],
            'plan' => [
                'name' => 'Lịch trình',
                'icon' => 'icon-paperplane',
                'url' => 'cms/plan'
            ]
        ],
    ];

    public static $ROOT_URL = 'cms/';
    public static $URL_KEY = 'hcmgispointclound2020';

    public static function getUrl($url)
    {
        return \Yii::$app->homeUrl . self::$ROOT_URL . $url;
    }
}