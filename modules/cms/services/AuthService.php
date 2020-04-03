<?php

namespace app\modules\cms\services;

use app\modules\cms\models\AuthFollowing;
use app\modules\cms\models\AuthUser;
use app\modules\cms\models\AuthUserInfo;
use Exception;
use Throwable;
use Yii;
use yii\db\Query;

class AuthService
{
    public static $ACT_LOGIN = 1;
    public static $ACT_LOGOUT = 0;

    public static $AUTH_STATUS = [
        'ACTIVE' => 1,
        'DEACTIVE' => 0
    ];

    public static $AUTH_DELETE = [
        'ALIVE' => 1,
        'DELETED' => 0
    ];

    public static $AUTH_TYPE = [
        'PUBLIC' => 1,
        'PRIVATE' => 0
    ];

    public static $AUTH_CONFIRM = [
        'CONFIRMED' => 1,
        'UNCONFIRMED' => 0
    ];

    public static $AUTH_ROLE = [
        'SUPERUSER' => 1,
        'ADMIN' => 2,
        'USER' => 3
    ];

    public static $AUTH_RESPONSES = [
        'EMPTY_FIELD' => 'Vui lòng điền đầy đủ thông tin',
        'INCORRECT_PASSWORD' => 'Mật khẩu không chính xác',
        'PASSWORD_LENGTH' => 'Độ dài mật khẩu từ 6 - 15 ký tự',
        'PASSWORD_MATCH' => 'Xác nhận mật khẩu không chính xác',
        'EMAIL_EXIST' => 'Email đã được sử dụng bởi một tài khoản khác',
        'EMAIL_FORMAT' => 'Định dạng email không chính xác',
        'INCORRECT_EMAIL_PASSWORD' => 'Email hoặc mật khẩu không chính xác',
        'NOT_ENOUGH_PERMISSION' => 'Bạn không đủ quyền để thực hiện thao tác này',
        'USER_NOT_FOUND' => 'Không tìm thấy người dùng',
        'LOGIN_SUCCESS' => 'Đăng nhập thành công',
        'REGISTER_SUCCESS' => 'Đăng ký tài khoản thành công',
        'CREATE_SUCCESS' => 'Đã tạo tài khoản và gửi thông tin đăng nhập tới người dùng',
        'UNCONFIRMED' => 'Email chưa được xác nhận, vui lòng kiểm tra lại',
        'INVALID' => 'Tài khoản hiện tại không khả dụng',
        'DELETE_SUCCESS' => 'Đã xóa người dùng',
        'ACTIVE_SUCCESS' => 'Đã bỏ khóa người dùng',
        'DEACTIVE_SUCCESS' => 'Đã khóa người dùng',
        'LOGIN_SUCCESS' => 'Đăng nhập thành công',
        'REGISTER_SUCCESS' => 'Đăng ký thành công',
        'CHANGE_ROLE_SUCCESS' => 'Đã thay đổi quyền người dùng',
        'CHANGE_TYPE_SUCCESS' => 'Đã thay đổi loại tài khoản người dùng',
        'EMPTY_LIST' => 'Danh sách trống',
        'CHANGE_AVATAR_SUCCESS' => 'Đã thay đổi ảnh đại diện',
        'CHANGE_INFORMATION_SUCCESS' => 'Đã thay đổi thông tin người dùng',
        'CHANGE_PASSWORD_SUCCESS' => 'Đã thay đổi mật khẩu người dùng',
        'FOLLOW_USER' => 'Đã theo dõi người dùng',
        'UNFOLLOW_USER' => 'Đã bỏ theo dõi người dùng',
        'ERROR' => 'Có lỗi sảy ra, vui lòng thử lại sau',
    ];

    // AuthService::$AUTH_RESPONSES['UNCONFIRMED'];

    public static function CreateUser($data) {
        $fullname = $data['AuthUser']['fullname'];
        $username = $data['AuthUser']['username'];
        if(!$fullname || !$username) {
            return self::$AUTH_RESPONSES['EMPTY_FIELD'];
        } else if(self::CheckUsernameExist($username)) {
            return self::$AUTH_RESPONSES['EMAIL_EXIST'];
        } else if(!self::CheckEmailFormat($username)) {
            return self::$AUTH_RESPONSES['EMAIL_FORMAT'];
        } else {
            $model = new AuthUser();
            $model->load($data);
            $password = SiteService::RandomString();
            $model->password = Yii::$app->getSecurity()->generatePasswordHash($password);
            $model->status = self::$AUTH_STATUS['ACTIVE'];
            $model->status = self::$AUTH_STATUS['ACTIVE'];
            $model->delete = self::$AUTH_DELETE['ALIVE'];
            $model->confirmed = self::$AUTH_CONFIRM['CONFIRMED'];
            $model->slug = SiteService::uniqid();
            $model->generateAuthKey();
            $model->generateAccessToken();

            if($model->save()) {
                $userInfo = new AuthUserInfo([
                    'auth_user_id' => $model->id
                ]);
                $userInfo->save();
                SiteService::SendEmailInstruction($model, $password);
                SiteService::WriteLog(Yii::$app->user->id, SiteService::$ACTIVITIES['DELETE_USER'], $model->id, $model->className(), $model->fullname);
                return true;
            }
        }
    }

    public static function UpdateUser($data, $id) {
        $fullname = $data['AuthUser']['fullname'];
        $username = $data['AuthUser']['username'];
    }

    public static function IsSuperUser($id = null) {
        $id = $id ? $id : \Yii::$app->user->id;
        $isSuperUser = AuthUser::find()->where(['and', ['id' => $id], ['auth_role_id' => self::$AUTH_ROLE['SUPERUSER']]])->one();
        return $isSuperUser ? true : false;
    }

    public static function IsAdmin($id = null)
    {
        $id = $id ? $id : \Yii::$app->user->id;

        if(self::IsSuperUser($id)) {
            return true;
        }

        $isAdmin = AuthUser::find()->where(['and', ['id' => $id], ['auth_role_id' => self::$AUTH_ROLE['ADMIN']]])->one();
        return $isAdmin ? true : false;
    }

    public static function UserFullName()
    {
        return Yii::$app->user->identity->fullname;
    }

    public static function GetUserByUsername($username) 
    {
        $user = AuthUser::findOne(['username' => $username]);
        return $user;
    }

    public static function GetIdByUsername($username) 
    {
        $user = self::GetUserByUsername($username);
        if($user) {
            return $user->id;
        }
        return false;
    }

    public static function GetUserModel($id = null) 
    {
        $id = $id ? $id : \Yii::$app->user->id;
        $user = AuthUser::findOne($id);
        return $user;
    }

    public static function CheckUsernameExist($username)
    {
        $existUsername = AuthUser::findAll(['username' => $username]);
        if ($existUsername) {
            return true;
        }
        return false;
    }

    public static function CheckEmailFormat($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        return false;
    }

    public static function CheckPhoneFormat($phone)
    {
        if(strlen($phone) == 10 && preg_match("/^[0-9]{10}$/", $phone)) {
            return true;
        }
        return false;
    }

    public static function CheckPassword($password) 
    {
        $user = self::GetUserModel();
        return Yii::$app->getSecurity()->validatePassword($password, $user->password);
    }

    public static function CheckFollowingUser($userid) 
    {
        $currentUserId = Yii::$app->user->id;
        $following = AuthFollowing::find()->where(['and', ['auth_user_id' => $userid], ['follower_id' => $currentUserId]])->one();
        return $following ? true : false;
    }

    public static function GetUserProfile($id = null) {
        $id = $id ? $id : \Yii::$app->user->id;
        $profile = (new Query())
                        ->select(['info.*', 'auth.username', 'auth.type', 'auth.fullname', 'auth.avatar'])
                        ->from('auth_user_info as info')
                        ->leftJoin('auth_user as auth', 'auth.id = info.auth_user_id')
                        ->where(['auth.id' => $id])
                        ->one();
        return $profile;
    }

    public static function GetUserProfileBySlug($slug = null) {
        $query = (new Query())
                        ->select(['info.*', 'auth.username', 'auth.type', 'auth.fullname', 'auth.avatar', 'auth.auth_role_id', 'auth.confirmed'])
                        ->from('auth_user_info as info')
                        ->leftJoin('auth_user as auth', 'auth.id = info.auth_user_id')
                        ->where(['auth.slug' => $slug]);
        if(!self::IsAdmin()) {
            $query->andWhere(['and', 
                                ['auth.status' => self::$AUTH_STATUS['ACTIVE']], 
                                ['auth.status' => self::$AUTH_DELETE['ALIVE']],
                                ['auth.status' => self::$AUTH_TYPE['PUBLIC']],
                                ['auth.status' => self::$AUTH_CONFIRM['CONFIRMED']]]);
        }
        $profile = $query->one();
        return $profile;
    }

    public static function GetUserBySlug($slug = null) {
        $query = (new Query())
                        ->select(['fullname', 'avatar', 'id'])
                        ->from('auth_user')
                        ->where(['slug' => $slug]);
        if(!self::IsAdmin()) {
            $query->andWhere(['and', 
                            ['status' => self::$AUTH_STATUS['ACTIVE']], 
                            ['status' => self::$AUTH_DELETE['ALIVE']],
                            ['status' => self::$AUTH_TYPE['PUBLIC']],
                            ['status' => self::$AUTH_CONFIRM['CONFIRMED']]]);
        }
        $user = $query->one();
        return $user;
    }

    public static function GetUserAvatar($filename) {
        $path = Yii::$app->homeUrl . ($filename ? 'uploads/' . $filename : 'resources/images/no_avatar.jpg');
        return $path;
    }

    public static function ChangeInformation($data) {
        $userData = $data['AuthUser'];
        $userInfoData = $data['AuthUserInfo'];
        if(!$userData['fullname']) {
            return self::$AUTH_RESPONSES['EMPTY_FIELD'];
        } else {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $user = self::GetUserModel();
                $user->fullname = $userData['fullname'];

                $userInfo = AuthUserInfo::findOne(['auth_user_id' => $user->id]);
                $userInfo->birthday = $userInfoData['birthday'];
                $userInfo->gender = $userInfoData['gender'];
                $userInfo->address = $userInfoData['address'];
                $userInfo->company = $userInfoData['company'];
                $userInfo->phone = $userInfoData['phone'];

                if($user->save() && $userInfo->save()) {
                    $transaction->commit();
                    return true;
                }
            } catch(Exception $e) {
                $transaction->rollBack();
                return self::$AUTH_RESPONSES['ERROR'];
            } catch(Throwable $e) {
                $transaction->rollBack();
                return self::$AUTH_RESPONSES['ERROR'];
            }
        }
        return self::$AUTH_RESPONSES['ERROR'];
    }

    public static function ChangePassword($data) {
        $userData = $data['AuthUser'];
        if(!$userData['password'] || !$userData['newpassword'] || !$userData['confirmpassword']) {
            return self::$AUTH_RESPONSES['EMPTY_FIELD'];
        } else if (!self::CheckPassword($userData['password'])){
            return self::$AUTH_RESPONSES['INCORRECT_PASSWORD'];
        } else if (strlen($userData['newpassword']) < 6 || strlen($userData['newpassword']) > 15) {
            return self::$AUTH_RESPONSES['PASSWORD_LENGTH'];
        } else if ($userData['newpassword'] != $userData['confirmpassword']) {
            return self::$AUTH_RESPONSES['PASSWORD_LENGTH'];
        } else {
            $user = self::GetUserModel();
            $user->password = Yii::$app->getSecurity()->generatePasswordHash($userData['newpassword']);
            if($user->save()) {
                return true;
            }
        }
        return self::$AUTH_RESPONSES['ERROR']; 
    }

    public static function FollowUser($data) {
        $userid = $data['userid'];
        $fullname = $data['fullname'];
        $currentUserId = Yii::$app->user->id;

        $following = new AuthFollowing([
            'auth_user_id' => $userid,
            'follower_id' => $currentUserId
        ]);

        if($following->save()) {
            SiteService::WriteLog($currentUserId, SiteService::$ACTIVITIES['FOLLOW_USER'], $userid, Yii::$app->user->identityClass, $fullname);
            return true;
        }

        return false;
    }

    public static function UnfollowUser($data) {
        $userid = $data['userid'];
        $fullname = $data['fullname'];
        $currentUserId = Yii::$app->user->id;

        $following = AuthFollowing::find()->where(['and', ['auth_user_id' => $userid], ['follower_id' => $currentUserId]])->one();
        if($following) {
            $following->delete();
            SiteService::WriteLog($currentUserId, SiteService::$ACTIVITIES['UNFOLLOW_USER'], $userid, Yii::$app->user->identityClass, $fullname);
            return true;
        }

        return false;
    }

    public static function GetFollowing() {
        $currentUserId = Yii::$app->user->id;
        $following = (new Query())
                        ->select(['auth.id', 'auth.fullname', 'auth.slug', 'auth.avatar'])
                        ->from('auth_user as auth')
                        ->leftJoin('auth_following as f', 'f.auth_user_id = auth.id')
                        ->where(['follower_id' => $currentUserId])
                        ->all();
        return $following;
    }

    public static function GetFollower() {
        $currentUserId = Yii::$app->user->id;
        $follower = (new Query())
                        ->select(['auth.id', 'auth.fullname', 'auth.slug', 'auth.avatar'])
                        ->from('auth_user as auth')
                        ->leftJoin('auth_following as f', 'f.follower_id = auth.id')
                        ->where(['auth_user_id' => $currentUserId])
                        ->all();
        return $follower;
    }
}