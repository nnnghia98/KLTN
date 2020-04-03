<div class='gxmap_create_map_container h-100'>
    <div class='row m-0 position-relative overflow-hidden' style="height: calc(100% - 50px);">
        <div id='gxmap_create_map' class='col-12 p-0 h-100' style="z-index: 99"></div>
        <div id="_placecontrolcontainer" style="width: 250px; position: absolute; top: 12px; left: 58px; background: white; z-index: 1000">
            <input id="_searchplacecontrol" placeholder="Nhập vào địa chỉ cần tìm..." class="form-control px-2">
            <div id="_placecontrolitems" class="place-items"></div>
        </div>
    </div>
    <div class="row form-group m-0 d-flex align-items-center bg-white" style="height: 50px">
        <div class="col-2 col-md-1 col-form-label">
            <span class="font-weight-semibold">Lat</span>
        </div>
        <div class="col-4 col-md-5">
            <input class="form-control" id='geom_lat' name="Work[lat]" type="text" onblur="onBlurLatLng()" />
        </div>
        <div class="col-2 col-md-1 col-form-label">
            <span class="font-weight-semibold">Lng</span>
        </div>
        <div class="col-4 col-md-5">
            <input class="form-control" id='geom_lng' name="Work[lng]" type="text" onblur="onBlurLatLng()" />
        </div>
    </div>
</div>

<script type="application/javascript">
    var DATA = {
        map: null,
        layers: {
            base: [],
            overlay: []
        },
        icons: {},
        controls: {},
    };

    var lat = <?= $lat ?>;
    var lng = <?= $lng ?>;

    $(function() {
        if(!DATA.map) {
            initMap();
        }
    });

    function initMap() {
        DATA.map = L.map('gxmap_create_map', {
            minZoom: 0,
            maxZoom: 22
        }).setView([lat, lng], 6);
        initControl();
        initExtends();
    }

    function initControl() {
        initGoogleLayer();
        initSearchPlaceControl();
    }

    function initExtends() {
        initDragMarker(null, false);
        initClickToMapEvent();
    }

    function initGoogleLayer() {
        DATA.layers.base['Google'] = L.tileLayer('https://{s}.google.com/vt/lyrs=' + 'r' + '&x={x}&y={y}&z={z}', {
            maxZoom: 22,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
        });
        DATA.layers.base['Google'].addTo(DATA.map);
    }

    function initSearchPlaceControl() {
        let placeControlId = '_searchplacecontrol';
        let placeControlItemsId = '_placecontrolitems';

        let placeControl = $('#' + placeControlId);
        let placeControlItems = $('#' + placeControlItemsId)
        placeControl.on('input', function(e) {
            $.ajax({
                url: 'https://places.demo.api.here.com/places/v1/discover/search?app_id=zSfLmO4akpNNRkXp0CG9&app_code=Qx4lDVRUvipDhgpvpMjFFg&at=10.7974,106.7348&q=' + placeControl.val(),
                success: function(e) {
                    let items = e.results.items;
                    placeControlResults = e.results;
                    placeControlItems.empty();
                    for(let i=0; i<items.length; i++) {
                        let item = items[i];
                        let placeItemHtml = "<div class='place-item' style='padding: 5px; cursor: pointer' data-idx='"+ i +"'>"+ item.title +"</div>";
                        placeControlItems.append(placeItemHtml);
                    }
                    placeControlItems.on('click', '.place-item', function(event) {
                        let idx = $(this).attr('data-idx');
                        let item = placeControlResults.items[idx];
                        DATA.map.panTo(item.position, 17);
                        if (MARKER != undefined) {
                            DATA.map.removeLayer(MARKER);
                            initDragMarker(item.position);
                        };
                        $('#' + placeControlItemsId).empty();
                    })
                }
            })
        })
    }

    function initDragMarker(coords, zoom = true) {
        coords = coords === null ? [lat, lng] : coords;
        var iconOption = L.icon({
            iconUrl: '<?= Yii::$app->homeUrl . "resources/images/marker_hcmgis.png" ?>',
            iconSize:     [32, 32],
            iconAnchor:   [16, 32],
            popupAnchor:  [0, -20]
        });

        MARKER = L.marker(coords, {
            draggable: true,
            icon: iconOption
        }).bindPopup('<p>Move the marker or manually enter in the <b>Lat</b> and <b>Lng</b> below to update your image coordinates</p>');
        MARKER.addTo(DATA.map);
        DATA.map.setView(coords, zoom ? 12 : 6);
        initBindingMarkerAndGeometryInput();
    }

    function initClickToMapEvent() {
        DATA.map.on('click', function(e) {
            if (MARKER != undefined) {
                DATA.map.removeLayer(MARKER);
                initDragMarker(e.latlng, false);
            };
        })
    }

    function onBlurLatLng() {
        let lat = $('#geom_lat').val();
        let lng = $('#geom_lng').val();

        initDragMarker([lat, lng]);
    }

    function initBindingMarkerAndGeometryInput() {
        var inlat = $('#geom_lat');
        var inlng = $('#geom_lng');

        var latlng = MARKER.getLatLng();
        inlat.val(latlng.lat);
        inlng.val(latlng.lng);

        inlat.on('change', function() {
            MARKER.setLatLng([inlat.val(), inlng.val()]);
        });

        inlng.on('change', function() {
            MARKER.setLatLng([inlat.val(), inlng.val()]);
        })

        MARKER.on('dragend', function(e) {
            var latlng = e.target._latlng;
            inlat.val(latlng.lat);
            inlng.val(latlng.lng);
        })
    }
</script>