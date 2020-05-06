<?php

namespace app\modules\cms\services;

use app\modules\cms\models\Plan;
use app\modules\cms\models\PlanDetail;
use app\modules\cms\services\InteractiveService;
use DateTime;
use Yii;
use yii\db\Query;

class PlanService
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
        'ERROR_LIST' => 'Không thể lấy danh sách lịch trình',
        'CREATE_ERROR' => 'Không thể tạo lịch trình',
        'EDIT_ERROR' => 'Không thể lưu lịch trình chi tiết'
    ];

    public static $HERE_API_ID = 'gRqLa6YYLXTqvoTTUhiT';
    public static $HERE_API_KEY = 'hPtC4kp3SDaqlFsNbcT_zPpyknvCfWEdcxejzcUk8zI';
    public static $OBJECT_TYPE = 'app\modules\cms\models\Plan';

    public static function GetNewestPlans($limit) {
        $interactive = InteractiveService::GetQueryInteractive(self::$OBJECT_TYPE);
        $plans = (new Query())
                        ->select(['p.*', 'i.*'])
                        ->from(['p' => 'plan'])
                        ->leftJoin(['i' => $interactive], 'p.id = i.object_id')
                        ->where(['and', ['status' => self::$STATUS['ACTIVE']], ['delete' => self::$DELETE['ALIVE']]])
                        ->orderBy('p.created_at DESC')
                        ->limit($limit)
                        ->all();
        return $plans;
    }

    public static function GetListAppPage($page, $perpage, $destination, $comment, $rating) {
        $interactive = InteractiveService::GetQueryInteractive(self::$OBJECT_TYPE);
        $query = (new Query())
                        ->select(['p.*', 'i.*'])
                        ->from(['p' => 'plan'])
                        ->leftJoin(['i' => $interactive], 'p.id = i.object_id')
                        ->where(['and', ['status' => self::$STATUS['ACTIVE']], ['delete' => self::$DELETE['ALIVE']]]);
                        
        if($destination) {
            $query->andWhere(['destination_id' => $destination]);
        }

        if($rating) {
            $query->andWhere(['>=', 'i.avg_rating', $rating]);
        }

        $total = $query->select('COUNT(*)')->column();
        
        list($limit, $offset) = SiteService::GetLimitAndOffset($page, $perpage);
                        
        $plans = $query->select(['p.*', 'i.*'])
                    ->orderBy(['i.count_comment' => $comment ? SORT_DESC : SORT_ASC])
                    ->limit($limit)
                    ->offset($offset)
                    ->all();
        $pagination = SiteService::CreatePaginationMetadata($total[0], $page, $perpage, count($plans));
        return [$plans, $pagination];
    }

    public static function GetPlanBySlug($slug) {
        $plan = Plan::find()
                    ->where(['and', ['status' => self::$STATUS['ACTIVE']], ['delete' => self::$DELETE['ALIVE']]])
                    ->andWhere(['slug' => $slug])
                    ->one();
        return $plan;
    }

    public static function Create($data)
    {
        $model = new Plan();
        $model->load($data);

        $desitnation = DestinationService::GetDestinationById($model->destination_id);
        $totalDay = self::CalculateTotalDayFromStartAndEnd($model->date_start, $model->date_end);
        $model->slug = SiteService::uniqid();
        $model->status = self::$STATUS['ACTIVE'];
        $model->delete = self::$DELETE['ALIVE'];
        $model->name = self::GeneratePlanName($desitnation->name, $totalDay);
        $model->total_day = $totalDay;
        $model->created_by = Yii::$app->user->id;
        $model->thumbnail = $desitnation->thumbnail;
        $model->viewed = 0;
        $model->routes = '[]';
        $model->detail = '[]';
        
        if($model->save()) {
            return $model->slug;
        }
        return false;
    }

    public static function CalculateTotalDayFromStartAndEnd($date_start, $date_end)
    {
        $diff = abs(strtotime($date_end) - strtotime($date_start));
        $totalDay = $diff / (60 * 60 * 24) + 1;
        return $totalDay;
    }

    public static function GeneratePlanName($destName, $totalDay) {
        $planName =  'Lịch trình ' . $totalDay . ' ngày tại ' . $destName;
        return $planName;
    }

    public static function SaveDetail($data) {
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $planid = $data['planid'];
            $detail = json_decode($data['detail'], true);
            $routes = $data['routes'];

            $plan = $planid ? Plan::findOne($planid) : self::DuplicatePlan($planid);
            self::DeleteAllPlanDetail($planid);

            $planid = $plan->id;
            $flag = true;
            foreach($detail as &$date) {
                foreach($date['places'] as $place) {
                    $plan_detail = new PlanDetail();
                    $plan_detail->plan_id = $planid;
                    $plan_detail->place_id = (int)$place['id'];
                    $plan_detail->place_name = $place['name'];
                    $plan_detail->time_start = (int)$place['time_start'];
                    $plan_detail->time_stay = (int)$place['time_stay'];
                    $plan_detail->time_free = (int)$place['time_free'];
                    $plan_detail->time_move = (int)$place['time_move'];
                    $plan_detail->distance = (float)$place['distance'];
                    $plan_detail->move_type = (int)$place['move_type'];
                    $plan_detail->note = $place['note'];
                    $plan_detail->date_index = (int)$place['didx'];
                    $plan_detail->thumbnail = $place['thumbnail'];
                    $plan_detail->slug = $place['slug'];
                    $plan_detail->lat = $place['lat'];
                    $plan_detail->lng = $place['lng'];

                    if(!($flag = $plan_detail->save(false))) {
                        $transaction->rollBack();
                        break;
                    }
                }
            }

            if($flag) {
                $plan->detail = json_encode($detail, true);
                $plan->routes = $routes;
                if($plan->save()) {
                    $transaction->commit();
                    return true;
                }
            }
            
        } catch(\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch(\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        return false;
    }

    public static function DuplicatePlan($id) {
        $oldPlan = Plan::findOne($id);
        $newPlan = new Plan();

        $newPlan->setAttributes($oldPlan->attributes);
        $newPlan->created_by = Yii::$app->user->id;
        $newPlan->slug = SiteService::uniqid();
        $newPlan->save();

        return $newPlan;
    }

    public static function DeleteAllplanDetail($planid) {
        PlanDetail::deleteAll(['plan_id' => $planid]);
    }
}