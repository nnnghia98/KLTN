
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