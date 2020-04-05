<?php

use app\modules\app\APPConfig;
use app\modules\app\PathConfig;

$pageData = [
    'pageTitle' => 'Địa điểm nghỉ ngơi',
    'pageBreadcrumb' => 'Nghỉ ngơi',
    'backgoundHeader' => Yii::$app->homeUrl . 'resources/images/destination-header.jpg'
];
?>
<?= $this->render(PathConfig::getAppViewPath('pageListHeader'), $pageData); ?>

<div class="content container mt-3" id="rest-page">
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
                                <input type="text" class="form-control border-right-0" placeholder="Tên khách sạn" v-model="keyword">
                                <span class="input-group-append" @click="getRests()">
                                    <span class="input-group-text bg-secondary border-secondary text-white">
                                        <i class="icon-search4"></i>
                                    </span>
                                </span>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label for="" class="font-weight-bold">Đánh giá</label>
                            <select class="form-control" v-model="rating" @change="getRests()">
                                <option value="5">5 sao</option>
                                <option value="4">Trên 4 sao</option>
                                <option value="3">Trên 3 sao</option>
                                <option value="0">Tất cả</option>
                            </select>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label for="" class="font-weight-bold">Bình luận</label>
                            <select class="form-control" v-model="comment" @change="getRests()">
                                <option value="1">Giảm dần</option>
                                <option value="0">Tăng dần</option>
                            </select>
                        </div>
                    </div>
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
                        <div class="media flex-column flex-sm-row mt-0 mb-3" v-cloak>
                            <ul class="media-list media-list-linked media-list-bordered w-100">
                                <li v-for="item in rests">
                                    <div class="media">
                                        <div class="mr-2">
                                            <a :href="'<?= APPConfig::getUrl('rest/') ?>' + item.slug" class="media-list-photo">
                                                <img :src="'<?= Yii::$app->homeUrl . 'uploads/' ?>' + item.thumbnail" height="150" width="225" :alt="item.name">
                                            </a>
                                        </div>
                                        <div class="media-body">
                                            <h5 class="media-title font-weight-bold"><a :href="'<?= APPConfig::getUrl('rest/') ?>' + item.slug">{{ item.name }}</a></h5>
                                            {{ item.subtitle }}
                                            <p class="text-muted"><i class="icon-bubble9 mr-1"></i> {{ item.count_comment ? item.count_comment : 0 }}</p>
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
            el: '#rest-page',
            data: {
                rests: null,
                pagination: null,
                loading: true,
                page: 1,
                perpage: 5,
                comment: 1,
                rating: 0,
                keyword: ''
            },
            created: function() {
                this.getRests()
            },
            watch: {
                page: function() {
                    this.getRests()
                }
            },
            methods: {
                getRests: function() {
                    var _this = this
                    var api = '<?= APPConfig::getUrl('place/get-rest-list') ?>' +
                        '?page=' + this.page + '&perpage=' + this.perpage + '&keyword=' + this.keyword + '&comment=' + this.comment + '&rating=' + this.rating

                    sendAjax(api, {}, 'GET', (resp) => {
                        if (resp.status) {
                            _this.rests = resp.rests
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