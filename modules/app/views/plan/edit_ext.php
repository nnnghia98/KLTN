<style>
    .plan-detail-wrap {
        display: flex;
        overflow-x: scroll;
        min-height: 400px;
    }

    .date-item-wrap {
        width: 350px;
        min-width: 350px;
        margin-right: 1.25rem
    }

    .btn-add-place {
        padding: .625rem;
        border: 2px dashed var(--main-color-pink);
        border-radius: .625rem;
        color: var(--main-color-pink);
        cursor: pointer;
        transition: .5s all ease;
    }

    .btn-add-place:hover {
        background: #ddd;
    }

    .move-to-next-place {
        display: flex;
        align-items: center;
        position: relative;
        margin: 0 1.3rem;
        border-left: 2px dashed #777;
        height: 75px;
    }

    .move-type-wrap {
        position: absolute;
        top: 50%;
        left: -10px;
        margin: .1875rem 0;
        background-color: #eeeded;
    }

    .move-type-wrap .move-type::before {
        top: 0;
        transform: translate(-100%, -100%);
    }

    .move-type-wrap .move-type::after {
        bottom: 0;
        transform: translate(-100%, 100%);
    }

    .time-free-card {
        position: absolute;
        bottom: -.3125rem;
        right: 0;
        transform: translateY(100%);
        z-index: 3;
    }

    .map-preview {
        position: fixed;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        background: #eeeded;
        transition: .5s all ease;
        z-index: 101;
    }
</style>
<script>
    var DATA = {
        map: null,
        layers: {
            base: {},
            overlay: {}
        },
        controls: {}
    }

    var baseLayers = [
        {
            domain: 'http://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}',
            minZoom: 0,
            maxZoom: 22,
            attribution: 'Google Maps'
        }, {
            domain: 'http://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}',
            minZoom: 0,
            maxZoom: 22,
            attribution: 'Google Satellite'
        }, {
            domain: 'http://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}',
            minZoom: 0,
            maxZoom: 22,
            attribution: 'Google Satellite Hybrid'
        }, {
            domain: 'http://server.arcgisonline.com/arcgis/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}',
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            minZoom: 0,
            maxZoom: 22,
            attribution: 'Esri Street'
        }
    ]

    function initMap() {
        DATA.map = L.map('map-preview', {
            zoomMin: 0, 
            zoomMax: 20
        }).setView([16.047079, 108.206230], 6)
        initBaseLayer()
    }

    function initBaseLayer() {
        baseLayers.forEach(function(el, idx) {
            DATA.layers.base[el.attribution] = L.tileLayer.wms(el.domain, el)
            if (idx === 0) {
                DATA.map.addLayer(DATA.layers.base[el.attribution])
            }
        })

        DATA.controls.controllayer = L.control.layers(DATA.layers.base)
        DATA.controls.controllayer.addTo(DATA.map)
    }

    function initOverlayLayer() {
        DATA.layers.overlay['place'] = L.featureGroup().addTo(DATA.map)
        DATA.layers.overlay['plan'] = L.featureGroup().addTo(DATA.map)
    }

    function drawPlan(geojson) {
        if(DATA.map.hasLayer(DATA.layers.overlay['plan'])) {
            DATA.layers.overlay['plan'].clearLayers()
        }

    }

    function drawPlaces(places) {
        if(DATA.map.hasLayer(DATA.layers.overlay['place'])) {
            DATA.layers.overlay['place'].clearLayers()
        }

        var markers = [], icon, marker, bounds
        places.forEach(item => {
            icon = customIcon()
            marker = L.marker([item.lat, item.lng], {icon: icon})
            marker.ID = item.id
            DATA.layers.overlay['place'].addLayer(marker)
        })

        bounds = DATA.layers.overlay['place'].getBounds()
        DATA.map.fitBounds(bounds, {padding: [50, 50]})
    }

    function customIcon() {
        return L.icon({
            iconUrl: '<?= Yii::$app->homeUrl . 'resources/images/marker-plan.png' ?>',
            iconSize: [40, 40],
            iconAnchor: [20, 40],
            popupAnchor: [0, -30]
        });
    }
</script>