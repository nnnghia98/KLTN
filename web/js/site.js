$(function() {
    $('.navbar-nav .nav-item a').each(function() {
        if ($(this).prop('href') == window.location.href) {
            if ($(this).hasClass('navbar-nav-link')) {
                $(this).addClass('active');
            } else {
                $(this).parent().siblings('.navbar-nav-link').addClass('active');
            }
        }
    });

    $('.navbar-nav .nav-item a.navbar-nav-link').each(function() {
        if ($(this).prop('href') == window.location.href) {
            $(this).addClass('active');
        }
    });

    $(window).scroll(function() { // this will work when your window scrolled.
        var height = $(window).scrollTop(); //getting the scrolling height of window
        var navbar = $(".navbar")
        if (height > $(window).height() / 2) {
            navbar.addClass('sticky');
        } else {
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

Vue.use(Toasted, Option);

function toastMessage(type, message) {
    Vue.toasted.show(message, {
        type: type,
        theme: "bubble",
        position: "top-center",
        duration: 4000
    });
};

function formatTime(time) {
    var created_at = new Date(time);
    var formatted_time = created_at.getDate().toString().padStart(2, '0')
        + "/" + (created_at.getMonth() + 1).toString().padStart(2, '0')
        + "/" + created_at.getFullYear();
    return formatted_time;
};

function formatDate(date) {
    if (!date) return null
    var [year, month, day] = date.split('-')
    return `${day}-${month}-${year}`
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
            console.log('error', 'Lỗi!')
        }
    })
}

function convertTimeToMinute(hour, minute) {
    return parseInt(hour) * 60 + parseInt(minute);
}

function convertMinuteToTime(minute, type) {
    var hour = Math.floor(minute / 60)
    var min = Math.floor(minute % 60)
    var time = ''
    if (type === 'range') {
        if (hour !== 0) {
            time += hour + 'h'
        }
        if (min !== 0) {
            time += min + '\''
        }
    }
    if (type === 'oclock') {
        hour = hour >= 24 ? hour % 24 : hour;
        time = hour.toString().padStart(2, '0') + ':' + min.toString().padStart(2, '0');
    }
    return time
}

/**VUE COMPONENT */