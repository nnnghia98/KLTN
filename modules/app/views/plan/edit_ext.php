<style>
    .plan-detail-wrap {
        display: flex;
        overflow-x: scroll
    }

    .date-item-wrap {
        width: 300px;
        margin-right: 1.25rem
    }

    .btn-add-place {
        padding: .625rem;
        border: 2px dashed var(--main-color-pink);
        border-radius: .625rem;
        color: var(--main-color-pink);
        cursor: pointer;
        transition: .5s all ease;
    }

    .btn-add-place:hover {
        background: #ddd;
    }

    .move-to-next-place {
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
    }

    .move-type-wrap .move-type::before, 
    .move-type-wrap .move-type::after {
        content: "";
        position: absolute;
        display: block;
        width: 0;
        height: 70px;
        left: 50%;
        border: 1px dashed;
    }

    .move-type-wrap .move-type::before {
        top: 0;
        transform: translate(-100%, -100%);
    }

    .move-type-wrap .move-type::after {
        bottom: 0;
        transform: translate(-100%, 100%);
    }
</style>
<script>
    function convertTimeToMinute(hour, minute) {
        return parseInt(hour) * 60 + parseInt(minute);
    }

    function convertMinuteToTime(minute, type) {
        var hour = Math.floor(minute / 60);
        var min = Math.floor(minute % 60);
        var time = '';
        if (type === 'range') {
            if (hour !== 0) {
                time += hour + 'h';
            }
            if (min !== 0) {
                time += min + "'";
            }
        }
        if (type === 'oclock') {
            hour = hour >= 24 ? hour % 24 : hour;
            hour = hour < 10 ? '0' + hour : hour;
            min = min < 10 ? '0' + min : min;
            time += hour + ':' + min;
        }
        return time;
    }
</script>