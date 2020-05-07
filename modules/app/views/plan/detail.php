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
<div class="content mt-3" id="edit-plan-page">
    <div class="edit-plan-content">
        <div class="card bg-indigo-400">
            <div class="card-header header-elements-inline">
                <h4 class="card-title font-weight-bold text-uppercase" v-cloak>#{{ plan.name }}</h4>
                <div class="header-elements">
                    <button class="btn bg-pink-400 rounded-round" @click="showMap">Xem trên bản đồ <i class="icon-map4 ml-2"></i></button>
                </div>
            </div>
        </div>

        <div class="plan-detail-wrap w-100 pb-3 px-3">
            <div class="date-item-wrap" v-for="(dateItem, didx) in plan.detail">
                <div class="card bg-indigo-400">
                    <div class="card-header header-elements-inline p-2">
                        <h4 class="card-title font-weight-bold" v-cloak>Ngày {{ didx + 1 }} ({{ dateItem.date }})</h4>
                        <div class="header-elements"></div>
                    </div>
                </div>
                <div class="date-places-item position-relative px-2">
                    <div class="overlay-loading position-absolute w-100 h-100 top-0 left-0 d-flex justify-content-center align-items-center border-radius-3" style="background:rgba(255,255,255,.9);z-index:10;" v-if="dateItem.calculating">
                        <i class="icon-spinner2 spinner"></i>
                    </div>
                    <div class="date-places">
                        <place-item-detail v-for="(place, pidx) in dateItem.places" :key="place.id +'-'+ place.time_start +'-'+ place.time_free +'-'+ place.time_start +'-'+ place.time_stay" :place="place" :pidx="pidx" :didx="didx" :placeofdate="dateItem.places.length" :movetype="moveType">
                        </place-item-detail>
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
                                        <h5 class="font-weight-bold border-bottom-1 border-bottom-dashed border-bottom-indigo-400" :class="map.dateView == didx ? 'text-pink-400' : ''" style="white-space: nowrap">
                                            Ngày {{ didx + 1 }}
                                        </h5>
                                    </a>
                                </div>
                                <div class="map-date-detail w-100">
                                    <div class="list-feed">
                                        <div class="list-feed-item d-flex justify-content-between border-pink-400" v-for="(place, pidx) in plan.detail[map.dateView].places">
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
        Vue.component('v-select', VueSelect.VueSelect);
        var plan = JSON.parse(`<?= json_encode($model) ?>`)
        var vm = new Vue({
            el: '#edit-plan-page',
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
                map: {
                    dateView: 0
                }
            },
            mounted: function() {
                initMap()
            },
            methods: {
                viewDetailOfDate: function(didx) {
                    this.map.dateView = didx
                    drawPlaces(this.plan.detail[this.map.dateView].places)
                    drawPlan(this.plan.routes[this.map.dateView])
                },

                showMap: function() {
                    var _this = this
                    $('#mapPreviewModal').modal()
                    setTimeout(() => {
                        DATA.map.invalidateSize()
                        drawPlaces(_this.plan.detail[_this.map.dateView].places)
                        drawPlan(_this.plan.routes[_this.map.dateView])
                    }, 200)
                },

                rangeTimeFormat: function(minute) {
                    return convertMinuteToTime(minute, 'range');
                },

                oclockTimeFormat: function(minute) {
                    return convertMinuteToTime(minute, 'oclock');
                },

                zoomToPlace: function(item) {
                    zoomToPlace(item)
                }
            }
        })
    })
</script>