
$(function () {
    $('.navbar-nav .nav-item a').each(function () {
        if ($(this).prop('href') == window.location.href) {
            if ($(this).hasClass('navbar-nav-link')) {
                $(this).addClass('active');
            } else {
                $(this).parent().siblings('.navbar-nav-link').addClass('active');
            }
        }
    });

    $('.navbar-nav .nav-item a.navbar-nav-link').each(function () {
        if ($(this).prop('href') == window.location.href) {
            $(this).addClass('active');
        }
    });

    $(window).scroll(function() {    // this will work when your window scrolled.
        var height = $(window).scrollTop();  //getting the scrolling height of window
        var navbar = $(".navbar")
		if(height  > $(window).height() / 2) {
			navbar.addClass('sticky');
		} else{
			navbar.removeClass('sticky');
		}
	});
});


function fixImageActionsHeight() {
    var cardImgActions = $('.card-img-actions');
    cardImgActions.height(cardImgActions.width() * 2 / 3);
}

$(window).on('resize', function() {
    fixImageActionsHeight();
});

function formatTime(time) {
    var created_at = new Date(time);
    var formatted_date = formatStringDateTime(created_at.getDate())
        + "-" + formatStringDateTime((created_at.getMonth() + 1))
        + "-" + formatStringDateTime(created_at.getFullYear())
        + " " + formatStringDateTime(created_at.getHours())
        + ":" + formatStringDateTime(created_at.getMinutes())
        + ":" + formatStringDateTime(created_at.getSeconds());
    return formatted_date;
};

function formatStringDateTime(string) {
    if (string.toString().length === 1) {
        return '0' + string;
    }
    return string;
}

Vue.use(Toasted, Option);
function toastMessage(type, message) {
    Vue.toasted.show(message, {
        type: type,
        theme: "bubble",
        position: "top-center",
        duration: 4000
    });
};

function substringMatcher(words) {
    return function (q, cb) {
        var matches, substrRegex;
        matches = [];
        substrRegex = new RegExp(q, 'i');
        $.each(words, function (i, word) {
            if (substrRegex.test(word)) {
                matches.push(word);
            }
        });
        cb(matches);
    };
};

function reverseDate(date) {
    var reverse = date.split("-").reverse().join("/");
    return reverse;
}

function offset(el) {
    var rect = el.parent(),
    scrollLeft = window.pageXOffset || document.documentElement.scrollLeft,
    scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    return { top: rect.top + scrollTop, left: rect.left + scrollLeft }
}

function getAvatarPath(avatar) {
    var path = '/' + (avatar ? 'uploads/' + avatar : 'resources/images/no_avatar.jpg')
    return path
}

function sendAjax(api, data, type = 'POST', callback) {
    $.ajax({
        url: api,
        type: type,
        data: data,
        success: function(resp) {
            callback(resp)
        },
        error: function(msg) {
            toastMessage('error', 'Lỗi!')
        }
    })
}

/**VUE COMPONENT */
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
            <div class="mr-2">
                <a :href="root + '/app/place/visit/' + place.slug" class="media-list-photo">
                    <img :src="root + '/uploads/' + place.thumbnail" height="150" width="225" :alt="place.name">
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