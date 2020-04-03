<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 04-Mar-19
 * Time: 2:55 PM
 */
?>
<footer class="page-footer navbar-light pt-4">
    <div class="container text-center text-md-left">
        <div class="row">
            <div class="col-md-3 mx-auto">
                <h5 class="font-weight-bold mt-3 mb-4"><a href="<?= Yii::$app->homeUrl ?>">Travel Sharing</a></h5>
                <ul class="list-unstyled list-inline">
                    <li class="list-inline-item">
                        <a class="btn-floating btn-fb mx-1" href="#">
                            <i class="icon-facebook2"></i>
                        </a>
                    </li>
                    <li class="list-inline-item">
                        <a class="btn-floating btn-tw mx-1 text-info" href="#">
                            <i class="icon-twitter2"></i>
                        </a>
                    </li>
                    <li class="list-inline-item">
                        <a class="btn-floating btn-you mx-1 text-danger" href="#">
                            <i class="icon-youtube"></i>
                        </a>
                    </li>
                </ul>
            </div>

            <hr class="clearfix w-100 d-md-none">

            <div class="col-md-3 mx-auto">
                <h5 class="font-weight-bold mt-3 mb-4">Danh mục</h5>
                <ul class="list-unstyled">
                    <li>
                        <a href="<?= Yii::$app->homeUrl . 'app/destination' ?>">Điểm đến</a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->homeUrl . 'app/place/visit' ?>" target="_blank">Tham quan</a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->homeUrl . 'app/place/food' ?>" target="_blank">Ăn uống</a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->homeUrl . 'app/place/rest' ?>" target="_blank">Nghỉ ngơi</a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->homeUrl . 'app/plan' ?>" target="_blank">Lịch trình</a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->homeUrl . 'app/plan/create' ?>" target="_blank">Tạo lịch trình</a>
                    </li>
                </ul>
            </div>

            <hr class="clearfix w-100 d-md-none">

            <div class="col-md-3 mx-auto">
                <h5 class="font-weight-bold mt-3 mb-4">Contact</h5>
                <ul class="list-unstyled">
                    <li>
                        <i class="icon-location4 mr-2"></i>Địa chỉ xxx
                    </li>
                    <li>
                        <i class="icon-phone2 mr-2"></i>09xx xxx xxx
                    </li>
                    <li>
                        <i class="icon-mail5 mr-2"></i>emailxxx@gmail.com
                    </li>
                </ul>
            </div>

            <hr class="clearfix w-100 d-md-none">

            <div class="col-md-3 mx-auto">
                <h5 class="font-weight-bold text-uppercase mt-3 mb-4"></h5>
                    <div class="widget widget-map"><img src="<?= Yii::$app->homeUrl ?>resources/images/bg-ft.png" alt="" /></div>
            </div>
        </div>
    </div>
    <hr class="mt-2 mb-0">
    <div class="footer-copyright text-center py-3">© 2020 <a href="https://hcmgis.vn/">Travel Sharing</a></div>

</footer>
