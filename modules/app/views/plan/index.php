<?php

use app\modules\app\APPConfig;
use app\modules\app\PathConfig;
use app\modules\contrib\gxassets\GxVueSelectAsset;

GxVueSelectAsset::register($this);

$pageData = [
    'pageTitle' => 'Cùng nhau chia sẻ lịch trình du lịch',
    'pageBreadcrumb' => 'Lịch trình',
    'backgoundHeader' => Yii::$app->homeUrl . 'resources/images/plan-header.jpg'
];
?>
<?= $this->render(PathConfig::getAppViewPath('pageListHeader'), $pageData); ?>

<div class="container mt-3" id="plan-page">
    <div class="row">
        <div class="col-md-4 sidebar-wrap">
            <div class="sidebar-header">
                <h1 class="card-title font-weight-bold">Tìm kiếm</h1>
            </div>
            <div class="card sidebar-content">
                <div class="card-body">
                    <div class="form-search" v-cloak>
                        <div class="form-group">
                            <label for="" class="font-weight-bold">Điểm đến</label> 
                            <v-select :options="destinationCategories" :reduce="selected => selected.code" v-model="destination"></v-select>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label for="" class="font-weight-bold">Đánh giá</label>
                            <select class="form-control" v-model="rating" @change="getPlans()">
                                <option value="5">5 sao</option>
                                <option value="4">Trên 4 sao</option>
                                <option value="3">Trên 3 sao</option>
                                <option value="0">Tất cả</option>
                            </select>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label for="" class="font-weight-bold">Bình luận</label>
                            <select class="form-control" v-model="comment" @change="getPlans()">
                                <option value="1">Giảm dần</option>
                                <option value="0">Tăng dần</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body d-flex justify-content-center">
                    <a href="<?= APPConfig::getUrl('plan/create') ?>" class="btn btn-outline bg-pink-400 border-pink-400 text-pink-400 rounded-round">
                        Lên lịch trình <i class="icon-plus2 ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-8 data-wrap">
            <div class="data-header">
                <h1 class="card-title font-weight-bold">Danh sách lịch trình</h1>
            </div>
            <div class="card data-content pb-3">
                <div class="loading-data d-flex justify-content-center p-3" style="height: 100vh" v-if="loading">
                    <div class="loading-content"><i class="icon-spinner2 spinner icon-2x"></i></div>
                </div>
                <div class="loaded-data" v-else>
                    <div class="empty-data d-flex justify-content-center align-items-center flex-column p-3" v-if="plans.length == 0">
                        <h4 class="font-weight-bold mb-5 text-center">Chưa có lịch trình nào được tạo</h4>
                        <a href="<?= APPConfig::getUrl('plan/create') ?>" class="btn btn-outline border-pink-400 text-pink-400 bg-pink-400 rounded-round">
                            Thêm lịch trình của bạn<i class="icon-plus2 ml-2"></i>
                        </a>
                    </div>
                    <div class="available-data" v-else>
                        <div class="data-summary py-2 px-3">
                            <h5 class="mb-0"><b>{{ pagination.from }}</b> - <b>{{ pagination.to }}</b> trong <b>{{ pagination.total }}</b> kết quả</h5>
                        </div>
                        <div class="media flex-column flex-sm-row mt-0 mb-3" v-cloak>
                            <ul class="media-list media-list-linked media-list-bordered w-100">
                                <li v-for="item in plans">
                                    <div class="media">
                                        <div class="mr-2">
                                            <a :href="'<?= APPConfig::getUrl('plan/detail/') ?>' + item.slug" class="media-list-photo">
                                                <img :src="'<?= Yii::$app->homeUrl . 'uploads/' ?>' + item.thumbnail" height="150" width="225" :alt="item.name">
                                            </a>
                                        </div>
                                        <div class="media-body">
                                            <h5 class="media-title font-weight-bold"><a :href="'<?= APPConfig::getUrl('plan/detail/') ?>' + item.slug">{{ item.name }}</a></h5>
                                            {{ item.subtitle }}
                                            <p class="text-muted"><i class="icon-comment mr-1"></i> {{ item.count_comment ? item.count_comment : 0 }}</p>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="pagination-wrap" v-if="pagination.pages > 1">
                            <ul class="pagination-separated justify-content-center twbs-separated pagination">
                                <li class="page-item" v-for="(p, idx) in pagination.links" :class="p == 'current' ? 'active' : ''" @click="page = p">
                                    <a href="#" class="page-link">{{ idx == 0 ? 'Trang đầu' : (idx == pagination.links.length - 1 ? 'Trang cuối' : (p == 'current' ? pagination.current : p)) }}</a>
                                </li>
                            </ul>
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
            el: '#plan-page',
            data: {
                plans: [],
                pagination: {},
                loading: true,
                page: 1,
                perpage: 5,
                comment: 1,
                rating: 0,
                destination: 13,
                destinationCategories: []
            },
            created: function() {
                this.getPlans()
                this.getDestinations()
            },
            watch: {
                page: function() {
                    this.getPlans()
                },

                destination: function() {
                    this.getPlans()
                }
            },
            methods: {
                getPlans: function() {
                    var _this = this
                    var api = '<?= APPConfig::getUrl('plan/get-list') ?>' +
                        '?page=' + this.page + '&perpage=' + this.perpage + '&destination=' + this.destination + '&comment=' + this.comment + '&rating=' + this.rating

                    sendAjax(api, {}, 'GET', (resp) => {
                        if (resp.status) {
                            _this.plans = resp.plans
                            _this.pagination = resp.pagination
                            _this.loading = false
                        } else {
                            toastMessage('error', 'Lỗi!')
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