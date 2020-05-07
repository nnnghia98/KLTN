<?php
use app\modules\app\APPConfig;
use app\modules\contrib\gxassets\GxLeafletAsset;

GxLeafletAsset::register($this);
?>
<div id="destination-map-page" class="map-page">
    <div class="map-wrap d-flex flex-1 position-relative h-100">
        <div class="map-sidebar h-100 bg-white show" id="map-sidebar">
            <div class="position-relative h-100">
                <div class="sidebar-content h-100 overflow-auto">
                    <div class="sidebar-header p-3">
                        <h4 class="mb-0 font-weight-bold"><a href="<?= APPConfig::getUrl('destination') ?>"><i class="icon-circle-left2 mr-2"></i></a>Bản đồ điểm đến</h4>
                    </div>
                    <hr class="my-1">
                    <div class="form-search p-3">
                        <div class="form-group mb-0">
                            <div class="input-group">
                                <input type="text" class="form-control border-right-0" placeholder="Tên điểm đến" v-model="keyword">
                                <span class="input-group-append" @click="getDestinations()">
                                    <span class="input-group-text bg-indigo-400 border-indigo-400 text-white">
                                        <i class="icon-search4"></i>
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <hr class="my-1">
                    <div class="data-wrap">
                        <div class="data-content pb-3">
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
                                    <div class="media flex-column flex-sm-row mt-0" v-cloak>
                                        <ul class="media-list media-list-linked media-list-bordered w-100">
                                            <li v-for="item in destinations">
                                                <div class="media">
                                                    <div class="mr-2">
                                                        <a :href="'<?= APPConfig::getUrl('destination/') ?>' + item.slug" class="media-list-photo">
                                                            <img :src="'<?= Yii::$app->homeUrl . 'uploads/' ?>' + item.thumbnail" height="35" width="50" :alt="item.name">
                                                        </a>
                                                    </div>
                                                    <div class="media-body">
                                                        <h4 class="media-title font-weight-bold">
                                                            <a :href="'<?= APPConfig::getUrl('destination/') ?>' + item.slug">{{ item.name }}</a>
                                                        </h4>
                                                        <h6 class="mb-0 text-muted">{{ item.subtitle }}</h6>
                                                        <rating-star-static :rating="item.avg_rating" :key="item.slug"></rating-star-static>
                                                        <p class="text-muted"><i class="icon-comment mr-1"></i> {{ item.count_comment ? item.count_comment : 0 }}</p>
                                                    </div>
                                                    <div class="ml-1">
                                                        <button class="btn btn-sm btn-icon btn-outline-primary" @click="zoomToDestination(item)"><i class="icon-location4"></i></a></button>
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
                <div class="sidebar-toggle position-absolute bottom-0 px-1 py-2 bg-white cursor-pointer" onclick="toggleSidebar()">
                    <i class="icon-arrow-left5 icon-2x"></i>
                </div>
            </div>
        </div>
        <div class="map-content h-100 d-flex flex-1">
            <div id="map" class="h-100 w-100"></div>
        </div>
    </div>
</div>
<?php include('map_ext.php') ?>
<script>
    $(function() {
        var vm = new Vue({
            el: '#destination-map-page',
            data: {
                destinations: null,
                pagination: null,
                loading: true,
                page: 1,
                perpage: 20,
                comment: 1,
                rating: 0,
                keyword: ''
            },
            created: function() {
                var _this = this
                _this.$nextTick(function() {
                    initMap()
                    _this.getDestinations()
                })
                
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
                            initLayer(_this.destinations)
                            _this.loading = false
                        } else {
                            toastMessage('error', resp.message)
                        }
                    })
                },

                zoomToDestination: function(target) {
                    DATA.map.flyTo([target.lat, target.lng], 9)
                    DATA.layers.overlay['destination'].eachLayer(function(layer) {
                        if(layer.ID == target.id) {
                            layer.openPopup()
                        }
                    })
                }
            }
        })

        fixMapHeight()
    })

    $(window).on('resize', function() {
        fixMapHeight()
    })
</script>