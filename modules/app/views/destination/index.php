<?php

use app\modules\app\APPConfig;
use app\modules\app\PathConfig;

$pageData = [
    'pageTitle' => 'Hàng trăm điểm đến trên toàn quốc',
    'pageBreadcrumb' => 'Điểm đến',
    'backgoundHeader' => Yii::$app->homeUrl . 'resources/images/destination-header.jpg'
];
?>
<?= $this->render(PathConfig::getAppViewPath('pageListHeader'), $pageData); ?>

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
                                    <span class="input-group-text bg-secondary border-secondary text-white">
                                        <i class="icon-search4"></i>
                                    </span>
                                </span>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label for="" class="font-weight-bold">Đánh giá</label>
                            <select class="form-control" v-model="rating" @change="getDestinations()">
                                <option value="5">5 sao</option>
                                <option value="4">Trên 4 sao</option>
                                <option value="3">Trên 3 sao</option>
                                <option value="0">Tất cả</option>
                            </select>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label for="" class="font-weight-bold">Bình luận</label>
                            <select class="form-control" v-model="comment" @change="getDestinations()">
                                <option value="1">Giảm dần</option>
                                <option value="0">Tăng dần</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <a href="<?= APPConfig::getUrl('destination/map') ?>" class="btn btn-primary btn-labeled btn-labeled-left d-block">
                        <b><i class="icon-map4"></i></b> Xem trên bản đồ
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
                            <h5 class="mb-0"><b>{{ pagination.from }}</b> - <b>{{ pagination.to }}</b> trong <b>{{ pagination.total }}</b> kết quả</h5>
                        </div>
                        <div class="media flex-column flex-sm-row mt-0 mb-3" v-cloak>
                            <ul class="media-list media-list-linked media-list-bordered w-100">
                                <li v-for="item in destinations">
                                    <div class="media">
                                        <div class="mr-2">
                                            <a :href="'<?= APPConfig::getUrl('destination/') ?>' + item.slug" class="media-list-photo">
                                                <img :src="'<?= Yii::$app->homeUrl . 'uploads/' ?>' + item.thumbnail" height="150" width="225" :alt="item.name">
                                            </a>
                                        </div>
                                        <div class="media-body">
                                            <h4 class="media-title font-weight-bold">
                                                <a :href="'<?= APPConfig::getUrl('destination/') ?>' + item.slug">{{ item.name }}</a>
                                            </h4>
                                            <h5 class="mb-0 text-muted">{{ item.subtitle }}</h5>
                                            <rating-star-static :rating="item.avg_rating"></rating-star-static>
                                            <p class="text-muted"><i class="icon-comment mr-1"></i> {{ item.count_comment ? item.count_comment : 0 }}</p>
                                        </div>
                                        <div class="ml-1">
                                            <a :href="'<?= APPConfig::getUrl('place/destination?target=') ?>' + item.slug" class="btn btn-sm btn-icon btn-outline-primary" title="Xem trên bản đồ"><i class="icon-location4"></i></a>
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
        var vm = new Vue({
            el: '#destination-page',
            data: {
                destinations: null,
                pagination: null,
                loading: true,
                page: 1,
                perpage: 5,
                comment: 1,
                rating: 0,
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
                        '?page=' + this.page + '&perpage=' + this.perpage + '&keyword=' + this.keyword + '&comment=' + this.comment + '&rating=' + this.rating

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