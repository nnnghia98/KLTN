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
        ['Chỉnh sửa lịch trình']
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
                        <div class="header-elements">
                            <div class="dropdown">
                                <a class="list-icons-item dropdown-toggle caret-0 text-white" data-toggle="dropdown" aria-expanded="false">
                                    <i class="icon-notebook"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(16px, 19px, 0px); width: 250px;">
                                    <div class="p-2">
                                        <textarea class="form-control" cols="35" rows="4" v-model="dateItem.note"></textarea>
                                    </div>
                                    <div class="d-flex justify-content-end p-2">
                                        <button class="btn btn-primary btn-sm">Lưu</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="date-places-item position-relative px-2">
                    <div class="overlay-loading position-absolute w-100 h-100 top-0 left-0 d-flex justify-content-center align-items-center border-radius-3" style="background:rgba(255,255,255,.9);z-index:10;" v-if="dateItem.calculating">
                        <i class="icon-spinner2 spinner"></i>
                    </div>
                    <div class="date-places">
                        <draggable v-model="dateItem.places" @end="recalculatePlacesOfDate(didx)">
                            <transition-group>
                                <place-item v-for="(place, pidx) in dateItem.places" :key="place.id +'-'+ place.time_start +'-'+ place.time_free +'-'+ place.time_start +'-'+ place.time_stay" :place="place" :pidx="pidx" :didx="didx" :placeofdate="dateItem.places.length" :movetype="moveType" @get-recents="getRecents" @remove-place="removePlace" @on-modify-place="onModifyPlace" @on-change-time-start="onChangeTimeStart">
                                </place-item>
                            </transition-group>
                        </draggable>
                        <div class="place-item btn-add-place mt-3" data-toggle="modal" data-target="#placeListModal" @click="openPlacesModal(didx)">
                            <div class="add-place-content d-flex justify-content-center align-items-center">
                                <h4 class="mb-0"><i class="icon-plus2 mr-2"></i> Thêm địa điểm</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center my-3">
            <h6 class="saving-message"></h6>
            <button id="btn-save-plan" class="btn btn-lg bg-pink-400 rounded-round font-weight-bold" style="font-size: 1rem" @click="savePlan">
                Lưu lịch trình
            </button>
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
                    <div class="form-search" v-cloak>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label for="" class="font-weight-bold">Loại địa điểm</label>
                                <select class="form-control" v-model="places.query.type">
                                    <option value="<?= PlaceService::$TYPE['VISIT'] ?>">Tham quan</option>
                                    <option value="<?= PlaceService::$TYPE['FOOD'] ?>">Ăn uống</option>
                                    <option value="<?= PlaceService::$TYPE['REST'] ?>">Nghỉ ngơi</option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="" class="font-weight-bold">Tên địa điểm</label>
                                <input type="text" class="form-control" placeholder="Tên địa điểm" v-model="places.query.keyword">
                            </div>
                            <div class="col-md-4 form-group text-right mt-auto">
                                <button class="btn bg-pink-400 rounded-round" @click="searchPlaces">Tìm kiếm</button>
                            </div>
                        </div>
                    </div>
                    <div class="place-list" v-cloak>
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
            computed: {
                queryPage: function() {
                    return this.places.query.page
                }
            },
            mounted: function() {
                initMap()
            },
            created: function() {
                this.plan.detail = this.plan.detail.length > 0 ? this.plan.detail : this.createDefaultDetail()
                this.getPlaces()
            },
            watch: {
                queryPage: function() {
                    this.getPlaces()
                },
            },
            methods: {
                getPlaces: function() {
                    var _this = this,
                        query = this.places.query,
                        api = `<?= APPConfig::getUrl('place/get-list') ?>` +
                        `?page=${query.page}&keyword=${query.keyword}&destination=${this.plan.destination_id}&type=${query.type}&sort=${query.sort}&lat=${query.center[0]}&lng=${query.center[1]}`

                    this.places.loading = true
                    sendAjax(api, {}, 'GET', (resp) => {
                        if (resp.status) {
                            _this.places.data = resp.places
                            _this.places.pagination = resp.pagination
                        } else {
                            toastMessage('error', 'Lỗi!')
                        }
                        this.places.loading = false
                    })
                },

                openPlacesModal: function(didx) {
                    this.dateTarget = didx

                    this.places.query.center = ['', '']
                    this.places.query.page = 1
                    this.$nextTick(function() {
                        this.getPlaces()
                    })
                },

                getRecents: function(didx, lat, lng) {
                    this.dateTarget = didx
                    
                    this.places.query.center = [lat, lng]
                    this.places.query.keyword = ''
                    this.places.query.page = 1
                    this.$nextTick(function() {
                        this.getPlaces()
                    })
                },

                searchPlaces: function() {
                    this.places.query.page = 1

                    this.$nextTick(function() {
                        this.getPlaces()
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
                    this.plan.detail[didx].places.forEach(p => {
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
                        placesOfDate = this.plan.detail[didx].places,
                        newPlace = this.normalizePlaceData(place)

                    if (placesOfDate.length >= 1) {
                        this.showOverlayProcessSchedule(didx)
                        var lastPlace = this.plan.detail[didx].places[placesOfDate.length - 1],
                            waypoints = [lastPlace, newPlace]
                        this.getRoutesAndDistancesBetweenLocations(waypoints, function(data) {
                            if (data == false) {
                                toastMessage('error', 'Có lỗi sảy ra, vui lòng thử lại')
                            } else {
                                data.forEach((wp, index) => {
                                    waypoints[index].distance = wp.length / 1000
                                    waypoints[index].time_move = Math.ceil(waypoints[index].distance / _this.moveType[waypoints[index].move_type].velocity * 60)
                                    waypoints[index + 1].time_start = _this.getTotalTimeFormFristPlace(didx, placesOfDate.length - 1)
                                })

                                _this.plan.detail[didx].places.push(newPlace)
                            }

                            _this.hideOverlayProcessSchedule(didx)
                        })
                    } else {
                        _this.plan.detail[didx].time_start = 480; //480' = 08:am
                        newPlace.time_start = 480; //480' = 08:am
                        _this.plan.detail[didx].places.push(newPlace);
                    }
                },

                getRoutesAndDistancesBetweenLocations: function(waypoints, callback) {
                    var api = `https://route.ls.hereapi.com/routing/7.2/calculateroute.json?apiKey=<?= PlanService::$HERE_API_KEY ?>`

                    waypoints.forEach((wp, index) => {
                        api += `&waypoint${index}=geo!${wp.lat},${wp.lng}`
                    })

                    api += `&routeattributes=sm&mode=fastest;car`

                    sendAjax(api, {}, 'GET', (resp) => {
                        if (resp.response.route) {
                            callback(resp.response.route[0].leg);
                        } else {
                            callback(false);
                        }
                    })
                },

                getTotalTimeFormFristPlace(didx, pidx) {
                    var totalTime = 0,
                        place = this.plan.detail[didx].places[pidx]

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

                removePlace: function(didx, pidx) {
                    if (pidx != this.plan.detail[didx].places.length - 1) {
                        this.plan.detail[didx].places.splice(pidx, 1)
                        this.recalculatePlacesOfDate(didx)
                    } else {
                        this.plan.detail[didx].places.splice(pidx, 1)
                    }

                },

                onModifyPlace: function(didx, pidx) {
                    if (pidx != this.plan.detail[didx].places.length - 1) {
                        var totaltime = this.getTotalTimeFormFristPlace(didx, pidx)
                        this.updateStartTimeFromIdxToIdx(didx, pidx + 1, this.plan.detail[didx].places.length - 1, totaltime)
                    }
                },

                onChangeTimeStart: function(oldvalue, newvalue, didx, pidx) {
                    if (pidx != 0) {
                        if (newvalue > oldvalue) {
                            this.plan.detail[didx].places[pidx - 1].time_free = newvalue - oldvalue
                        } else if (newvalue < oldvalue) {
                            var totaltime = this.plan.detail[didx].places[0].time_start - (oldvalue - newvalue)
                            this.updateStartTimeFromIdxToIdx(didx, 0, pidx - 1, totaltime)
                        }
                    }

                    if (pidx != this.plan.detail[didx].places.length - 1) {
                        var totaltime = this.getTotalTimeFormFristPlace(didx, pidx)
                        this.updateStartTimeFromIdxToIdx(didx, pidx + 1, this.plan.detail[didx].places.length - 1, totaltime)
                    }
                },

                updateStartTimeFromIdxToIdx: function(didx, fromidx, toidx, totaltime) {
                    var total_time = totaltime
                    for (var i = fromidx; i <= toidx; i++) {
                        this.plan.detail[didx].places[i].time_start = total_time
                        var place = this.plan.detail[didx].places[i]
                        total_time += parseInt(place.time_stay) + parseInt(place.time_free) + parseInt(place.time_move)
                    }
                    //update start time of date
                    if (fromidx == 0) {
                        this.plan.detail[didx].time_start = this.plan.detail[didx].places[0].time_start
                    }
                },

                recalculatePlacesOfDate: function(didx) {
                    var _this = this;
                    var coords = [];
                    if (this.plan.detail[didx].places.length == 0) {
                        return;
                    } else if (this.plan.detail[didx].places.length == 1) {
                        this.plan.detail[didx].places[0].time_start = this.plan.detail[didx].time_start;
                    } else if (this.plan.detail[didx].places.length > 1) {
                        this.showOverlayProcessSchedule(didx)
                        var waypoints = this.plan.detail[didx].places
                        this.getRoutesAndDistancesBetweenLocations(waypoints, function(data) {
                            if (data == false) {
                                toastMessage('error', 'Có lỗi sảy ra, vui lòng thử lại')
                            } else {
                                data.forEach((wp, index) => {
                                    if (index == 0) {
                                        waypoints[index].time_start = _this.plan.detail[didx].time_start
                                    }
                                    waypoints[index].distance = wp.length / 1000
                                    waypoints[index].time_move = Math.ceil(waypoints[index].distance / _this.moveType[waypoints[index].move_type].velocity * 60)
                                    waypoints[index + 1].time_start = _this.getTotalTimeFormFristPlace(didx, index)
                                })
                            }

                            _this.hideOverlayProcessSchedule(didx)
                        })
                    }
                },

                showOverlayProcessSchedule: function(didx) {
                    this.plan.detail[didx].calculating = true;
                },

                hideOverlayProcessSchedule: function(didx) {
                    var _this = this;
                    _this.$nextTick(function() {
                        _this.plan.detail[didx].calculating = false;
                    })
                },

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
                        drawPlaces(this.plan.detail[this.map.dateView].places)
                        this.getRoutesFromHere(() => {
                            drawPlan(_this.plan.routes[_this.map.dateView])
                        })
                    }, 200)
                },

                rangeTimeFormat: function(minute) {
                    return convertMinuteToTime(minute, 'range');
                },

                oclockTimeFormat: function(minute) {
                    return convertMinuteToTime(minute, 'oclock');
                },

                getRoutesFromHere: function(callback) {
                    var _this = this,
                        promies = []

                    this.plan.detail.forEach(function(dateItem, index) {
                        var api = 'https://route.ls.hereapi.com/routing/7.2/calculateroute.json?apiKey=hPtC4kp3SDaqlFsNbcT_zPpyknvCfWEdcxejzcUk8zI'
                        dateItem.places.forEach((wp, idx) => {
                            api += `&waypoint${idx}=geo!${wp.lat},${wp.lng}`
                        })
                        api += '&routeattributes=sh&mode=fastest;car';

                        var request = $.ajax({
                            url: api,
                            type: 'GET',
                            success: function(resp) {
                                if (resp.response.route) {
                                    var data = resp.response.route[0]
                                    var geojson = {
                                        type: 'LineString',
                                        coordinates: []
                                    }
                                    data.shape.forEach((latlng) => {
                                        geojson.coordinates.push(latlng.split(',').reverse())
                                    })

                                    var counter = 0
                                    data.waypoint.forEach((wp, idx) => {
                                        var place = dateItem.places[idx]
                                        if(idx != dateItem.places.length - 1) {
                                            geojson.coordinates.splice(wp.shapeIndex + counter, 0, [place.lng, place.lat])
                                            counter++
                                        } else {
                                            geojson.coordinates.push([place.lng, place.lat])
                                        }
                                        
                                    })

                                    _this.plan.routes[index] = geojson
                                } else {
                                    toastMessage('error', 'Không thể lấy thông tin tuyến đường')
                                }
                            },
                            error: function(msg) {
                                console.log(msg)
                            }
                        })

                        promies.push(request)
                    })

                    $.when.apply(null, promies).done(callback)
                },

                zoomToPlace: function(item) {
                    zoomToPlace(item)
                },

                savePlan: function() {
                    var _this = this,
                        ladda = Ladda.create($('#btn-save-plan')[0]),
                        savingMsg = $('.saving-message')

                    ladda.start()
                    savingMsg.empty().append('Đang truy vấn tuyến đường')
                    this.getRoutesFromHere(() => {
                        var api = '<?= APPConfig::getUrl('plan/save') ?>',
                            data = {
                                detail: JSON.stringify(this.plan.detail),
                                routes: JSON.stringify(this.plan.routes),
                                planid: this.plan.id
                            }
                        savingMsg.empty().append('Đang lưu lịch trình')
                        sendAjax(api, data, 'POST', (resp) => {
                            if (resp.status) {
                                var slug = _this.plan.slug
                                // window.location.assign('<?= APPConfig::getUrl('plan/detail/') ?>' + slug)
                            } else {
                                toastMessage('error', resp.message)
                            }
                            savingMsg.empty()
                            ladda.stop()
                        })
                    })
                }
            }
        })
    })
</script>