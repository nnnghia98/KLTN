<?php
use app\modules\app\APPConfig;
use app\modules\contrib\gxassets\GxVueSelectAsset;
use app\modules\contrib\gxassets\GxLeafletAsset;

GxLeafletAsset::register($this);
GxVueSelectAsset::register($this);
?>
<div id="rest-map-page" class="map-page">
    <div class="map-wrap d-flex flex-1 position-relative h-100">
        <div class="map-sidebar h-100 bg-white show" id="map-sidebar">
            <div class="position-relative h-100">
                <div class="sidebar-content h-100 overflow-auto">
                    <div class="sidebar-header p-3">
                        <h4 class="mb-0 font-weight-bold"><a href="<?= APPConfig::getUrl('place/rest') ?>"><i class="icon-circle-left2 mr-2"></i></a>Bản đồ địa điểm nghỉ ngơi</h4>
                    </div>
                    <hr class="my-1">
                    <div class="form-search p-3">
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control border-right-0" placeholder="Tên địa điểm nghỉ ngơi" v-model="keyword">
                                <span class="input-group-append" @click="getRests()">
                                    <span class="input-group-text bg-indigo-400 border-indigo-400 text-white">
                                        <i class="icon-search4"></i>
                                    </span>
                                </span>
                            </div>
                        </div> 
                        <div class="form-group">
                            <label for="" class="font-weight-bold">Điểm đến</label> 
                            <v-select :options="destinationCategories" :reduce="selected => selected.code" v-model="destination"></v-select>
                        </div>
                    </div>
                    <hr class="my-1">
                    <div class="data-wrap">
                        <div class="data-content pb-3">
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
        Vue.component('v-select', VueSelect.VueSelect)
        var vm = new Vue({
            el: '#rest-map-page',
            data: {
                rests: [],
                pagination: {},
                loading: true,
                page: 1,
                perpage: 20,
                keyword: '',
                comment: 1,
                rating: 0,
                destination: 13,
                destinationCategories: []
            },
            created: function() {
                var _this = this
                _this.$nextTick(function() {
                    initMap()
                    _this.getRests()
                    _this.getDestinations()
                })
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
                    var api = '<?= APPConfig::getUrl('place/get-rest-list') ?>' +
                        '?page=' + this.page + '&perpage=' + this.perpage + '&destination=' + this.destination + '&keyword=' + this.keyword + '&comment=' + this.comment + '&rating=' + this.rating

                    sendAjax(api, {}, 'GET', (resp) => {
                        if (resp.status) {
                            _this.rests = resp.rests
                            _this.pagination = resp.pagination
                            initLayer(_this.rests)
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
                },

                zoomToDestination: function(target) {
                    DATA.map.flyTo([target.lat, target.lng], 9)
                    DATA.layers.overlay['place'].eachLayer(function(layer) {
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