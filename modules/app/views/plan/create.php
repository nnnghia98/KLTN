<?php

use app\modules\app\APPConfig;
use app\modules\app\PathConfig;
use kartik\form\ActiveForm;
use app\modules\contrib\gxassets\GxVueSelectAsset;
use app\modules\contrib\gxassets\GxVuetifyAsset;

GxVueSelectAsset::register($this);
GxVuetifyAsset::register($this);
$pageData = [
    'pageTitle' => 'Tạo một lịch trình du lịch chi tiết nào',
    'pageBreadcrumb' => [
        ['Lịch trình', APPConfig::getUrl('plan')],
        ['Tạo lịch trình']
    ],
    'backgoundHeader' => Yii::$app->homeUrl . 'resources/images/plan-header.jpg'
]; ?>
<?= $this->render(PathConfig::getAppViewPath('pageHeader'), $pageData); ?>

<style>
    .v-application--wrap {
        min-height: unset;
    }
</style>
<div class="container mt-3" id="create-plan-page">
    <v-app>
        <div class="create-plan-content d-flex flex-column flex-md-row justify-content-center align-items-center my-5">
            <div class="w-100 w-md-50">
                <div class="create-plan-image">
                    <img src="<?= Yii::$app->homeUrl . 'resources/images/create-plan.png' ?>" style="max-width: 100%">
                </div>
            </div>
            <div class="w-100 w-md-50 mt-5 mt-md-0 d-flex justify-content-center">
                <div class="create-plan-from w-100 w-md-75">
                    <?php $form = ActiveForm::begin([
                        'id' => 'create-plan-from'
                    ]) ?>
                    <div class="form-group">
                        <label for="" class="font-weight-bold">Điểm đến</label>
                        <v-select :options="destinationCategories" :reduce="selected => selected.code" v-model="destination"></v-select>
                        <input type="hidden" name="Plan[destination_id]" v-model="destination">
                    </div>
                    <div class="form-group">
                        <label for="" class="font-weight-bold">Ngày bắt đầu</label>
                        <v-menu ref="menu" v-model="menu" :close-on-content-click="false" transition="scale-transition" offset-y max-width="290px">
                            <template v-slot:activator="{ on }">
                                <input type="text" name="Plan[date_start]" class="form-control input-custom" v-model="dateStartFormated" v-on="on">
                            </template>
                            <v-date-picker v-model="dateStart" no-title @input="menu = false"></v-date-picker>
                        </v-menu>
                    </div>
                    <div class="form-group">
                        <label for="" class="font-weight-bold">Ngày kết thúc</label>
                        <v-menu ref="menu2" v-model="menu2" :close-on-content-click="false" transition="scale-transition" offset-y max-width="290px">
                            <template v-slot:activator="{ on }">
                                <input type="text" name="Plan[date_end]" class="form-control input-custom" v-model="dateEndFormated" v-on="on">
                            </template>
                            <v-date-picker v-model="dateEnd" :min="dateStart" no-title @input="menu2 = false"></v-date-picker>
                        </v-menu>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn bg-pink-400 rounded-round btn-block">Bắt đầu <i class="icon-paperplane ml2"></i></button>
                    </div>
                    <?php ActiveForm::end() ?>
                </div>
            </div>
        </div>
    </v-app>
</div>

<script>
    $(function() {
        Vue.component('v-select', VueSelect.VueSelect);
        var vm = new Vue({
            el: '#create-plan-page',
            data: {
                destination: null,
                destinationCategories: [],
                menu: false,
                menu2: false,
                dateStart: new Date().toISOString().substr(0, 10),
                dateEnd: new Date().toISOString().substr(0, 10),
            },
            vuetify: new Vuetify(),
            computed: {
                dateStartFormated: function() {
                    return formatDate(this.dateStart)
                },
                dateEndFormated: function() {
                    return formatDate(this.dateEnd)
                }
            },
            created: function() {
                this.getDestinations()
            },
            methods: {
                getDestinations: function() {
                    var _this = this
                    var api = '<?= APPConfig::getUrl('destination/get-categories') ?>'

                    sendAjax(api, {}, 'GET', (resp) => {
                        if (resp.status) {
                            _this.destinationCategories = resp.categories
                            _this.destination = 13
                        } else {
                            toastMessage('error', 'Lỗi!')
                        }
                    })
                }
            }
        })
    })
</script>