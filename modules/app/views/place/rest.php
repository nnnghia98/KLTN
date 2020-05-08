<?php

use app\modules\app\APPConfig;
use app\modules\app\PathConfig;
use app\modules\cms\services\PlaceService;
use app\modules\contrib\gxassets\GxVueSelectAsset;

GxVueSelectAsset::register($this);
$pageData = [
    'pageTitle' => 'Địa điểm nghỉ ngơi',
    'pageBreadcrumb' => [['Nghỉ ngơi']],
    'backgoundHeader' => Yii::$app->homeUrl . 'resources/images/destination-header.jpg'
];
?>
<?= $this->render(PathConfig::getAppViewPath('pageHeader'), $pageData); ?>

<div class="container mt-3" id="rest-page">
    <div class="row">
        <div class="col-md-4 sidebar-wrap">
            <div class="sidebar-header">
                <h1 class="card-title font-weight-bold">Tìm kiếm</h1>
            </div>

            <div class="card sidebar-content">
                <div class="card-body">
                    <div class="form-search" v-cloak>
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control border-right-0" placeholder="Tên địa điểm nghỉ ngơi" v-model="keyword">
                                <span class="input-group-append" @click="getRests()">
                                    <span class="input-group-text bg-pink-400 border-pink-400 text-white">
                                        <i class="icon-search4"></i>
                                    </span>
                                </span>
                            </div>
                        </div>    
                        <hr>    
                        <div class="form-group">
                            <label for="" class="font-weight-bold">Điểm đến</label> 
                            <v-select :options="destinationCategories" :reduce="selected => selected.code" v-model="destination"></v-select>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label for="" class="font-weight-bold">Sắp xếp</label>
                            <select class="form-control" v-model="order" @change="getRests()">
                                <option v-for="(value, key) in orderMap" :value="key">{{ value }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body d-flex justify-content-center">
                    <a href="<?= APPConfig::getUrl('place/rest-map') ?>" class="btn bg-pink-400 rounded-round">
                        Xem trên bản đồ <i class="icon-map4 ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-8 data-wrap">
            <div class="data-header">
                <h1 class="card-title font-weight-bold">Danh sách khách sạn</h1>
            </div>
            <div class="card data-content pb-3">
                <div class="loading-data d-flex justify-content-center p-3" style="height: 100vh" v-if="loading">
                    <div class="loading-content"><i class="icon-spinner2 spinner icon-2x"></i></div>
                </div>
                <div class="loaded-data" v-else>
                    <div class="empty-data d-flex justify-content-center p-3" v-if="rests.length == 0">
                        <h4 class="font-weight-bold mb-0">Không có dữ liệu phù hợp</h4>
                    </div>
                    <div class="available-data" v-else>
                        <div class="data-summary py-2 px-3">
                            <pagination-summary :current="pagination.current" :from="pagination.from" :to="pagination.to" :total="pagination.total"></pagination-summary>
                        </div>
                        <div class="media flex-column flex-sm-row mt-0" v-cloak>
                            <ul class="media-list media-list-linked media-list-bordered w-100">
                                <place v-for="item in rests" :place="item"></place>
                            </ul>
                        </div>
                        <div class="data-summary py-2 px-3 mb-3">
                            <pagination-summary :current="pagination.current" :from="pagination.from" :to="pagination.to" :total="pagination.total"></pagination-summary>
                        </div>
                        <div class="pagination-wrap" v-if="pagination.pages > 1">
                            <pagination :current="pagination.current" :pages="pagination.pages" @change="page = $event"></pagination>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        Vue.component('v-select', VueSelect.VueSelect);
        var vm = new Vue({
            el: '#rest-page',
            data: {
                rests: [],
                pagination: {},
                loading: true,
                page: 1,
                perpage: 5,
                order: 'rating-desc',
                orderMap: {
                    'rating-desc': 'Đánh giá giảm dần',
                    'rating-asc': 'Đánh giá tăng dần',
                },
                keyword: '',
                destination: 13,
                destinationCategories: []
            },
            created: function() {
                this.getRests()
                this.getDestinations()
            },
            watch: {
                page: function() {
                    this.getRests()
                },

                destination: function() {
                    this.getRests()
                }
            },
            methods: {
                getRests: function() {
                    var _this = this
                    var api = '<?= APPConfig::getUrl('place/get-place-list') ?>' +
                        `?page=${this.page}&perpage=${this.perpage}&destination=${this.destination}&keyword=${this.keyword}&order=${this.order}&type=` + '<?= PlaceService::$TYPE['REST'] ?>'

                    sendAjax(api, {}, 'GET', (resp) => {
                        if (resp.status) {
                            _this.rests = resp.places
                            _this.pagination = resp.pagination
                            _this.loading = false
                        } else {
                            toastMessage('error', resp.message)
                        }
                    })
                },

                getDestinations: function() {
                    var _this = this
                    var api = '<?= APPConfig::getUrl('destination/get-categories') ?>'

                    sendAjax(api, {}, 'GET', (resp) => {
                        if (resp.status) {
                            _this.destinationCategories = resp.categories
                        } else {
                            toastMessage('error', 'Lỗi!')
                        }
                    })
                }
            }
        })
    })
</script>