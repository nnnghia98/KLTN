<?php

/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 04-Mar-19
 * Time: 2:54 PM
 */
?>
<?php

use app\modules\app\APPConfig;
use app\modules\cms\CMSConfig;
use app\modules\cms\services\AuthService;
?>

<style>
    .navbar {
        background: rgba(0, 0, 0, .4);
        position: absolute;
        top: 0;
        width: 100%;
        color: #fff;
        padding: .7rem 0;
        transition: .5s all ease;
    }

    .navbar-brand img {
        height: 3.5rem;
        transition: .5s all ease;
    }

    .navbar-brand img:hover {
        opacity: .8;
    }

    .navbar-nav-link {
        color: #333 !important;
        text-transform: uppercase;
        transition: .5s all ease;
    }

    .navbar-toggler {
        padding: .3125rem;
        border-radius: .1875rem;
        background: rgba(0, 0, 0, .5);
        color: #fff !important;
    }

    .sticky {
        position: fixed;
        background: #5c6bc0; /**#414852 */
    }

    @media (min-width: 768px) {
        .navbar-nav-link {
            color: #fff !important;
        }

        #navbar-mobile {
            background: transparent !important;
        }
    }
</style>

<div class="navbar navbar-expand-md navbar-light navbar-static wow animate fadeInDown">
    <div class="container">
        <div class="navbar-brand p-2 wmin-250">
            <a class="sitename d-flex align-items-center" href="<?= Yii::$app->homeUrl ?>">
                <img src="<?= Yii::$app->homeUrl . 'resources/images/logo.png' ?>" alt="">
            </a>
        </div>
        <div class="p-2">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-mobile" aria-expanded="true" id="navbar-toggle">
                <i class="icon-menu7"></i>
            </button>
        </div>
        <div class="collapse navbar-collapse bg-white p-2 mx-0" id="navbar-mobile">
            <ul class="navbar-nav main-menu">
                <li class="nav-item">
                    <a href="<?= Yii::$app->homeUrl . 'app/destination' ?>" class="navbar-nav-link">
                        <h6 class="mb-0 font-weight-bold">Điểm đến</h6>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a href="#" class="navbar-nav-link d-flex align-items-center dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <h6 class="mb-0 font-weight-bold">Địa điểm</h6>
                    </a>
                    
                    <div class="dropdown-menu dropdown-menu-right">
                        <a href="<?= Yii::$app->homeUrl . 'app/place/visit' ?>" class="dropdown-item"><i class="icon-camera"></i> Tham quan</a>
                        <div class="dropdown-divider"></div>
                        <a href="<?= Yii::$app->homeUrl . 'app/place/food' ?>" class="dropdown-item"><i class="icon-cup2"></i> Ăn uống</a>
                        <div class="dropdown-divider"></div>
                        <a href="<?= Yii::$app->homeUrl . 'app/place/rest' ?>" class="dropdown-item"><i class="icon-bed2"></i> Nghỉ ngơi</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a href="<?= Yii::$app->homeUrl . 'app/plan' ?>" class="navbar-nav-link">
                        <h6 class="mb-0 font-weight-bold">Lịch trình</h6>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= Yii::$app->homeUrl . 'app/plan/create' ?>" class="navbar-nav-link">
                        <h6 class="mb-0 font-weight-bold">LET'S GO</h6>
                    </a>
                </li>
                <?php if (AuthService::IsAdmin()) : ?>
                <li class="nav-item">
                    <a href="<?= CMSConfig::getUrl('user') ?>" class="navbar-nav-link">
                        <h6 class="mb-0 font-weight-bold">Admin</h6>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav ml-md-auto">
                <?php if (Yii::$app->user->isGuest) : ?>
                    <li class="nav-item">
                        <a href="<?= Yii::$app->homeUrl . 'site/login' ?>" class="navbar-nav-link">
                            <h6 class="mb-0 font-weight-bold">Đăng nhập</h6>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= Yii::$app->homeUrl . 'site/register' ?>" class="navbar-nav-link">
                            <h6 class="mb-0 font-weight-bold">Đăng ký</h6>
                        </a>
                    </li>
                <?php else : ?>
                    <li class="nav-item dropdown dropdown-user">
                        <a href="#" class="navbar-nav-link d-flex align-items-center dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <h6 class="mb-0 font-weight-bold"><?= AuthService::UserFullName() ?></h6>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" style="z-index: 102">
                            <a href="<?= APPConfig::getUrl('user/my-profile') ?>" class="dropdown-item"><i class="icon-user"></i> Trang cá nhân</a>
                            <div class="dropdown-divider my-0"></div>
                            <a href="<?= APPConfig::getUrl('user/my-plan') ?>" class="dropdown-item"><i class="icon-paperplane"></i> Lịch trình của tôi</a>
                            <div class="dropdown-divider my-0"></div>
                            <a href="<?= Yii::$app->homeUrl ?>site/logout" class="dropdown-item"><i class="icon-switch2"></i> Đăng xuất</a>
                        </div>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>