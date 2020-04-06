<?php
use app\modules\app\APPConfig;
use app\modules\contrib\gxassets\GxLeafletPruneClusterAsset;

GxLeafletPruneClusterAsset::register($this);
?>
<div id="destination-map-page" class="map-page">
    <div class="map-wrap d-flex flex-1 position-relative h-100">
        <div class="map-sidebar h-100 bg-white show" id="map-sidebar">
            <div class="sidebar-content position-relative h-100">
                <div class="sidebar-toggle position-absolute top-0 px-2 py-3 bg-white cursor-pointer" onclick="toggleSidebar()">
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
                perpage: 5,
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
                            _this.loading = false
                        } else {
                            toastMessage('error', resp.message)
                        }
                    })
                },
            }
        })

        fixMapHeight()
    })
</script>