<?php

use app\modules\app\APPConfig;
use app\modules\app\PathConfig;
use app\modules\cms\services\PlaceService;
use app\modules\cms\services\PlanService;
use app\modules\contrib\gxassets\GxVueDraggableAsset;

use app\modules\contrib\gxassets\GxVueSelectAsset;
use app\modules\contrib\gxassets\GxVuetifyAsset;

GxVueDraggableAsset::register($this);

GxVueSelectAsset::register($this);
GxVuetifyAsset::register($this);
include('edit_ext.php');

$pageData = [
    'pageTitle' => $model['name'],
    'pageBreadcrumb' => 'Chỉnh sửa lịch trình',
    'backgoundHeader' => Yii::$app->homeUrl . 'resources/images/plan-header.jpg'
]; ?>
<?= $this->render(PathConfig::getAppViewPath('pageListHeader'), $pageData); ?>

<style>
    .v-application--wrap {
        min-height: unset;
    }
</style>
<div class="content mt-3" id="edit-plan-page">
    <div class="edit-plan-content">
        <div class="card bg-indigo-400">
            <div class="card-header header-elements-inline">
                <h4 class="card-title font-weight-bold text-uppercase">#{{ plan.name }}</h4>
            </div>
        </div>

        <div class="plan-detail-wrap w-100">
            <div class="date-item-wrap" v-for="(dateItem, didx) in detail">
                <div class="card bg-indigo-400">
                    <div class="card-header header-elements-inline p-2">
                        <h4 class="card-title font-weight-bold">Ngày {{ didx + 1 }}</h4>
                        <div class="header-elements"></div>
                    </div>
                </div>
                <div class="date-places-item position-relative">
                    <div class="overlay-loading position-absolute w-100 h-100 top-0 left-0 d-flex justify-content-center align-items-center" style="background:rgba(255,255,255,.9);z-index:100;" v-if="dateItem.calculating">
                        <i class="icon-spinner2 spinner"></i>
                    </div>
                    <div class="date-places">
                        <draggable v-model="dateItem.places" @end="updateDistanceAndStartTimeOfDate(didx)">
                            <transition-group>
                                <place-item v-for="(place, pidx) in dateItem.places" 
                                    :key="place.id + place.time_start" 
                                    :place="place" :pidx="pidx" :didx="didx" :placeofdate="dateItem.places.length" :movetype="moveType" @remove-place="removePlace" @save-note="saveNote" @save-time-start="saveTimeStart" @save-time-stay="saveTimeStay" @save-time-free="saveTimeFree" @change-move-type="changeMoveType">
                                </place-item>
                            </transition-group>
                        </draggable>
                        <div class="place-item btn-add-place" data-toggle="modal" data-target="#placeListModal" @click="dateTarget = didx">
                            <div class="add-place-content d-flex justify-content-center align-items-center">
                                <h4 class="mb-0"><i class="icon-plus2 mr-2"></i> Thêm địa điểm</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade place-list-modal" id="placeListModal" tabindex="-1" role="dialog" aria-labelledby="placeListModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-indigo-400">
                    <h4 class="modal-title font-weight-bold" id="placeListModalLabel">Danh sách địa điểm</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-search">

                    </div>
                    <div class="place-list">
                        <div class="loading-data d-flex justify-content-center p-3" style="height: 100vh" v-if="places.loading">
                            <div class="loading-content"><i class="icon-spinner2 spinner icon-2x"></i></div>
                        </div>
                        <div class="loaded-data" v-else>
                            <div class="empty-data d-flex justify-content-center p-3" v-if="places.data.length == 0">
                                <h4 class="font-weight-bold mb-0">Không có dữ liệu</h4>
                            </div>
                            <div class="available-data" v-else>
                                <div class="data-summary py-2 px-3">
                                    <pagination-summary :current="places.pagination.current" :from="places.pagination.from" :to="places.pagination.to" :total="places.pagination.total"></pagination-summary>
                                </div>
                                <div class="media flex-column flex-sm-row mt-0" v-cloak>
                                    <ul class="media-list media-list-linked media-list-bordered w-100">
                                        <place-choosen v-for="item in places.data" :place="item" :target="dateTarget" @add="addPlace">
                                            </place-choosen>
                                    </ul>
                                </div>
                                <div class="data-summary py-2 px-3 mb-3">
                                    <pagination-summary :current="places.pagination.current" :from="places.pagination.from" :to="places.pagination.to" :total="places.pagination.total"></pagination-summary>
                                </div>
                                <div class="pagination-wrap" v-if="places.pagination.pages > 1">
                                    <pagination :current="places.pagination.current" :pages="places.pagination.pages" @change="places.query.page = $event"></pagination>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        Vue.component('v-select', VueSelect.VueSelect);
        var plan = JSON.parse(`<?= json_encode($model) ?>`)
        var detail = plan.detail
        var vm = new Vue({
            el: '#edit-plan-page',
            data: {
                plan: plan,
                detail: detail ? detail : [],
                places: {
                    data: {},
                    pagination: {},
                    loading: false,
                    query: {
                        center: ['', ''],
                        type: '<?= PlaceService::$TYPE['VISIT'] ?>',
                        keyword: '',
                        page: 1,
                        sort: 'avg_rating'
                    },
                },
                dataOfPlaceEditing: {
                    didx: null,
                    pidx: null,
                    note: null,
                    time_start: null,
                    time_stay: null,
                    time_move: null,
                    time_free: null,
                    distance: null
                },
                moveType: [
                    {
                        type: 'car',
                        label: 'Xe ô tô',
                        icon: 'icon-car'
                    }, {
                        type: 'pedestrian',
                        label: 'Người đi bộ',
                        icon: 'icon-footprint'
                    }, {
                        type: 'bicycle',
                        label: 'Xe đạp',
                        icon: 'icon-bike'
                    }
                ],
                dateTarget: null,
                openTimeStayBox: false,
                openTimeStartBox: false,
                openTimeFreeBox: false,
                openTimeFreeBox: false,
                openMoveTypeBox: false,
                openNoteBox: false
            },

            vuetify: new Vuetify(),
            computed: {},
            created: function() {
                this.detail = this.detail.length > 0 ? this.detail : this.createDefaultDetail()
                this.getPlaces()
            },
            methods: {
                getPlaces: function() {
                    var _this = this,
                        query = this.places.query,
                        api = '<?= APPConfig::getUrl('place/get-list') ?>' +
                        `?page=${query.page}&keyword=${query.keyword}&destination=${this.plan.destination_id}&type=${query.type}&sort=${query.sort}&lat=${query.center[0]}&lng=${query.center[1]}`

                    sendAjax(api, {}, 'GET', (resp) => {
                        if (resp.status) {
                            _this.places.data = resp.places
                            _this.places.pagination = resp.pagination
                        } else {
                            toastMessage('error', 'Lỗi!')
                        }
                    })
                },

                createDefaultDetail: function() {
                    var detail = []
                    for (var i = 0; i < this.plan.total_day; i++) {
                        detail.push({
                            date: this.plusDays(this.plan.date_start, i),
                            time_start: 480,
                            calculating: false,
                            note: '',
                            places: []
                        });
                    }

                    return detail
                },

                plusDays: function(dateStr, days) {
                    var dateObj = new Date(dateStr)
                    dateObj.setDate(dateObj.getDate() + days)

                    var month = String(dateObj.getMonth() + 1).padStart(2, '0')
                    var day = String(dateObj.getDate()).padStart(2, '0')
                    var year = dateObj.getFullYear()

                    return `${day}-${month}-${year}`
                },

                addPlace: function(place, didx) {
                    var duplicate = false
                    this.detail[didx].places.forEach(p => {
                        if (p.slug == place.slug) {
                            duplicate = true
                            toastMessage('error', 'Địa điểm đã có trong lịch trình')
                        }
                    })

                    if (!duplicate) {
                        this.calDistanceBetweenLastPlaceInDateWithNewPlace(place, didx);
                    }
                },

                calDistanceBetweenLastPlaceInDateWithNewPlace: function(place, didx) {
                    var _this = this,
                        coordsStr = '',
                        placesOfDate = this.detail[didx].places,
                        newPlace = this.normalizePlaceData(place)

                    if (placesOfDate.length >= 1) {
                        this.showOverlayProcessSchedule(didx)
                        var lastPlace = this.detail[didx].places[placesOfDate.length - 1];
                        this.getRoutesAndDistancesBetweenLocations(lastPlace, newPlace, this.moveType[0].type, function(response) {
                            if (response == false) {
                                toastMessage('error', 'Có lỗi sảy ra, vui lòng thử lại');
                            } else {
                                lastPlace.distance = response.distance / 1000;
                                lastPlace.time_move = response.travelTime / 60;
                                newPlace.time_start = _this.getTotalTimeFormFristPlace(didx, placesOfDate.length - 1);
                                _this.detail[didx].places.push(newPlace);
                            }

                            _this.hideOverlayProcessSchedule(didx)
                        })
                    } else {
                        _this.detail[didx].time_start = 480; //480' = 08:am
                        newPlace.time_start = 480; //480' = 08:am
                        _this.detail[didx].places.push(newPlace);
                    }
                },


                //transporttype: car | pedestrian
                getRoutesAndDistancesBetweenLocations: function(waypoint0, waypoint1, moveType, callback) {
                    var api = `https://route.ls.hereapi.com/routing/7.2/calculateroute.json?apiKey=<?= PlanService::$HERE_API_KEY ?>
                            &waypoint0=geo!${waypoint0.lat},${waypoint0.lng}&waypoint1=geo!${waypoint0.lat},${waypoint0.lng}&routeattributes=sm&mode=fastest;${moveType}`;

                    sendAjax(api, {}, 'GET', (resp) => {
                        if (resp.response.route) {
                            callback(resp.response.route[0].summary);
                        } else {
                            callback(false);
                        }
                    })
                },

                getTotalTimeFormFristPlace(didx, pidx) {
                    var totalTime = 0,
                        place = this.detail[didx].places[pidx]

                    totalTime += parseInt(place.time_start) + parseInt(place.time_stay) + parseInt(place.time_move) + parseInt(place.time_free);
                    return totalTime;
                },

                normalizePlaceData: function(place) {
                    var placeData = {
                        name: place.name,
                        time_start: 480,
                        time_stay: place.time_stay ? place.time_stay : 60,
                        time_move: 0,
                        time_free: 0,
                        id: place.id,
                        lat: place.lat,
                        lng: place.lng,
                        thumbnail: place.thumbnail,
                        didx: this.dateTarget,
                        note: '',
                        move_type: 0,
                        distance: 0,
                        slug: place.slug
                    }

                    return placeData;
                },

                removePlace: function() {

                },

                saveNote: function() {

                },

                saveTimeStart: function() {

                },
                
                saveTimeFree: function() {
                    
                },

                saveTimeStay: function() {

                },

                changeMoveType: function() {

                },

                openEditingBox: function(e, didx, pidx, place, box_type) {
                    var _this = this,
                        offset = $(e.target).offset()

                    if (didx !== _this.dataOfPlaceEditing.didx || pidx !== _this.dataOfPlaceEditing.pidx) {
                        _this.dataOfPlaceEditing = {
                            didx: didx,
                            pidx: pidx,
                            note: place.note,
                            time_start: place.time_start,
                            time_stay: place.time_stay,
                            time_free: place.time_free,
                            distance: place.distance
                        }
                    }

                    switch (box_type) {
                        case 'move_type':
                            _this.openMoveTypeBox = true;
                            break;
                        case 'time_stay':
                            _this.openStayTimeBox = true;
                            break;
                        case 'time_start':
                            _this.openStartTimeBox = true;
                            break;
                        case 'time_free':
                            _this.openTimeFreeBox = true;
                            break;
                        case 'note':
                            _this.openNoteBox = true;
                            break;
                    }

                    _this.$nextTick(function() {
                        var ofssetTop = offset.top + 25;
                        var ofssetLeft = offset.left - 10;
                        $('.place-editing-box').css('opacity', 1);
                        $('.place-editing-box').css('top', ofssetTop);
                        $('.place-editing-box').css('left', ofssetLeft);
                    })
                },

                showOverlayProcessSchedule: function(didx) {
                    this.detail[didx].calculating = true;
                },

                hideOverlayProcessSchedule: function(didx) {
                    var _this = this;
                    _this.$nextTick(function() {
                        _this.detail[didx].calculating = false;
                    })
                },
            }
        })
    })
</script>