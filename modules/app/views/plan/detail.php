<?php

use app\modules\app\APPConfig;
use app\modules\app\PathConfig;
use app\modules\cms\services\PlaceService;
use app\modules\cms\services\PlanService;
use app\modules\contrib\gxassets\GxLaddaAsset;
use app\modules\contrib\gxassets\GxLeafletAsset;
use app\modules\contrib\gxassets\GxVueDraggableAsset;
use app\modules\contrib\gxassets\GxVueSelectAsset;

GxLaddaAsset::register($this);
GxVueDraggableAsset::register($this);
GxVueSelectAsset::register($this);
GxLeafletAsset::register($this);

include('edit_ext.php');

$pageData = [
    'pageTitle' => $model['name'],
    'pageBreadcrumb' => [
        ['Lịch trình', APPConfig::getUrl('plan')],
        ['Chi tiết lịch trình']
    ],
    'backgoundHeader' => Yii::$app->homeUrl . 'resources/images/plan-header.jpg'
]; ?>
<?= $this->render(PathConfig::getAppViewPath('pageHeader'), $pageData); ?>
<div class="content mt-3" id="detail-plan-page">
    <div class="detail-plan-content">
        <div class="card">
            <div class="card-header header-elements-inline bg-light">
                <h4 class="card-title font-weight-bold text-uppercase" v-cloak>#{{ plan.name }}</h4>
                <div class="header-elements">
                    <div class="share-plan cursor-pointer" @click="sharePlan"><i class="icon-share3 icon-2x text-indigo-400"></i></div>
                    <div class="like-plan cursor-pointer ml-3" @click="likePlan"><i class="icon-heart6 icon-2x text-indigo-400"></i></div>
                    <div class="copy ml-3">
                        <a href="" class="btn bg-pink-400 rounded-round">Sao chép và chỉnh sửa <i class="ml-2 icon-copy3"></i></a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="overview-plan px-4 d-flex">
                    <div class="date-item-wrap my-3" v-for="(dateItem, didx) in plan.detail">
                        <div class="date-item-header mb-3"><h5 class="font-weight-bold">{{ dateItem.date }}</h5></div>
                        <div class="date-item-content">
                            <div class="list-feed list-feed-time">
                                <div class="list-feed-item border-pink-400" 
                                    v-for="(place, pidx) in dateItem.places">
                                    <div class="left-item">
                                        <span class="feed-time text-muted font-size-base">{{ oclockTimeFormat(place.time_start) }}</span>
                                        <h6 class="mb-0">{{ place.name }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card plan-detail">
                    <div class="card-header">
                        <h3 class="card-title">Lịch trình chi tiết</h3>
                    </div>
                    <div class="card-body">
                        <div class="map-date-list-wrap d-flex flex-row p-3">
                            <div class="map-date-list mr-4">
                                <a v-for="(dateItem, didx) in plan.detail" @click="viewDetailOfDate(didx)">
                                    <h5 class="font-weight-bold border-bottom-1 border-bottom-dashed border-bottom-indigo-400" :class="dateView == didx ? 'text-pink-400' : ''" style="white-space: nowrap">
                                        Ngày {{ didx + 1 }}
                                    </h5>
                                </a>
                            </div>
                            <div class="map-date-detail w-100">
                                <div class="date-places">
                                    <place-item-detail v-for="(place, pidx) in plan.detail[dateView].places" 
                                        :key="place.id +'-'+ place.time_start +'-'+ place.time_free +'-'+ place.time_start +'-'+ place.time_stay" 
                                        :place="place" 
                                        :pidx="pidx" 
                                        :didx="dateView" 
                                        :placeofdate="plan.detail[dateView].places.length" 
                                        :movetype="moveType">
                                    </place-item-detail>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card comment-wrap">
                    <div class="card-header">
                        <h3 class="card-title" v-cloak>Bình luận ({{ plan.count_comment ? plan.count_comment : '0' }})</h3>
                    </div>
                    <?php if (!Yii::$app->user->isGuest) : ?>
                        <div class="card-body">
                            <div class="form-group d-flex align-items-center">
                                <span class="mr-2">Đánh giá: </span>
                                <rating :star="interactive.star" @change="interactive.star = $event" :key="interactive.star"></rating>
                            </div>
                            <div class="form-group">
                                <textarea id="comment-text" class="form-control" rows="5">{{ interactive.comment ? interactive.comment : '' }}</textarea>
                            </div>
                            <div class="form-group text-right mb-0">
                                <button class="btn bg-pink-400 rounded-round" @click="submitComment">Bình luận</button>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <comment-list :comments="comments" :key="comments.length"></comment-list>
                        <div class="loadmore d-flex justify-content-center" v-if="comments.length < plan.count_comment" v-cloak>
                            <button class="btn btn-outline bg-pink-400 border-pink-400 text-pink-400 rounded-round" @click="getDestinationComments(++comment_page)">Xem thêm</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-body">
                    <h3 class="plan-title font-weight-bold">
                        {{ plan.name }}
                    </h3>
                    <p>
                        <rating-star-static :rating="plan.avg_rating" :key="plan.slug"></rating-star-static>
                    </p>
                    <hr>
                    <div class="counter">
                        <h6 class="d-flex justify-content-between"><span><i class="icon-star-full2 mr-2"></i>Đánh giá:</span><span>{{ (plan.count_rating ? plan.count_rating : '0') + ' lượt' }}</span></h6>
                        <h6 class="d-flex justify-content-between"><span><i class="icon-comment mr-2"></i>Bình luận:</span><span>{{ (plan.count_comment ? plan.count_comment : '0') + ' lượt' }}</span></h6>
                        <h6 class="d-flex justify-content-between"><span><i class="icon-heart5 mr-2"></i>Thích:</span><span>{{ (plan.count_like ? plan.count_like : '0') + ' lượt' }}</span></h6>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body d-flex justify-content-center">
                        <button class="btn bg-pink-400 rounded-round" @click="showMap">
                            Xem trên bản đồ <i class="icon-map4 ml-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bg-white h-100 p-0" id="mapPreviewModal" role="modal">
        <!-- map-preview -->
        <div class="modal-dialog modal-map-dialog m-0 h-100" role="document">
            <div class="modal-content h-100 border-radius-0" style="box-shadow: unset">
                <div class="modal-body h-100" style="overflow-y: scroll">
                    <div class="back-to-list-view" style="height: 40px">
                        <h4 class="text-indigo-400 mb-0 font-weight-bold">
                            <a data-dismiss="modal"><i class="icon-circle-left2 mr-2"></i>Xem danh sách</a>
                        </h4>
                    </div>
                    <div class="map-wrap row" style="height: calc(100% - 40px)">
                        <div class="col-md-4">
                            <div class="map-date-list-wrap d-flex flex-row p-3 card">
                                <div class="map-date-list mr-3">
                                    <a v-for="(dateItem, didx) in plan.detail" @click="viewDetailOfDate(didx)">
                                        <h5 class="font-weight-bold border-bottom-1 border-bottom-dashed border-bottom-indigo-400" :class="dateView == didx ? 'text-pink-400' : ''" style="white-space: nowrap">
                                            Ngày {{ didx + 1 }}
                                        </h5>
                                    </a>
                                </div>
                                <div class="map-date-detail w-100">
                                    <div class="list-feed">
                                        <div class="list-feed-item d-flex justify-content-between border-pink-400" v-for="(place, pidx) in plan.detail[dateView].places">
                                            <div class="left-item">
                                                <div class="text-muted">Bắt đầu: {{ oclockTimeFormat(place.time_start) }}</div>
                                                <a :href="'<?= APPConfig::getUrl('place/detail/') ?>' + place.slug">
                                                    <h5 class="mb-0">{{ place.name }}</h5>
                                                </a>
                                            </div>
                                            <div class="right-item my-auto">
                                                <span class="text-indigo-400 cursor-pointer btn-zoom-to-place" @click="zoomToPlace(place)"><i class="icon-location4"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8 h-100">
                            <div class="card h-100 overflow-hidden">
                                <div id="map-preview" class="h-100 w-100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        var plan = JSON.parse(`<?= json_encode($model) ?>`)
        var vm = new Vue({
            el: '#detail-plan-page',
            data: {
                plan: plan,
                moveType: [{
                        velocity: 40,
                        label: 'Xe máy',
                        icon: 'fas fa-motorcycle'
                    },
                    {
                        velocity: 60,
                        label: 'Xe ô tô',
                        icon: 'fas fa-car'
                    }, {
                        velocity: 5,
                        label: 'Người đi bộ',
                        icon: 'fas fa-walking'
                    }
                ],
                dateTarget: null,
                dateView: 0,
                comments: [],
                comment_page: 1,
                interactive: {}
            },
            mounted: function() {
                initMap()
            },
            created: function() {
                this.getPlanComments()
                this.getCurrentUserInteractive()
            },
            methods: {
                viewDetailOfDate: function(didx) {
                    this.dateView = didx
                    drawPlaces(this.plan.detail[this.dateView].places)
                    drawPlan(this.plan.routes[this.dateView])
                },

                showMap: function() {
                    var _this = this
                    $('#mapPreviewModal').modal()
                    setTimeout(() => {
                        DATA.map.invalidateSize()
                        drawPlaces(_this.plan.detail[_this.dateView].places)
                        drawPlan(_this.plan.routes[_this.dateView])
                    }, 200)
                },

                getPlanComments: function(page = 1) {
                    var _this = this
                    var api = '<?= APPConfig::getUrl('plan/get-comments') ?>' + `?id=${this.plan.id}&page=${page}`

                    sendAjax(api, {}, 'GET', (resp) => {
                        if (resp.status) {
                            _this.comments = _this.comments.concat(resp.comments)
                        }
                    })
                },

                getCurrentUserInteractive: function() {
                    var _this = this
                    var api = '<?= APPConfig::getUrl('plan/get-interactive') ?>' + `?id=${this.plan.id}`

                    sendAjax(api, {}, 'GET', (resp) => {
                        if (resp.status) {
                            _this.interactive = resp.interactive
                        }
                    })
                },

                sharePlan: function() {

                },

                likePlan: function() {

                },

                rangeTimeFormat: function(minute) {
                    return convertMinuteToTime(minute, 'range');
                },

                oclockTimeFormat: function(minute) {
                    return convertMinuteToTime(minute, 'oclock');
                },

                zoomToPlace: function(item) {
                    zoomToPlace(item)
                },

                submitComment: function() {
                    var _this = this
                    var api = '<?= APPConfig::getUrl('plan/submit-comment') ?>' + `?id=${this.plan.id}`,
                        data = {
                            id: this.plan.id,
                            star: this.interactive.star,
                            comment: this.interactive.comment,
                        }

                    sendAjax(api, data, 'POST', (resp) => {
                        if (resp.status) {
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