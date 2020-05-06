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
                        <h5 class="caption-label text-center">DU LỊCH VÀ CHIA SẺ</h5>
                        <a href="<?= APPConfig::getUrl('plan/create') ?>" class="btn bg-pink-400 btn-lg rounded-round font-weight-bold">
                            Lên lịch trình <i class="icon-plus2 ml-2"></i>
                        </a>
                    </div>
                </div>
                <div class="carousel-item">
                    <img class="d-block w-100" src="<?= Yii::$app->homeUrl . 'resources/images/slide1.jpg' ?>">
                    <div class="carousel-caption-custom">
                        <h5 class="caption-label text-center">HÀNG NGHÌN ĐIỂM DU LỊCH TRÊN TOÀN QUỐC</h5>
                        <a href="<?= APPConfig::getUrl('place/visit') ?>" class="btn bg-pink-400 btn-lg rounded-round font-weight-bold">
                            Khám phá ngay <i class="icon-paperplane ml-2"></i>
                        </a>
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

    <div class="homage-page-content my-3">
        <div class="destination-wrap homepage-section">
            <div class="container">
                <div class="section-header">
                    <h1 class="text-center text-uppercase font-weith-bold">Điểm đến nổi bật</h1>
                </div>
            </div>
        </div>
        <div class="plan-introduction-wrap homepage-section">
            <div class="w-100 h-100 d-flex justify-content-center align-items-center flex-column content">
                <h1 class="text-white text-uppercase font-weight-bold" style="font-size: 2rem;">Bắt đầu chuyến du lịch của bạn</h1>
                <h3 class="text-white">Hãy xây dựng một lịch trình du lịch chi tiết của riêng bạn và chia sẻ tới mọi người ngay nào!</h3>
                <a href="<?= APPConfig::getUrl('plan/create') ?>" class="btn btn-outline bg-white border-white text-white btn-lg rounded-round font-weight-bold mt-3">
                    Lên lịch trình <i class="icon-plus2 ml-2"></i>
                </a>
            </div>
        </div>
        <div class="plan-wrap homepage-section">
            <div class="container">
                <div class="section-header">
                    <h1 class="text-center text-uppercase font-weith-bold">Lịch trình được tạo gần đây</h1>
                </div>
            </div>
        </div>
        <div class="feature-wrap homepage-section">
            <div class="container">
                <div class="row feature-list">
                    <div class="col-md-3 feature-item">
                        <div class="feature-icon">
                            <img src="<?= Yii::$app->homeUrl . 'resources/images/beach.png' ?>" width="64" height="64">
                        </div>
                        <div class="feature-content mt-3">
                            <h3>Địa điểm phong phú</h3>
                            <p>Hàng nghìn điểm đến và địa điểm du lịch trong và ngoài nước được chúng tôi cập nhật thường xuyên</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="feature-icon">
                            <img src="<?= Yii::$app->homeUrl . 'resources/images/planning.png' ?>" width="64" height="64">
                        </div>
                        <div class="feature-content mt-3">
                            <h3>Lịch trình cụ thể</h3>
                            <p>Lịch trình du lịch được xây dựng cụ thể tại từng điểm dừng chân: Thời gian bắt đầu, lưu trú, di chuyển, phương tiện di chuyển, ...</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="feature-icon">
                            <img src="<?= Yii::$app->homeUrl . 'resources/images/map.png' ?>" width="64" height="64">
                        </div>
                        <div class="feature-content mt-3">
                            <h3>Bản đồ chi tiết</h3>
                            <p>Giao diện bản đồ mô tả chi tiết lịch trình di chuyển theo từng ngày bao gồm địa điểm và tuyến đường</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="feature-icon">
                            <img src="<?= Yii::$app->homeUrl . 'resources/images/share.png' ?>" width="64" height="64">
                        </div>
                        <div class="feature-content mt-3">
                            <h3>Kết nối cộng đồng</h3>
                            <p>Cùng chia sẻ lịch trình và kinh nghiệm du lịch tại từng điểm đến của bạn cho cộng đồng</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="place-wrap homepage-section">
            <div class="container">
                <div class="section-header">
                    <h1 class="text-center text-uppercase font-weith-bold">Địa điểm được quan tâm</h1>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        // $('#travel-sharing-slider').carousel('pause')
    })
</script>