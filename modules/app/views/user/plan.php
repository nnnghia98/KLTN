<?php

use app\modules\app\APPConfig;
use app\modules\cms\CMSConfig;
use app\modules\contrib\gxassets\GxLaddaAsset;
use app\modules\contrib\gxassets\GxVueComponentAsset;

GxVueComponentAsset::register($this);
GxLaddaAsset::register($this);
?>

<style>
    .user-avatar {
        width: 150px;
        height: 150px;
    }

    .user-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>

<div class="content user-page" id="user-pointclound-page" v-cloak>
    <div class="">
        <div class="user-pointclound-page-header d-flex justify-content-center align-items-center flex-column card card-body pb-0 mb-0">
            <div class="user-swapper py-4 d-flex align-items-center flex-column">
                <div class="user-avatar rounded-circle overflow-hidden position-relative border-2 border-primary">
                    <img :src="getAvatarPath(user.avatar)" :alt="user.fullname" style="object-fit: cover">
                </div>
                <div class="user-name text-center mt-3 mb-0">
                    <h3>{{ user.fullname }} ({{ points.length }} points)</h3>
                </div>
                <user-following :following="user.following" :userid="user.id" :fullname="user.fullname"></user-following>
            </div>
        </div>
        <div class="user-pointclound-page-body py-5">
            <div class="tab-content w-100 d-flex justify-content-center">
                <div class="tab-pane fade w-100" id="pointclound">
                    <div class="row">
                        <div class="col-md-3" v-for="point in points">
                            <div class="card">
                                <div class="card-img-actions m-1">
                                    <img class="card-img img-fluid" :src="point.thumbnail" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var user = JSON.parse('<?= json_encode($user, true) ?>')
    var points = JSON.parse('<?= json_encode($points, true) ?>')
    var vm = new Vue({
        el: '#user-pointclound-page',
        data: {
            user: user,
            points: points
        },
        methods: {
            getAvatarPath: function(avatar) {
                var path = '<?= Yii::$app->homeUrl ?>' + (avatar ? 'uploads/' + avatar : 'resources/images/no_avatar.jpg')
                return path
            }
        }
    })
</script>