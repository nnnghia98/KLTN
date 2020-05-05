Vue.component('rating-star-static', {
    props: ['rating'],
    data: function() {
        return {
            ratingFixed: parseFloat(this.rating).toFixed(1),
            percent: parseFloat(this.rating) / 5 * 100
        }
    },
    template: `
    <div class="star-rating">
        <div class="back-stars my-1">
            <i class="icon-star-full2" aria-hidden="true"></i>
            <i class="icon-star-full2" aria-hidden="true"></i>
            <i class="icon-star-full2" aria-hidden="true"></i>
            <i class="icon-star-full2" aria-hidden="true"></i>
            <i class="icon-star-full2" aria-hidden="true"></i>
            
            <div class="front-stars" :style="'width: ' + percent + '%'">
                <i class="icon-star-full2" aria-hidden="true"></i>
                <i class="icon-star-full2" aria-hidden="true"></i>
                <i class="icon-star-full2" aria-hidden="true"></i>
                <i class="icon-star-full2" aria-hidden="true"></i>
                <i class="icon-star-full2" aria-hidden="true"></i>
            </div>
        </div>
        <div class="rating-number ml-1 text-muted">({{ ratingFixed }})</div>
    </div>`
})

Vue.component('pagination', {
    props: ['current', 'pages'],
    data: function() {
        return {
            page: this.current
        }
    },
    template: `
    <ul class="pagination pagination-pager pagination-rounded justify-content-center">
        <li class="page-item first" :class="page == 1 ? 'disabled' : ''"  @click="page = 1">
            <span class="page-link">⇤</span>
        </li>
        <li class="page-item prev" :class="page == 1 ? 'disabled' : ''"  @click="page = --page < 1 ? 1 : page">
            <span class="page-link">⇠</span>
        </li>
        <li class="page-item next" :class="page == pages ? 'disabled' : ''"  @click="page = ++page > pages ? pages : page">
            <span class="page-link">⇢</span>
        </li>
        <li class="page-item last" :class="page == pages ? 'disabled' : ''" @click="page = pages">
            <span class="page-link">⇥</span>
        </li>
    </ul>`,
    watch: {
        page: function() {
            this.$emit('change', this.page);
        }
    }
})

Vue.component('pagination-summary', {
    props: ['current', 'from', 'to', 'total'],
    template: `
    <h5 class="mb-0">
        Trang {{ current }}: <b>{{ from }}</b> - <b>{{ to }}</b> trong <b>{{ total }}</b> kết quả
    </h5>`
})

Vue.component('place', {
    props: ['place'],
    data: function() {
        return {
            root: APP.root,
            type: this.place.place_type_id == 1 ? 'visit' : (this.place.place_type_id == 2 ? 'food' : 'rest')
        }
    },
    template: `
    <li>
        <div class="media">
            <div class="mr-2" style="width: 30%; max-width: 225px">
                <a :href="root + '/app/place/visit/' + place.slug" class="media-list-photo">
                    <img :src="root + '/uploads/' + place.thumbnail" :alt="place.name" class="w-100 h-auto">
                </a>
            </div>
            <div class="media-body">
                <h4 class="media-title font-weight-bold">
                    <a :href="root + '/app/place/visit/' + place.slug">{{ place.name }}</a>
                </h4>
                <h6 class="mb-0 text-muted"><i class="icon-home5 mr-1"></i>{{ place.address }}</h6>
                <rating-star-static :rating="place.avg_rating"></rating-star-static>
                <p class="text-muted"><i class="icon-comment mr-1"></i> {{ place.count_comment ? place.count_comment : 0 }}</p>
            </div>
            <div class="ml-1">
                <a :href="root + '/app/place/' + type + '-map?target=' + place.slug" class="btn btn-sm btn-icon btn-outline-primary" title="Xem trên bản đồ"><i class="icon-location4"></i></a>
            </div>
        </div>
    </li>`
})

Vue.component('place-choosen', {
    props: ['place', 'target'],
    data: function() {
        return {
            root: APP.root,
            type: this.place.place_type_id == 1 ? 'visit' : (this.place.place_type_id == 2 ? 'food' : 'rest')
        }
    },
    template: `<li>
        <div class="media">
            <div class="mr-2" style="width: 30%; max-width: 225px">
                <a :href="root + '/app/place/visit/' + place.slug" class="media-list-photo">
                    <img :src="root + '/uploads/' + place.thumbnail" :alt="place.name" class="w-100 h-auto">
                </a>
            </div>
            <div class="media-body">
                <h4 class="media-title font-weight-bold">
                    <a :href="root + '/app/place/visit/' + place.slug">{{ place.name }}</a>
                </h4>
                <h6 class="mb-0 text-muted"><i class="icon-home5 mr-1"></i>{{ place.address }}</h6>
                <rating-star-static :rating="place.avg_rating"></rating-star-static>
                <p class="text-muted"><i class="icon-comment mr-1"></i> {{ place.count_comment ? place.count_comment : 0 }}</p>
            </div>
            <div class="ml-2">
                <button class="btn bg-pink-400 rounded-round" @click="$emit('add', place, target)" data-dismiss="modal">
                    Thêm
                </button>
            </div>
        </div>
    </li>`
})

Vue.component('place-item', {
    props: ['place', 'didx', 'pidx', 'placeofdate', 'movetype'],
    data: function() {
        return {
            root: APP.root,
            time_start: {
                hour: Math.floor(this.place.time_start / 60),
                minute: this.place.time_start % 60
            },
            time_stay: {
                hour: Math.floor(this.place.time_stay / 60),
                minute: this.place.time_stay % 60
            },
            time_free: {
                hour: Math.floor(this.place.time_free / 60),
                minute: this.place.time_free % 60
            }
        }
    },
    template: `<div class="place-item">
        <div class="place-item-card card mb-0">
            <div class="card-body p-2 position-relative">
                <div class="position-relative">
                    <div class="d-flex place-line-1">
                        <div class="place-thumbnail mr-2">
                            <img :src="root + 'uploads/' + place.thumbnail" height="60" width="90" class="border-radius-3">
                        </div>
                        <div class="d-flex flex-column justify-content-between align-items-start pr-3">
                            <a :href="root + 'app/place/detail/' + place.slug" target="_blank">
                                <h4 class="mb-2 font-weight-bold">{{ place.name }}</h4>
                            </a>
                            <a data-toggle="modal" data-target="#placeListModal" @click="$emit('get-recents', didx, place.lat, place.lng)" class="text-primary border-bottom-1 border-bottom-dashed">
                                <i class="icon-feed mr-1"></i>Quanh đây
                            </a>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between place-line-2 mt-3">
                        <div class="d-flex align-items-center mr-1">
                            <i class="icon-alarm mr-1"></i>Bắt đầu: 
                            <div class="dropdown ml-1">
                                <a class="list-icons-item dropdown-toggle caret-0 text-primary border-bottom-1 border-bottom-dashed" data-toggle="dropdown" aria-expanded="false">
                                    {{ oclockTimeFormat(place.time_start) }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(16px, 19px, 0px);">
                                    <div class="d-flex justify-content-between p-2">
                                        <input type="number" min="0" max="23" class="form-control mr-2" v-model="time_start.hour">
                                        <input type="number" min="0" max="59" class="form-control" v-model="time_start.minute">
                                    </div>
                                    <div class="d-flex justify-content-end p-2">    
                                        <button class="btn btn-primary btn-sm" @click="saveTimeStart">Lưu</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mr-1">
                            <i class="icon-alarm mr-1"></i>Lưu trú: 
                            <div class="dropdown ml-1">
                                <a class="list-icons-item dropdown-toggle caret-0 text-primary border-bottom-1 border-bottom-dashed" data-toggle="dropdown" aria-expanded="false">
                                    {{ rangeTimeFormat(place.time_stay) }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(16px, 19px, 0px);">
                                    <div class="d-flex justify-content-between p-2">
                                        <input type="number" min="0" max="23" class="form-control mr-2" v-model="time_stay.hour">
                                        <input type="number" min="0" max="59" class="form-control" v-model="time_stay.minute">
                                    </div>
                                    <div class="d-flex justify-content-end p-2">   
                                        <button class="btn btn-primary btn-sm" @click="saveTimeStay">Lưu</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="dropdown">
                                <a class="list-icons-item dropdown-toggle caret-0 text-primary border-bottom-1 border-bottom-dashed" data-toggle="dropdown" aria-expanded="false">
                                    <i class="icon-notebook"></i> 
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(16px, 19px, 0px); width: 250px;">
                                    <div class="p-2">
                                        <textarea class="form-control" cols="35" rows="4" v-model="place.note"></textarea>
                                    </div>
                                    <div class="d-flex justify-content-end p-2">   
                                        <button class="btn btn-primary btn-sm">Lưu</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="remove-btn position-absolute top-0 right-0">
                        <a @click="$emit('remove-place', didx, pidx)" class="text-danger"><i class="icon-cancel-circle2"></i></a>
                    </div>
                </div>
                <div class="time-free-card card mb-0 p-2" v-if="place.time_free > 0 && pidx < placeofdate - 1">
                    <div class="position-relative">
                        <div class="d-flex flex-row justify-content-between align-items-center mr-4">
                            <div class="d-flex align-items-center">
                                <i class="icon-alarm mr-1"></i>Trống: 
                                <div class="dropdown ml-1">
                                    <a class="list-icons-item dropdown-toggle caret-0 text-primary border-bottom-1 border-bottom-dashed" data-toggle="dropdown" aria-expanded="false">
                                        {{ rangeTimeFormat(place.time_free) }}
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(16px, 19px, 0px)">
                                        <div class="d-flex justify-content-between p-2">
                                            <input type="number" min="0" max="23" class="form-control mr-2" v-model="time_free.hour">
                                            <input type="number" min="0" max="59" class="form-control" v-model="time_free.minute">
                                        </div>
                                        <div class="d-flex justify-content-end p-2">   
                                            <button class="btn btn-primary btn-sm" @click="saveTimeFree">Lưu</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="remove-btn position-absolute top-0 right-0">
                                <a @click="removeFreeTime()" class="text-danger"><i class="icon-cancel-circle2"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="move-to-next-place" v-if="pidx < placeofdate - 1">
            <div class="move-type-wrap d-flex">
                <div class="dropdown move-type position-relative mr-2">
                    <a class="list-icons-item dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <i :class="movetype[place.move_type].icon"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(16px, 19px, 0px);">
                        <a class="dropdown-item" v-for="(type, idx) in movetype" @click="changeMoveType(idx)">
                            <i :class="type.icon"></i> {{ type.label }}
                        </a>
                    </div>
                </div>
                <span>{{ place.distance.toFixed(2) + 'km - ' + rangeTimeFormat(place.time_move) }}</span>
            </div>
        </div>
    </div>`,
    methods: {
        rangeTimeFormat: function(minute) {
            return convertMinuteToTime(minute, 'range');
        },

        oclockTimeFormat: function(minute) {
            return convertMinuteToTime(minute, 'oclock');
        },

        removeFreeTime: function() {
            this.place.time_free = 0
            this.$emit('on-modify-place', this.didx, this.pidx)
        },

        saveTimeStay: function() {
            var old_time_stay = this.place.time_stay
            var new_time_stay = convertTimeToMinute(this.time_stay.hour, this.time_stay.minute)
            if (old_time_stay != new_time_stay) {
                this.place.time_stay = new_time_stay
                this.$emit('on-modify-place', this.didx, this.pidx)
            }
        },

        saveTimeFree: function() {
            var old_time_free = this.place.time_free
            var new_time_free = convertTimeToMinute(this.time_free.hour, this.time_free.minute)
            if (old_time_free != new_time_free) {
                this.place.time_free = new_time_free
                this.$emit('on-modify-place', this.didx, this.pidx)
            }
        },

        saveTimeStart: function() {
            var old_time_start = this.place.time_start
            var new_time_start = convertTimeToMinute(this.time_start.hour, this.time_start.minute)
            if (old_time_start != new_time_start) {
                this.place.time_start = new_time_start
                this.$emit('on-change-time-start', old_time_start, new_time_start, this.didx, this.pidx)
            }
        },

        changeMoveType: function(move_type) {
            if (this.place.move_type != move_type) {
                this.place.move_type = move_type
                this.place.time_move = Math.ceil(this.place.distance / this.movetype[this.place.move_type].velocity * 60)
                this.$emit('on-modify-place', this.didx, this.pidx)
            }
        }
    }
})

Vue.component('place-item-detail', {
    props: ['place', 'didx', 'pidx', 'placeofdate', 'movetype'],
    data: function() {
        return {
            root: APP.root
        }
    },
    template: `<div class="place-item">
        <div class="place-item-card card mb-0">
            <div class="card-body p-2 position-relative">
                <div class="position-relative">
                    <div class="d-flex place-line-1">
                        <div class="place-thumbnail mr-2">
                            <img :src="root + 'uploads/' + place.thumbnail" height="60" width="90" class="border-radius-3">
                        </div>
                        <div class="d-flex flex-column justify-content-between align-items-start">
                            <a :href="root + 'app/place/detail/' + place.slug" target="_blank">
                                <h4 class="mb-2 font-weight-bold">{{ place.name }}</h4>
                            </a>
                            <span class="mb-1">Bắt đầu: {{ oclockTimeFormat(place.time_start) }}</span>
                            <span class="mb-1">Lưu trú: {{ rangeTimeFormat(place.time_stay) }}</span>
                        </div>
                    </div>
                </div>
                <div class="time-free-card card mb-0 p-2" v-if="place.time_free > 0 && pidx < placeofdate - 1">
                    <div class="position-relative">
                        <div class="d-flex flex-row justify-content-between align-items-center mr-4">
                            <div class="d-flex align-items-center">
                                <i class="icon-alarm mr-1"></i>
                                Trống: 
                                <span class="ml-1">{{ rangeTimeFormat(place.time_free) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="move-to-next-place" v-if="pidx < placeofdate - 1">
            <div class="move-type-wrap d-flex align-items-center">
                <i :class="movetype[place.move_type].icon" class="mr-2"></i>
                <span>{{ place.distance.toFixed(2) + 'km - ' + rangeTimeFormat(place.time_move) }}</span>
            </div>
        </div>
    </div>`,
    methods: {
        rangeTimeFormat: function(minute) {
            return convertMinuteToTime(minute, 'range');
        },

        oclockTimeFormat: function(minute) {
            return convertMinuteToTime(minute, 'oclock');
        }
    }
})