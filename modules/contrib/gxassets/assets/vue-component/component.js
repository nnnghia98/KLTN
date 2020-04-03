Vue.component('userFollowing', {
    props: ['following', 'userid', 'fullname'],
    template: `<div class="following d-flex justify-content-center">
        <button class="btn btn-sm btn-warning" @click="follow" v-if="!followingStt"><i class="icon-user-plus mr-2"></i>Theo dõi</button>
        <button class="btn btn-sm btn-primary" @click="unfollow" v-else><i class="icon-user-check mr-2"></i>Bỏ theo dõi</button>
    </div>`,
    data: function() {
        return {
            followingStt: this.following
        }
    },
    methods: {
        follow: function() {
            var _this = this,
                api = '/app/user/follow',
                data = { 
                    userid: this.userid,
                    fullname: this.fullname
                }
            
            sendAjax(api, data, function(resp) {
                if(resp.status) {
                    _this.followingStt = true
                    toastMessage('success', resp.message)
                } else {
                    toastMessage('error', resp.message)
                }
            })
        },

        unfollow: function() {
            var _this = this,
                api = '/app/user/unfollow',
                data = { 
                    userid: this.userid,
                    fullname: this.fullname
                }
            
            sendAjax(api, data, function(resp) {
                if(resp.status) {
                    _this.followingStt = false
                    toastMessage('success', resp.message)
                } else {
                    toastMessage('error', resp.message)
                }
            })
        },
    }
})