<?php

use app\modules\app\APPConfig;
use app\modules\app\PathConfig;
use app\modules\cms\services\PlaceService;
use app\modules\cms\widgets\CMSMapDetailWidget;
use app\modules\contrib\gxassets\GxLeafletAsset;
use app\modules\contrib\gxassets\GxVueperSlidesAsset;

GxLeafletAsset::register($this);
GxVueperSlidesAsset::register($this);

$pageData = [
    'pageTitle' => $user['fullname'],
    'pageBreadcrumb' => [['Lịch trình của ' . $user['fullname']]],
    'backgoundHeader' => Yii::$app->homeUrl . 'resources/images/destination-header.jpg'
];
?>
<?= $this->render(PathConfig::getAppViewPath('pageHeader'), $pageData); ?>

<div class="content" id="my-plan-page" v-cloak>
    <div class="">
        <div class="user-page-header d-flex justify-content-center align-items-center flex-column card card-body pb-0 mb-0">
            <div class="user-swapper py-4 d-flex flex-column align-items-center">
                <div class="user-avatar rounded-circle overflow-hidden position-relative border-2 border-primary">
                    <img :src="getAvatarPath(avatar)" :alt="user.fullname" width="150" height="150" style="object-fit: cover">
                </div>
                <div class="user-name text-center mt-3 mb-0">
                    <h3>{{ user.fullname }}</h3>
                </div>
            </div>
        </div>
        <div class="user-page-body my-5">
            <div class="container">
                <div class="loading-data d-flex justify-content-center p-3" style="height: 50vh" v-if="!plans">
                    <div class="loading-content"><i class="icon-spinner2 spinner icon-2x"></i></div>
                </div>
                <div class="loaded-data" v-else>
                    <div class="empty-data d-flex justify-content-center p-3" v-if="plans.length == 0">
                        <h4 class="font-weight-bold mb-0">Không có lịch trình</h4>
                    </div>
                    <div class="available-data" v-else>
                        <div class="row">
                            <plan-in-row v-for="plan in plans" :plan="plan" :col="3" :key="plan.id"></plan-in-row>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <delete-modal 
        :deletewarning="'Bạn có chắc chắn xóa lịch trình này?'" 
        @delete="deletePlan"></delete-modal>
</div>

<script>
    $(function() {
        var user = JSON.parse('<?= json_encode($user, true) ?>')
        var vm = new Vue({
            el: '#my-plan-page',
            data: {
                user: user,
                avatar: user.avatar,
                plans: null,
                planSelected: null
            },
            created: function() {
                this.getUserPlans()
            },
            methods: {
                getUserPlans: function() {
                    var _this = this
                    var api = '<?= APPConfig::getUrl('user/get-user-plans') ?>' + `?id=${this.user.id}`

                    sendAjax(api, {}, 'GET', (resp) => {
                        if (resp.status) {
                            _this.plans = resp.plans
                            _this.fixImageActionsHeight()
                        }
                    })
                },

                fixImageActionsHeight: function() {
                    this.$nextTick(function() {
                        fixImageActionsHeight()
                    })
                },

                getAvatarPath: function(avatar) {
                    var path = '<?= Yii::$app->homeUrl ?>' + (avatar ? 'uploads/' + avatar : 'resources/images/no_avatar.jpg')
                    return path
                },

                confirmDelete: function(slug) {
                    this.planSelected = slug
                    $('#delete-modal').modal()
                },

                deletePlan: function() {
                    var api = '<?= APPConfig::getUrl('plan/delete') ?>',
                        data = {slug: this.planSelected}
                    sendAjax(api, data, 'POST', function(resp) {
                        if(resp.status) {
                            window.location.reload()
                        } else {
                            toastMessage('error', resp.message)
                        }
                    })
                },
            }
        })
    })
</script>