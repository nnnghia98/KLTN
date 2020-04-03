<?php

namespace app\modules\cms\services;

use app\modules\cms\models\ActivitiesLog;
use app\modules\cms\models\AuthUser;
use app\modules\cms\models\VietnamDistricts;
use app\modules\cms\models\VietnamProvinces;
use Yii;
use app\modules\cms\services\ImagesService;
use DateTime;
use Exception;

class SiteService 
{
    public static $ACTIVITIES = [
        'REGISTER' => 'Đăng ký tài khoản',
        'CONFIRM_EMAIL' => 'Xác nhận email',
        'LOGIN' => 'Đăng nhập hệ thống',
        'LOGOUT' => 'Đăng xuất hệ thống',
        'CREATE_USER' => 'Tạo tài khoản người dùng',
        'DELETE_USER' => 'Xóa tài khoản người dùng',
        'UPDATE_USER' => 'Chỉnh sửa tài khoản người dùng',
        'FOLLOW_USER' => 'Theo dõi người dùng',
        'UNFOLLOW_USER' => 'Bỏ theo dõi người dùng',
        'CHANGE_ROLE_USER' => 'Thay đổi quyền người dùng',
        'EDIT_PROFILE' => 'Chỉnh sửa thông tin cá nhân',
        'CHANGE_PASSWORD' => 'Thay đổi mật khẩu',
        'RESET_PASSWORD' => 'Yêu cầu đổi mật khẩu',
    ];

    public static function CommandQuery($query)
    {
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand($query);
        return $command;
    }

    public static function CommandQueryOne($query)
    {
        $command = self::CommandQuery($query);
        return $command->queryOne();
    }

    public static function CommandQueryAll($query)
    {
        $command = self::CommandQuery($query);
        return $command->queryAll();
    }

    public static function CommandQueryColumn($query)
    {
        $command = self::CommandQuery($query);
        return $command->queryColumn();
    }

    public static function CreatePaginationMetadata($total, $page, $perpage, $count)
    {
        $total = intval($total);
        $page = intval($page);
        $pages = ceil($total / $perpage);
        $from = ($page - 1) * $perpage + 1;
        $to = $from + $count - 1;
        $pagination = [
            'total' => $total,
            'perpage' => $perpage,
            'current' => $page,
            'pages' => $pages,
            'from' => $from,
            'to' => $to,
            'links' => []
        ];

        //first link
        array_push($pagination['links'], 1);
        // ...
        if ($page - 1 > 2) {
            array_push($pagination['links'], '...');
        }
        //before current
        for ($i = 1; $i < $page; $i++) {
            if ($page - $i <= 2) {
                array_push($pagination['links'], $i);
            }
        }
        //current
        array_push($pagination['links'], 'current');
        //aftercurrent
        for ($i = $page + 1; $i <= $pages; $i++) {
            if ($i - $page <= 2) {
                array_push($pagination['links'], $i);
            }
        }
        // ...
        if ($pages - $page > 2) {
            array_push($pagination['links'], '...');
        }
        //last
        array_push($pagination['links'], $pages);
        return $pagination;
    }

    public static function GetLimitAndOffset($page, $perpage)
    {
        $limit = $perpage;
        $offset = ($page - 1) * $limit;
        return [$limit, $offset];
    }

    public static function ArrayIndexBy($array, $column)
    {
        $arrIdxBy = [];
        foreach ($array as $item) {
            $arrIdxBy[$item[$column]] = $item;
        }

        return $arrIdxBy;
    }

    public static function TimeAgo($date_string)
    {
        $timediff = time() - strtotime($date_string);

        $days = intval($timediff / 86400);
        $remain = $timediff % 86400;
        $hours = intval($remain / 3600);
        $remain = $remain % 3600;
        $mins = intval($remain / 60);
        $secs = $remain % 60;

        if ($secs >= 0) $timestring = $secs . " seconds ago";
        if ($mins > 0) $timestring = $mins . " mins ago";
        if ($hours > 0) $timestring = $hours . " hours ago";
        if ($days > 0) $timestring = $days . " days ago";

        return $timestring;
    }

    //0: array| 1,2,3...: fields to group
    public static function ArrayGroupBy()
    {
        
        $numargs = func_num_args();
        if($numargs > 1) {
            $args = func_get_args();
            $array = $args[0];
            $field = $args[1];
            $arrGroupBy = [];
            foreach ($array as $val1) {
                if (!isset($arrGroupBy[$val1[$field]])) {
                    $arrGroupBy[$val1[$field]] = [];
                }
                array_push($arrGroupBy[$val1[$field]], $val1);
            }

            array_splice($args, 0, 2);
            if(count($args) > 0) {
                foreach($arrGroupBy as $key => &$val2) {
                    $arr = array_merge([$val2], $args);
                    // array_unshift($args, $val2);
                    $arrGroupBy[$key] = call_user_func_array(__NAMESPACE__ . '\SiteService::ArrayGroupBy', $arr);
                }
            }

            return $arrGroupBy;
        }
        return null;
    }

    public static function GetSiteUrl() {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'];
        return $protocol.$domainName;
    }

    public static function ReverseArray($array, $bool = false) {
        $newArr = $bool ? array_reverse($array, $bool) : array_reverse($array);
        return $newArr;
    }

    public static function ReverseDate($date) {
        $arr = explode('-', $date);
        $arr = self::reverseArray($arr);
        $newDate = implode('/', $arr);
        return $newDate;
    }

    public static function uniqid() {
        return md5(uniqid(rand(), true));
    }

    public static function ConvertStringToSlug($string, $separator = '-')
    {
        $slug = self::ConvertVNToNonVN($string);
        $slug = strtolower(trim($slug));
        $slug = preg_replace('/[^a-z0-9-]/', $separator, $slug);
        $slug = preg_replace('/-+/', $separator, $slug);
        return $slug;
    }

    public static function RandomString() {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $string = [];
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $string[] = $alphabet[$n];
        }
        return implode($string);
    }

    public static function ConvertVNToNonVN($str)
    {
        $unicode = array(
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D' => 'Đ',
            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        );

        foreach ($unicode as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        $str = str_replace(' ', '-', $str);
        return $str;
    }

    public static function SendEmailConfirmEmail($model) {
        try {
            $user = AuthUser::findOne(['username' => $model->username]);
            $urlConfirm = self::getSiteUrl() . Yii::$app->homeUrl . 'site/confirm-email?auth=' .$user->auth_key. '&token=' .$user->access_token;
            $mail = Yii::$app->mailer->compose([
                    'html' => 'views/confirmemail-html',
                    'text' => 'views/confirmemail-text'
                ], ['user' => $model->fullname, 'urlConfirm' => $urlConfirm])
                ->setTo([$model->username => $model->fullname])
                ->setSubject('Xác nhận email đăng ký tài khoản Travel Sharing')
                ->send();
            return $mail;
        } catch(Exception $e) {
            return $e;
        }
    }

    public static function SendEmailInstruction($model, $password) {
        try {
            $urlConfirm = self::getSiteUrl() . Yii::$app->homeUrl . 'site/confirm-email?auth=' .$model->auth_key. '&token=' .$model->access_token;
            $mail = Yii::$app->mailer->compose([
                    'html' => 'views/instructionlogin-html',
                    'text' => 'views/instructionlogin-text'
                ], ['model' => $model, 'password' => $password, 'urlConfirm' => $urlConfirm])
                ->setTo([$model->username => $model->fullname])
                ->setSubject('Xác nhận email đăng ký tài khoản Travel Sharing')
                ->send();
            return $mail;
        } catch(Exception $e) {
            return $e;
        }
    }

    public static function WriteLog($userid, $activity, $objectid = null, $objecttype = null, $objectname = null, $note = null) {
        $userid = $userid ? $userid : Yii::$app->user->id;
        $log = new ActivitiesLog([
            'auth_user_id' => $userid,
            'activity' => $activity,
            'object_type' => $objecttype,
            'object_id' => $objectid,
            'object_name' => $objectname,
            'note' => $note
        ]);

        $log->save();
    }
}