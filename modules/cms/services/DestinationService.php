<?php

namespace app\modules\cms\services;

use app\modules\cms\models\Destination;
use app\modules\cms\models\Interactive;
use app\modules\cms\services\InteractiveService;
use Yii;
use yii\db\Query;

class DestinationService
{
    public static $DELETE = [
        'ALIVE' => 1,
        'DELETED' => 0
    ];

    public static $STATUS = [
        'ACTIVE' => 1,
        'DEACTIVE' => 0
    ];

    public static $RESPONSE = [
        'ERROR_LIST' => 'Không thể lấy danh sách điểm đến'
    ];

    public static $OBJECT_TYPE = 'app\modules\cms\models\Destination';

    public static function GetTopDestinations($limit) {
        $interactive = InteractiveService::GetQueryInteractive(self::$OBJECT_TYPE);
        $destinations = (new Query())
                        ->select(['d.name', 'd.thumbnail', 'd.subtitle', 'd.slug', 'i.avg_rating', 'i.count_rating'])
                        ->from(['d' => 'destination'])
                        ->leftJoin(['i' => $interactive], 'd.id = i.object_id')
                        ->where(['and', ['status' => self::$STATUS['ACTIVE']], ['delete' => self::$DELETE['ALIVE']]])
                        ->orderBy('i.avg_rating DESC')
                        ->limit($limit)
                        ->all();
        return $destinations;
    }

    public static function GetListAppPage($page, $perpage, $keyword, $comment, $rating) {
        $interactive = InteractiveService::GetQueryInteractive(self::$OBJECT_TYPE);
        $query = (new Query())
                        ->select(['d.*', 'i.*'])
                        ->from(['d' => 'destination'])
                        ->leftJoin(['i' => $interactive], 'd.id = i.object_id')
                        ->where(['and', ['status' => self::$STATUS['ACTIVE']], ['delete' => self::$DELETE['ALIVE']]])
                        ->andWhere(['like', 'LOWER(name)', strtolower($keyword)]);
        if($rating) {
            $query->andWhere(['>=', 'i.avg_rating', $rating]);
        }

        $total = $query->select('COUNT(*)')->column();
        
        list($limit, $offset) = SiteService::GetLimitAndOffset($page, $perpage);
                        
        $destinations = $query->select(['d.*', 'i.*'])
                        ->orderBy(['i.count_comment' => $comment ? SORT_DESC : SORT_ASC])
                        ->limit($limit)
                        ->offset($offset)
                        ->all();
        $pagination = SiteService::CreatePaginationMetadata($total[0], $page, $perpage, count($destinations));
        return [$destinations, $pagination];
    }

    public static function GetCategories() {
        $destinations = Destination::find()
                        ->select(['id', 'name'])
                        ->where(['and', ['status' => self::$STATUS['ACTIVE']], ['delete' => self::$DELETE['ALIVE']]])
                        ->orderBy('name')
                        ->all();

        $categories = [];
        foreach($destinations as $d) {
            array_push($categories, [
                'label' => $d['name'],
                'code' => $d['id']
            ]);
        }
        return $categories;
    }

    public static function GetDestinationById($id) {
        $destination = Destination::find()
                        ->where(['and', ['status' => self::$STATUS['ACTIVE']], ['delete' => self::$DELETE['ALIVE']]])
                        ->andWhere(['id' => $id])
                        ->one();

        return $destination;
    }

    public static function GetDestinationBySlug($slug) {
        $interactive = InteractiveService::GetQueryInteractive(self::$OBJECT_TYPE);
        $destination = (new Query())
                        ->select(['d.*', 'i.*'])
                        ->from(['d' => 'destination'])
                        ->leftJoin(['i' => $interactive], 'd.id = i.object_id')
                        ->andWhere(['slug' => $slug])
                        ->one();

        return $destination;
    }

    public static function GetTopPlaces($id, $type) {
        $interactive = InteractiveService::GetQueryInteractive(PlaceService::$OBJECT_TYPE);
        $places = (new Query())
                        ->select(['p.name', 'p.thumbnail', 'p.address', 'p.slug', 'i.avg_rating', 'i.count_rating'])
                        ->from(['p' => 'place'])
                        ->leftJoin(['i' => $interactive], 'p.id = i.object_id')
                        ->where(['and', ['status' => self::$STATUS['ACTIVE']], ['delete' => self::$DELETE['ALIVE']]])
                        ->andWhere(['and', ['destination_id' => $id], ['place_type_id' => $type]])
                        ->orderBy('i.avg_rating DESC')
                        ->limit(3)
                        ->all();
        return $places;
    }

    public static function GetNewestPlans($id) {
        $plans = (new Query())
                        ->select(['p.name', 'p.thumbnail', 'p.slug', 'u.avatar as author_avatar', 'u.slug as author_slug', 'u.fullname as author'])
                        ->from(['p' => 'plan'])
                        ->leftJoin(['u' => 'auth_user'], 'p.created_by = u.id')
                        ->where(['and', ['p.status' => self::$STATUS['ACTIVE']], ['p.delete' => self::$DELETE['ALIVE']]])
                        ->andWhere(['destination_id' => $id])
                        ->orderBy('p.created_at DESC')
                        ->limit(3)
                        ->all();
        return $plans;
    }

    public static function GetComments($id, $page, $perpage) {
        list($limit, $offset) =  SiteService::GetLimitAndOffset($page, $perpage);
        $comments = (new Query())
                        ->select(['i.comment', 'i.rating', 'i.created_at', 'u.avatar as author_avatar', 'u.slug as author_slug', 'u.fullname as author'])
                        ->from('interactive as i')
                        ->leftJoin(['u' => 'auth_user'], 'i.created_by = u.id')
                        ->where(['and', ['i.object_type' => self::$OBJECT_TYPE], ['i.object_id' => $id]])
                        ->orderBy('i.created_at DESC')
                        ->limit($limit)
                        ->offset($offset)
                        ->all();
        return $comments;
    }

    public static function GetInteractiveOfCurrentUser($id) {
        if(!Yii::$app->user->isGuest) {
            $userid = Yii::$app->user->id;
            $interactive = (new Query())
                        ->select(['comment', 'rating'])
                        ->from('interactive')
                        ->where(['and', ['object_type' => self::$OBJECT_TYPE], ['object_id' => $id]])
                        ->andWhere(['created_by' => $userid])
                        ->one();
            if($interactive) {
                return [
                    'star' => $interactive['rating'],
                    'comment' => $interactive['comment']
                ];
            }
        }
        
        return [
            'star' => 0,
            'comment' => 0,
        ];
    }

    public static function SubmitComment($data) {
        if(!Yii::$app->user->isGuest) {
            $id = $data['id'];
            $star = intval($data['star']);
            $comment = $data['comment'];
            $userid = Yii::$app->user->id;

            if(!$comment) {
                return 'Nội dung bình luận không được để trống';
            }

            if($star > 5 || $star < 1) {
                return 'Giá trị đánh giá không khả dụng';
            }

            $interactive = Interactive::find()
                        ->where(['and', ['object_id' => $id], ['object_type' => self::$OBJECT_TYPE]])
                        ->andWhere(['created_by' => $userid])
                        ->one();

            if($interactive) {
                $interactive->comment = $comment;
                $interactive->rating = $star;
            } else {
                $interactive = new Interactive([
                    'object_type' => self::$OBJECT_TYPE,
                    'object_id' => $id,
                    'created_by' => $userid,
                    'rating' => $star,
                    'comment' => $comment,
                ]);
            }

            if($interactive->save()) {
                return true;
            } else {
                return 'Không thể lưu bình luận của bạn';
            }
        }

        return 'Bạn cần đăng nhập để thực hiện chức năng này';
    }
}