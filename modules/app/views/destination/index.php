<?php

use app\modules\app\APPConfig;
use app\modules\app\PathConfig;

$pageData = [
    'pageTitle' => 'Hàng trăm điểm đến trên toàn quốc',
    'pageBreadcrumb' => [['Điểm đến']],
    'backgoundHeader' => Yii::$app->homeUrl . 'resources/images/destination-header.jpg'
];
?>
<?= $this->render(PathConfig::getAppViewPath('pageHeader'), $pageData); ?>

<div class="container mt-3" id="destination-page">
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
                                <input type="text" class="form-control border-right-0" placeholder="Tên điểm đến" v-model="keyword">
                                <span class="input-group-append" @click="getDestinations()">
                                    <span class="input-group-text bg-pink-400 border-pink-400 text-white">
                                        <i class="icon-search4"></i>
                                    </span>
                                </span>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label for="" class="font-weight-bold">Sắp xếp</label>
                            <select class="form-control" v-model="order" @change="getDestinations()">
                                <option v-for="(value, key) in orderMap" :value="key">{{ value }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body d-flex justify-content-center">
                    <a href="<?= APPConfig::getUrl('destination/map') ?>" class="btn bg-pink-400 rounded-round">
                        Xem trên bản đồ <i class="icon-map4 ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-8 data-wrap">
            <div class="data-header">
                <h1 class="card-title font-weight-bold">Danh sách điểm đến</h1>
            </div>
            <div class="card data-content pb-3">
                <div class="loading-data d-flex justify-content-center p-3" style="height: 100vh" v-if="loading">
                    <div class="loading-content"><i class="icon-spinner2 spinner icon-2x"></i></div>
                </div>
                <div class="loaded-data" v-else>
                    <div class="empty-data d-flex justify-content-center p-3" v-if="destinations.length == 0">
                        <h4 class="font-weight-bold mb-0">Không có dữ liệu phù hợp</h4>
                    </div>
                    <div class="available-data" v-else>
                        <div class="data-summary py-2 px-3">
                            <pagination-summary :current="pagination.current" :from="pagination.from" :to="pagination.to" :total="pagination.total"></pagination-summary>
                        </div>
                        <div class="media flex-column flex-sm-row mt-0" v-cloak>
                            <ul class="media-list media-list-linked media-list-bordered w-100">
                                <li v-for="item in destinations">
                                    <div class="media">
                                        <div class="mr-2">
                                            <a :href="'<?= APPConfig::getUrl('destination/detail/') ?>' + item.slug" class="media-list-photo">
                                                <img :src="'<?= Yii::$app->homeUrl . 'uploads/' ?>' + item.thumbnail" height="150" width="225" :alt="item.name">
                                            </a>
                                        </div>
                                        <div class="media-body">
                                            <h4 class="media-title font-weight-bold">
                                                <a :href="'<?= APPConfig::getUrl('destination/detail/') ?>' + item.slug">{{ item.name }}</a>
                                            </h4>
                                            <h6 class="mb-0 text-muted">{{ item.subtitle }}</h6>
                                            <rating-star-static :rating="item.avg_rating" :key="item.slug"></rating-star-static>
                                            <p class="text-muted"><i class="icon-comment mr-1"></i> {{ item.count_comment ? item.count_comment : 0 }}</p>
                                        </div>
                                    </div>
                                </li>
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
        var vm = new Vue({
            el: '#destination-page',
            data: {
                destinations: null,
                pagination: null,
                loading: true,
                page: 1,
                perpage: 5,
                order: 'rating-desc',
                orderMap: {
                    'rating-desc': 'Đánh giá giảm dần',
                    'rating-asc': 'Đánh giá tăng dần',
                },
                keyword: ''
            },
            created: function() {
                this.getDestinations()
            },
            watch: {
                page: function() {
                    this.getDestinations()
                }
            },
            methods: {
                getDestinations: function() {
                    var _this = this
                    var api = '<?= APPConfig::getUrl('destination/get-list') ?>' +
                        '?page=' + this.page + '&perpage=' + this.perpage + '&keyword=' + this.keyword + '&order=' + this.order

                    sendAjax(api, {}, 'GET', (resp) => {
                        if (resp.status) {
                            _this.destinations = resp.destinations
                            _this.pagination = resp.pagination
                            _this.loading = false
                        } else {
                            toastMessage('error', resp.message)
                        }
                    })
                },


            }
        })
    })
</script>