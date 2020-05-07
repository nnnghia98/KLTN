<?php

use app\modules\app\APPConfig;

include('site_ext.php')
?>

<div class="homepage" id="homepage">
    <div class="header-slider">
        <div id="travel-sharing-slider" class="carousel slide" data-ride="carousel" data-pause="true">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img class="d-block w-100" src="<?= Yii::$app->homeUrl . 'resources/images/slide2.jpg' ?>">
                    <div class="carousel-caption-custom">
                        <div class="wow animate fadeInDown d-flex flex-column justify-content-center align-items-center">
                            <h5 class="caption-label text-center">DU LỊCH VÀ CHIA SẺ</h5>
                            <a href="<?= APPConfig::getUrl('plan/create') ?>" class="btn bg-pink-400 btn-lg rounded-round font-weight-bold">
                                Lên lịch trình <i class="icon-plus2 ml-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <img class="d-block w-100" src="<?= Yii::$app->homeUrl . 'resources/images/slide1.jpg' ?>">
                    <div class="carousel-caption-custom">
                        <div class="wow animate fadeInDown d-flex flex-column justify-content-center align-items-center">
                            <h5 class="caption-label text-center">HÀNG NGHÌN ĐIỂM DU LỊCH TRÊN TOÀN QUỐC</h5>
                            <a href="<?= APPConfig::getUrl('place/visit') ?>" class="btn bg-pink-400 btn-lg rounded-round font-weight-bold">
                                Khám phá ngay <i class="icon-paperplane ml-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <a class="carousel-control-prev" href="#travel-sharing-slider" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#travel-sharing-slider" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
    </div>

    <div class="homage-page-content my-3 overflow-hidden">
        <div class="destination-wrap homepage-section wow animate fadeInUp">
            <div class="container">
                <div class="section-header">
                    <h1 class="text-center text-uppercase font-weith-bold">Điểm đến nổi bật</h1>
                </div>
                <div class="section-content mt-4">
                    <div class="row">
                        <div class="col-md-4" v-for="d in destinations" v-cloak>
                            <div class="card overflow-hidden">
                                <div class="card-image">
                                    <img :src="'<?= Yii::$app->homeUrl . 'uploads/' ?>' + d.thumbnail" class="w-100 h-auto" :alt="'travel sharing ' + d.name">
                                    <div class="card-image-overlay p-2">
                                        <a :href="'<?= APPConfig::getUrl('destination/detail/') ?>' + d.slug">
                                            <h2 class="destination-title ellipsis-1">{{ d.name }}</h2>
                                            <h5 class="destination-subtitle ellipsis-1">{{ d.subtitle }}</h5>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="plan-introduction-wrap homepage-section wow animate fadeInUp">
            <div class="w-100 h-100 d-flex justify-content-center align-items-center flex-column content">
                <h1 class="text-white text-uppercase font-weight-bold text-center" style="font-size: 2rem;">Bắt đầu chuyến du lịch của bạn</h1>
                <h3 class="text-white text-center">Hãy xây dựng một lịch trình du lịch chi tiết của riêng bạn và chia sẻ tới mọi người ngay nào!</h3>
                <a href="<?= APPConfig::getUrl('plan/create') ?>" class="btn btn-outline bg-white border-white text-white btn-lg rounded-round font-weight-bold mt-3">
                    Lên lịch trình <i class="icon-plus2 ml-2"></i>
                </a>
            </div>
        </div>
        <div class="plan-wrap homepage-section wow animate fadeInUp">
            <div class="container">
                <div class="section-header">
                    <h1 class="text-center text-uppercase font-weith-bold">Lịch trình được tạo gần đây</h1>
                </div>
                <div class="section-content mt-4">
                    <div class="row">
                        <div class="col-md-3" v-for="p in plans" v-cloak>
                            <div class="card overflow-hidden">
                                <div class="card-img-actions overflow-hidden">
                                    <img class="card-img img-fluid w-100 h-auto" :src="'<?= Yii::$app->homeUrl . 'uploads/' ?>' + p.thumbnail" :alt="'travel sharing ' + p.name">
                                </div>
                                <div class="p-2 mt-1">
                                    <div class="d-flex align-items-start flex-nowrap">
                                        <div>
                                            <a :href="'<?= APPConfig::getUrl('plan/detail/') ?>' + p.slug">
                                                <h4 class="font-weight-semibold">{{ p.name }}</h4>
                                            </a>
                                            <div>
                                                <img :src="p.author_avatar ? '<?= Yii::$app->homeUrl . 'uploads/' ?>' + p.author_avatar : '<?= Yii::$app->homeUrl . 'resources/images/no_avatar.jpg' ?>'"
                                                    class="mr-1 rounded-circle" width="35" height="35">
                                                <a :href="'<?= APPConfig::getUrl('user/') ?>' + p.author_slug">{{ p.author }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="feature-wrap homepage-section wow animate fadeInUp">
            <div class="container">
                <div class="row feature-list">
                    <div class="col-md-3 feature-item d-flex flex-column justify-content-start align-items-center align-items-md-start mt-4 mt-md-0">
                        <div class="feature-icon">
                            <img src="<?= Yii::$app->homeUrl . 'resources/images/beach.png' ?>" width="64" height="64">
                        </div>
                        <div class="feature-content mt-3">
                            <h3 class="text-center text-md-left">Địa điểm phong phú</h3>
                            <p class="text-center text-md-left">Hàng nghìn điểm đến và địa điểm du lịch trong và ngoài nước được chúng tôi cập nhật thường xuyên</p>
                        </div>
                    </div>
                    <div class="col-md-3 feature-item d-flex flex-column justify-content-start align-items-center align-items-md-start mt-4 mt-md-0">
                        <div class="feature-icon">
                            <img src="<?= Yii::$app->homeUrl . 'resources/images/planning.png' ?>" width="64" height="64">
                        </div>
                        <div class="feature-content mt-3">
                            <h3 class="text-center text-md-left">Lịch trình cụ thể</h3>
                            <p class="text-center text-md-left">Lịch trình du lịch được xây dựng cụ thể tại từng điểm dừng chân: Thời gian bắt đầu, lưu trú, di chuyển, phương tiện di chuyển, ...</p>
                        </div>
                    </div>
                    <div class="col-md-3 feature-item d-flex flex-column justify-content-start align-items-center align-items-md-start mt-4 mt-md-0">
                        <div class="feature-icon">
                            <img src="<?= Yii::$app->homeUrl . 'resources/images/map.png' ?>" width="64" height="64">
                        </div>
                        <div class="feature-content mt-3">
                            <h3 class="text-center text-md-left">Bản đồ chi tiết</h3>
                            <p class="text-center text-md-left">Giao diện bản đồ mô tả chi tiết lịch trình di chuyển theo từng ngày bao gồm địa điểm và tuyến đường</p>
                        </div>
                    </div>
                    <div class="col-md-3 feature-item d-flex flex-column justify-content-start align-items-center align-items-md-start mt-4 mt-md-0">
                        <div class="feature-icon">
                            <img src="<?= Yii::$app->homeUrl . 'resources/images/share.png' ?>" width="64" height="64">
                        </div>
                        <div class="feature-content mt-3">
                            <h3 class="text-center text-md-left">Kết nối cộng đồng</h3>
                            <p class="text-center text-md-left">Cùng chia sẻ lịch trình và kinh nghiệm du lịch tại từng điểm đến của bạn cho cộng đồng</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="place-wrap homepage-section wow animate fadeInUp">
            <div class="container">
                <div class="section-header">
                    <h1 class="text-center text-uppercase font-weith-bold">Địa điểm được quan tâm</h1>
                </div>
                <div class="section-content mt-4">
                    <div class="row">
                        <place-in-row v-for="place in places" :place="place" :col="3" :key="place.id"></place-in-row>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        var vm = new Vue({
            el: '#homepage',
            data: {
                destinations: null,
                places: null,
                plans: null
            },
            created: function() {
                this.getDestinations();
                this.getPlaces();
                this.getPlans();
            },
            methods: {
                getDestinations: function() {
                    var _this = this,
                        api = '<?= APPConfig::getUrl('site/get-destinations') ?>'

                    sendAjax(api, {}, 'GET', (resp) => {
                        if(resp.status) {
                            _this.destinations = resp.destinations
                            _this.fixImageActionsHeight()
                        }
                    })
                },

                getPlaces: function() {
                    var _this = this,
                        api = '<?= APPConfig::getUrl('site/get-places') ?>'

                    sendAjax(api, {}, 'GET', (resp) => {
                        if(resp.status) {
                            _this.places = resp.places
                            _this.fixImageActionsHeight()
                        }
                    })
                },

                getPlans: function() {
                    var _this = this,
                        api = '<?= APPConfig::getUrl('site/newest-plans') ?>'

                    sendAjax(api, {}, 'GET', (resp) => {
                        if(resp.status) {
                            _this.plans = resp.plans
                            _this.fixImageActionsHeight()
                        }
                    })
                },

                fixImageActionsHeight: function() {
                    this.$nextTick(function() {
                        fixImageActionsHeight()
                    })
                }
            }
        })
    })
</script>