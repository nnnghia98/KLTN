<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 04-Mar-19
 * Time: 2:55 PM
 */
?>

<style>
    .page-footer {
        background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('<?= Yii::$app->homeUrl . 'resources/images/visit-header.jpg' ?>');
        color: #fff !important;
    }

    .page-footer a {
        color: #fff;
    }
</style>
<footer class="page-footer navbar-light pt-4">
    <div class="container text-center text-md-left">
        <div class="row">
            <div class="col-md-3 mx-auto">
                <h4 class="font-weight-bold mt-3 mb-4"><a href="<?= Yii::$app->homeUrl ?>">Travel Sharing</a></h4>
                <ul class="list-unstyled list-inline">
                    <li class="list-inline-item">
                        <a class="btn-floating btn-fb mx-1" href="#">
                            <i class="icon-facebook2 icon-2x"></i>
                        </a>
                    </li>
                    <li class="list-inline-item">
                        <a class="btn-floating btn-you mx-1" href="#">
                            <i class="icon-youtube icon-2x"></i>
                        </a>
                    </li>
                </ul>
            </div>

            <hr class="clearfix w-100 d-md-none">

            <div class="col-md-3 mx-auto">
                <h4 class="font-weight-bold mt-3 mb-4">Danh mục</h4>
                <ul class="list-unstyled">
                    <li>
                        <h5 class="mb-0"><a href="<?= Yii::$app->homeUrl . 'app/destination' ?>">Điểm đến</a></h5>
                    </li>
                    <li>
                        <h5 class="mb-0"><a href="<?= Yii::$app->homeUrl . 'app/place/visit' ?>" target="_blank">Tham quan</a></h5>
                    </li>
                    <li>
                        <h5 class="mb-0"><a href="<?= Yii::$app->homeUrl . 'app/place/food' ?>" target="_blank">Ăn uống</a></h5>
                    </li>
                    <li>
                        <h5 class="mb-0"><a href="<?= Yii::$app->homeUrl . 'app/place/rest' ?>" target="_blank">Nghỉ ngơi</a></h5>
                    </li>
                    <li>
                        <h5 class="mb-0"><a href="<?= Yii::$app->homeUrl . 'app/plan' ?>" target="_blank">Lịch trình</a></h5>
                    </li>
                    <li>
                        <h5 class="mb-0"><a href="<?= Yii::$app->homeUrl . 'app/plan/create' ?>" target="_blank">Tạo lịch trình</a></h5>
                    </li>
                </ul>
            </div>

            <hr class="clearfix w-100 d-md-none">

            <div class="col-md-3 mx-auto">
                <h4 class="font-weight-bold mt-3 mb-4">Contact</h4>
                <ul class="list-unstyled">
                    <li>
                        <h5 class="mb-0"><i class="icon-location4 mr-2"></i>Địa chỉ Lĩnh Nam - Hoàng Mai - Hà Nội</h5>
                    </li>
                    <li>
                        <h5 class="mb-0"><i class="icon-phone2 mr-2"></i>09xx xxx xxx</h5>
                    </li>
                    <li>
                        <h5 class="mb-0"><i class="icon-mail5 mr-2"></i>emailxxx@gmail.com</h5>
                    </li>
                </ul>
            </div>

            <hr class="clearfix w-100 d-md-none">

            <div class="col-md-3 mx-auto">
                <h4 class="font-weight-bold text-uppercase mt-3 mb-4"></h4>
                <div class="widget widget-map">
                    <img src="<?= Yii::$app->homeUrl ?>resources/images/bg-ft.png" class="w-100" />
                </div>
            </div>
        </div>
    </div>
    <hr class="mt-2 mb-0">
    <div class="footer-copyright text-center py-3"><h5 class="mb-0 font-weight-bold">© 2020 <a href="https://hcmgis.vn/">Travel Sharing</a></h5></div>

</footer>
