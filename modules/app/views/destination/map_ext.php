<style>
    footer {
        display: none;
    }

    .map-sidebar {
        position: fixed;
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

    
    function fixMapHeight() {
        var navbarHeight = $('.navbar').height()
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