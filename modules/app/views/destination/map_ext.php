<?php

use app\modules\app\APPConfig;

?>
<style>
    .navbar {
        position: relative;
        padding: 0;
        background: #5c6bc0;
    }

    footer {
        display: none;
    }

    .map-sidebar {
        position: absolute;
        width: 350px;
        max-width: 75%;
        z-index: 1;
        top: 0;
        left: 0;
        bottom: 0;
        box-shadow: 1px 1px 3px #aaa;
        transition: .3s all ease;
    }

    .map-sidebar:not(.show) {
        width: 0px;
    }

    .map-sidebar:not(.show) .sidebar-toggle i {
        transform: rotate(180deg);
    }

        .sidebar-toggle {
            right: 0;
            transform: translateX(100%);
            box-shadow: 1px 1px 3px #aaa;
            border-top-right-radius: .3125rem;
            border-bottom-right-radius: .3125rem;
            transition: .3s all ease;
        }

            .sidebar-toggle:hover {
                background: #f0f0f0 !important;
            }
    #map {
        z-index: 0;
    }
    @media only screen and (min-width: 768px) {
        .map-sidebar {
            position: static;
        }
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
        DATA.map = L.map('map').setView([16.047079, 108.206230], 6)
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

    function initLayer(data) {
        removeLayerExist()

        var markers = [], icon, marker
        data.forEach(item => {
            icon = customIcon()
            marker = L.marker([item.lat, item.lng], {icon: icon}).bindPopup(markerPopup(item))
            marker.ID = item.id
            DATA.layers.overlay['destination'].addLayer(marker)
        })
    }

    function removeLayerExist() {
        if(DATA.map.hasLayer(DATA.layers.overlay['destination'])) {
            DATA.layers.overlay['destination'].eachLayer(function(layer) {
                DATA.layers.overlay['destination'].removeLayer(layer)
            })
        } else {
            DATA.layers.overlay['destination'] = L.featureGroup().addTo(DATA.map)
        }
    }

    function markerPopup(target) {
        var html = '<div class="destination-popup d-flex flex-column align-items-center">' +
            '<h4 class="font-weight-bold text-indigo-400 text-center">' +
                '<a href="<?= APPConfig::getUrl('destination/detail/') ?>' + target.slug + '">' + target.name + '</a>' +
            '</h4>' +
            '<h6 class="text-muted text-center">' + target.subtitle + '</h6>' +
            '<div class="destination-thumbnail overflow-hidden" style="border-radius: .625rem">' +
                '<img src="<?= Yii::$app->homeUrl . 'uploads/' ?>' + target.thumbnail + '" width="225" height="150">' +
            '</div>'
        '</div>'

        return html
    }

    function customIcon() {
        return L.icon({
            iconUrl: '<?= Yii::$app->homeUrl . 'resources/images/marker-destination.png' ?>',
            iconSize: [40, 40],
            iconAnchor: [20, 40],
            popupAnchor: [0, -30]
        });
    }
    
    function fixMapHeight() {
        var navbar = $('.navbar')
        var navbarHeight = navbar.outerHeight()
        var windowHeight = $(window).height()
        $('.map-page').height(windowHeight - navbarHeight)
    }

    function toggleSidebar() {
        var sidebar = $('#map-sidebar')
        if(sidebar.hasClass('show')) {
            sidebar.removeClass('show')
        } else {
            sidebar.addClass('show')
        }

        setTimeout(function () {
            DATA.map.invalidateSize();
        }, 300)
    }
</script>