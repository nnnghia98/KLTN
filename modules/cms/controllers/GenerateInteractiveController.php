<?php 

namespace app\modules\cms\controllers;

use app\modules\cms\CMSConfig;
use app\modules\cms\models\AuthRole;
use app\modules\cms\models\AuthUser;
use app\modules\cms\models\Destination;
use app\modules\cms\models\Interactive;
use app\modules\cms\models\Place;
use app\modules\cms\services\AuthService;
use app\modules\cms\services\DestinationService;
use app\modules\cms\services\SiteService;
use Yii;
use yii\db\Query;
use yii\web\Controller;

class GenerateInteractiveController extends Controller
{
    public $enableCsrfValidation = false;
    public $comments = [
        1 => [
            'Quá tệ',
            'Giá quá đắt',
            'Sẽ không bao giờ quay lại',
            'Chán'
        ],
        2=> [
            'Không tốt',
            'Dịch vụ chưa tốt',
            'Hơi thất vọng',
            'Đã ghé',
        ],
        3 => [
            'Dịch vụ chưa tốt',
            'Hơi thất vọng',
            'Đã ghé',
            'Không có gì đặc biệt'
        ], 
        4 => [
            'Sống ảo được',
            'Good',
            'Sẽ ghé lại',
            'Giá hơi cao'
        ],
        5 => [
            'Tuyệt vời',
            'Good',
            '5 sao',
            'Rất hài lòng'
        ]
    ];


    public function actionPlace() {
        $places = Place::find()->select('id')->column();
        $users = AuthUser::find()->select('id')->where(['>', 'id', 3])->column();
        $object_type = 'app\modules\cms\models\Place';
        foreach($places as $object_id) {
            foreach($users as $user_id) {
                $do = rand(0, 1);
                if($do) {
                    $star = rand(1, 5);
                    $comment = rand(0, 3);
                    $like = rand(0, 1);
                    $interactive = Interactive::find()
                                    ->where(['created_by' => $user_id])
                                    ->andWhere(['and', ['object_id' => $object_id], ['object_type' => $object_type]])
                                    ->one();
                    if(!$interactive) {
                        $interactive = new Interactive([
                            'object_type' => $object_type,
                            'object_id' => $object_id,
                            'is_like' => $like,
                            'rating' => $star,
                            'comment' => $this->comments[$star][$comment],
                            'created_by' => $user_id
                        ]);
    
                        $interactive->save(); 
                        // dd($interactive);
                    }
                }
            }
        }
    }

    public function actionDestination() {
        $destinations = Destination::find()->select('id')->column();
        $users = AuthUser::find()->select('id')->where(['>', 'id', 3])->column();
        $object_type = DestinationService::$OBJECT_TYPE;
        foreach($destinations as $object_id) {
            foreach($users as $user_id) {
                $do = rand(0, 1);
                if($do) {
                    $star = rand(1, 5);
                    $comment = rand(0, 3);
                    $like = rand(0, 1);
                    $interactive = Interactive::find()
                                    ->where(['created_by' => $user_id])
                                    ->andWhere(['and', ['object_id' => $object_id], ['object_type' => $object_type]])
                                    ->one();
                    if(!$interactive) {
                        $interactive = new Interactive([
                            'object_type' => $object_type,
                            'object_id' => $object_id,
                            'is_like' => $like,
                            'rating' => $star,
                            'comment' => $this->comments[$star][$comment],
                            'created_by' => $user_id
                        ]);
    
                        $interactive->save(); 
                        // dd($interactive);
                    }
                }
            }
        }
    }
}