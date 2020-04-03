<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 07-Mar-19
 * Time: 9:22 AM
 */

namespace app\models;


use app\modules\cms\models\AuthUser;
use app\modules\cms\models\AuthUserInfo;
use app\modules\cms\services\AuthService;
use app\modules\cms\services\SiteService;
use yii\base\Model;
use Yii;

class RegisterForm extends Model
{
    public $username;
    public $password;
    public $cpassword;
    public $fullname;

    public function rules()
    {
        return [
            [['username', 'password', 'cpassword', 'fullname'], 'required', 'message' => '{attribute} không được để trống'],
            [['password', 'cpassword'], 'string', 'min' => 6, 'max' => 15, 'tooLong' => '{attribute} tối đa 15 ký tự', 'tooShort' => '{attribute} tối thiểu 6 ký tự'],
            [['cpassword'], 'compare', 'compareAttribute' => 'password', 'message' => AuthService::$AUTH_RESPONSES['PASSWORD_MATCH']],
            ['username', 'email', 'message' => AuthService::$AUTH_RESPONSES['EMAIL_FORMAT']],
            ['username', 'validateEmail'],
        ];
    }

    public function validateEmail($attribute, $params)
    {
        if (AuthService::CheckUsernameExist($this->username)) {
            $this->addError($attribute, AuthService::$AUTH_RESPONSES['EMAIL_EXIST']);
        }
    }

    public function register()
    {
        if ($this->validate()) {
            $authUser = new AuthUser([
                'username' => $this->username,
                'fullname' => $this->fullname,
                'password' => Yii::$app->getSecurity()->generatePasswordHash($this->password),
                'auth_role_id' => AuthService::$AUTH_ROLE['USER'],
                'status' => AuthService::$AUTH_STATUS['ACTIVE'],
                'delete' => AuthService::$AUTH_DELETE['ALIVE'],
                'confirmed' => AuthService::$AUTH_CONFIRM['CONFIRMED'],
                'type' => AuthService::$AUTH_TYPE['PUBLIC'],
                'slug' => SiteService::uniqid()
            ]);

            $authUser->generateAuthKey();
            $authUser->generateAccessToken();

            if($authUser->save()) {
                $userInfo = new AuthUserInfo([
                    'auth_user_id' => $authUser->id
                ]);
                $userInfo->save();
                SiteService::WriteLog($authUser->id, SiteService::$ACTIVITIES['REGISTER']);

                return true;
            }
        }
        return false;
    }

    public function attributeLabels()
    {
        return [
            'username' => Yii::t('app', 'Email'),
            'password' => Yii::t('app', 'Mật khẩu'),
            'cpassword' => Yii::t('app', 'Xác nhận mật khẩu'),
            'fullname' => Yii::t('app', 'Họ và tên')
        ];
    }
}