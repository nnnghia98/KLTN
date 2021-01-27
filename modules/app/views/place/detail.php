<?php

use app\modules\app\APPConfig;
use app\modules\app\PathConfig;
use app\modules\cms\services\PlaceService;
use app\modules\cms\widgets\CMSMapDetailWidget;
use app\modules\contrib\gxassets\GxLeafletAsset;
use app\modules\contrib\gxassets\GxVueperSlidesAsset;

GxLeafletAsset::register($this);
GxVueperSlidesAsset::register($this);
$map = [
    1 => 'visit',
    2 => 'food',
    3 => 'rest',
];

$pageData = [
    'pageTitle' => $place['name'],
    'pageBreadcrumb' => [
        ['Điểm đến', APPConfig::getUrl('place/' . $map[$place['place_type_id']])],
        [$place['name']]
    ],
    'backgoundHeader' => Yii::$app->homeUrl . 'resources/images/' . $map[$place['place_type_id']] . '-header.jpg'
];
?>
<?= $this->render(PathConfig::getAppViewPath('pageHeader'), $pageData); ?>
<style>
    .carousel-indicators {
        bottom: -1rem;
        transform: translateY(100%);
        overflow-x: scroll;
        overflow-y: hidden;
        justify-content: start;
        margin: 0;
    }

    .carousel-indicators::-webkit-scrollbar {
        width: 0;
        height: 7px;
    }

    .carousel-indicators li {
        text-indent: unset;
        width: unset;
        height: unset;
    }

    .carousel-indicators li:not(.active) img {
        opacity: 0.5;
    }
</style>
<div class="container my-5" id="place-detail-page">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" v-cloak>Ảnh về {{ place.name }}</h3>
                </div>
                <div class="card-body">
                    <div style="padding-bottom: 70px">
                        <div id="carousel-thumb" class="carousel slide carousel-fade carousel-thumbnails" data-ride="carousel">
                            <div class="carousel-inner h-100" role="listbox">
                                <div class="carousel-item h-100" v-for="(path, idx) in images" :class="idx == 0 ? 'active' : ''">
                                    <img class="d-block w-100 h-100" :src="'<?= Yii::$app->homeUrl . 'uploads/' ?>' + path" style="object-fit: cover">
                                </div>
                            </div>
                            <a class="carousel-control-prev" href="#carousel-thumb" role="button" data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="carousel-control-next" href="#carousel-thumb" role="button" data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Next</span>
                            </a>
                            <ol class="carousel-indicators">
                                <li data-target="#carousel-thumb" v-for="(path, idx) in images" :class="idx == 0 ? 'active' : ''" :data-slide-to="idx">
                                    <img :src="'<?= Yii::$app->homeUrl . 'uploads/' ?>' + path" width="75" height="50">
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" v-cloak>Giới thiệu về {{ place.name }}</h3>
                </div>
                <div class="card-body">
                    <div v-html="place.description"></div>
                </div>
            </div>
            <div class="places-plan-wrap mt-5" v-cloak>
                <div class="top-places top-visit mb-4" v-if="visits.length > 0">
                    <h3 class="card-title">Điểm tham quan lân cận</h3>
                    <div class="row">
                        <place-in-row v-for="place in visits" :place="place" :col="4" :key="place.id"></place-in-row>
                    </div>
                </div>

                <div class="top-places top-food mb-4" v-if="foods.length > 0">
                    <h3 class="card-title">Điểm ăn uống lân cận</h3>
                    <div class="row">
                        <place-in-row v-for="place in foods" :place="place" :col="4" :key="place.id"></place-in-row>
                    </div>
                </div>

                <div class="top-places top-rest mb-4" v-if="rests.length > 0">
                    <h3 class="card-title">Điểm nghỉ ngơi lân cận</h3>
                    <div class="row">
                        <place-in-row v-for="place in rests" :place="place" :col="4" :key="place.id"></place-in-row>
                    </div>
                </div>

                <div class="newest-plan mb-4" v-if="plans.length > 0">
                    <h3 class="card-title">Lịch trình liên quan</h3>
                    <div class="row">
                        <plan-in-row v-for="plan in plans" :plan="plan" :col="4" :key="plan.id"></plan-in-row>
                    </div>
                </div>
            </div>
            <div class="card comment-wrap">
                <div class="card-header">
                    <h3 class="card-title" v-cloak>Bình luận ({{ place.count_comment }})</h3>
                </div>
                <?php if (!Yii::$app->user->isGuest) : ?>
                    <div class="card-body" v-if="interactive">
                        <div class="form-group d-flex align-items-center">
                            <span class="mr-2">Đánh giá: </span>
                            <rating :star="interactive.star" @change="interactive.star = $event" :key="interactive.star"></rating>
                        </div>
                        <div class="form-group">
                            <textarea id="comment-text" class="form-control" rows="5" v-model="interactive.comment"></textarea>
                        </div>
                        <div class="form-group text-right mb-0">
                            <button class="btn bg-pink-400 rounded-round" @click="submitComment">Bình luận</button>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="card-body">
                    <comment-list :comments="comments" :key="comments.length"></comment-list>
                    <div class="loadmore d-flex justify-content-center" v-if="comments.length < place.count_comment" v-cloak>
                        <button class="btn btn-outline bg-pink-400 border-pink-400 text-pink-400 rounded-round" @click="getPlaceComments(++comment_page)">Xem thêm</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-body">
                <a :href="'<?= APPConfig::getUrl('plan/create?desination=') ?>' + place.destination_id" class="btn bg-pink-400 rounded-round">
                    Lên lịch trình <i class="icon-plus2 ml-2"></i>
                </a>
            </div>
            <div class="card card-body">
                <h3 class="place-title font-weight-bold">
                    {{ place.name }}
                </h3>
                <p class="place-subtitle">
                    {{ place.subtitle }}
                </p>
                <p>
                    <rating-star-static :rating="place.avg_rating" :key="place.slug"></rating-star-static>
                </p>
                <hr>
                <div class="counter">
                    <h6 class="d-flex justify-content-between"><span><i class="icon-star-full2 mr-2"></i>Đánh giá:</span><span>{{ place.count_rating + ' lượt' }}</span></h6>
                    <h6 class="d-flex justify-content-between"><span><i class="icon-comment mr-2"></i>Bình luận:</span><span>{{ place.count_comment + ' lượt' }}</span></h6>
                </div>
            </div>
            <div class="card overflow-hidden" style="height: 300px;">
                <?= CMSMapDetailWidget::widget(['lat' => $place['lat'], 'lng' => $place['lng']]) ?>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        var place = <?= json_encode($place, true) ?>;
        var vm = new Vue({
            el: '#place-detail-page',
            data: {
                place: place,
                images: [],
                visits: [],
                foods: [],
                rests: [],
                plans: [],
                comments: [],
                comment_page: 1,
                interactive: {}
            },
            created: function() {
                this.getPlaceImages()
                this.getNearbyVisitPlaces()
                this.getNearbyFoodPlaces()
                this.getNearbyRestPlaces()
                this.getRelatePlans()
                this.getPlaceComments()
                this.getCurrentUserInteractive()
            },
            methods: {
                getPlaceImages: function() {
                    var _this = this
                    var api = '<?= APPConfig::getUrl('place/get-images') ?>' + `?id=${this.place.id}`

                    sendAjax(api, {}, 'GET', (resp) => {
                        if (resp.status) {
                            _this.images = resp.images
                            _this.$nextTick(function() {
                                var slider = $('#carousel-thumb')
                                slider.height(slider.width() * 2 / 3)
                            })
                        }
                    })
                },

                getPlaceComments: function(page = 1) {
                    var _this = this
                    var api = '<?= APPConfig::getUrl('place/get-comments') ?>' + `?id=${this.place.id}&page=${page}`

                    sendAjax(api, {}, 'GET', (resp) => {
                        if (resp.status) {
                            _this.comments = _this.comments.concat(resp.comments)
                        }
                    })
                },

                getCurrentUserInteractive: function() {
                    var _this = this
                    var api = '<?= APPConfig::getUrl('place/get-interactive') ?>' + `?id=${this.place.id}`

                    sendAjax(api, {}, 'GET', (resp) => {
                        if (resp.status) {
                            _this.interactive = resp.interactive
                        }
                    })
                },

                getNearbyVisitPlaces: function() {
                    var _this = this
                    var api = '<?= APPConfig::getUrl('place/get-nearby-places') ?>' + `?lat=${this.place.lat}&lng=${this.place.lng}&type=` + '<?= PlaceService::$TYPE['VISIT'] ?>'

                    sendAjax(api, {}, 'GET', (resp) => {
                        if (resp.status) {
                            _this.visits = resp.places
                            _this.fixImageActionsHeight()
                        }
                    })
                },

                getNearbyFoodPlaces: function() {
                    var _this = this
                    var api = '<?= APPConfig::getUrl('place/get-nearby-places') ?>' + `?lat=${this.place.lat}&lng=${this.place.lng}&type=` + '<?= PlaceService::$TYPE['FOOD'] ?>'

                    sendAjax(api, {}, 'GET', (resp) => {
                        if (resp.status) {
                            _this.foods = resp.places
                            _this.fixImageActionsHeight()
                        }
                    })
                },

                getNearbyRestPlaces: function() {
                    var _this = this
                    var api = '<?= APPConfig::getUrl('place/get-nearby-places') ?>' + `?lat=${this.place.lat}&lng=${this.place.lng}&type=` + '<?= PlaceService::$TYPE['REST'] ?>'

                    sendAjax(api, {}, 'GET', (resp) => {
                        if (resp.status) {
                            _this.rests = resp.places
                            _this.fixImageActionsHeight()
                        }
                    })
                },

                getRelatePlans: function() {
                    var _this = this
                    var api = '<?= APPConfig::getUrl('place/get-relate-plans') ?>' + `?id=${this.place.id}`

                    sendAjax(api, {}, 'GET', (resp) => {
                        if (resp.status) {
                            _this.plans = resp.plans
                            _this.fixImageActionsHeight()
                        }
                    })
                },

                submitComment: function() {
                    var _this = this
                    var api = '<?= APPConfig::getUrl('place/submit-comment') ?>' + `?id=${this.place.id}`,
                        data = {
                            id: this.place.id,
                            star: this.interactive.star,
                            comment: this.interactive.comment,
                        }

                    sendAjax(api, data, 'POST', (resp) => {
                        if (resp.status) {
                            window.location.reload()
                        } else {
                            toastMessage('error', resp.message)
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