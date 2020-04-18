<?php 
use app\modules\cms\CMSConfig;
use app\modules\cms\PathConfig;

$pageData = [
    'pageTitle' => 'Quản trị điểm đến',
    'headerElements' => [],
];
?>
<?= $this->render(PathConfig::getAppViewPath('tagPageHeader', true), $pageData); ?>


<div class="content" id="destination-manage-pageadmin">
    <div class="card">
        <div class="card-header header-elements-inline">
            <h4 class="card-title font-weight-bold">#Danh sách điểm đến</h4>
            <div class="header-elemnts">
                <a href="<?= CMSConfig::getUrl('destination/create') ?>" class="btn btn-sm btn-primary"><i class="icon-user-plus mr-2"></i>Thêm điểm đến</a>
            </div>
        </div>
        <div class="card-body" v-cloak>
            <div class="loading-destinations text-center my-3" v-if="!loading" >
                <span><i class="icon-spinner2 spinner icon-2x mr-2"></i> Đang tải danh sách điểm đến</span>
            </div>
            <div class="destinations-available" v-else>
                <table class="tables tables-striped">
                    <tr>
                        <th>#</th>
                        <th>Điểm đến</th>
                        <th>Mô tả</th>
                        <th>tên<th>
                        <th>Thao tác</th>
                    </tr>
                    <tr v-for="(destination, index) in destinations">
                        <td>{{ index + 1 }}</td>
                        <td>{{ destination.name }}</td>
                        <td>{{ destination.subtitle }}</td>
                        <td>{{ destination.slug }}</td>
                        <td>
                            <button class="btn btn-icon btn-sm btn-outline-danger" @click="confirmDelete(destination.destination_id)"><i class="icon-trash"></i></button>
                        </td>
                    </tr>
                </table>
                <ul class="pagination-flat justify-content-center twbs-visible-pages pagination" v-if="paginations.pages > 1">
                    <li class="page-item" v-for="p, i in paginations.links" :class="p == 'current' ? 'active' : '' ">
                        <a href="#" class="page-link" @click="changePage(p)">
                            {{ i == 0 ? '← &nbsp; Đầu tiên' : (i == paginations.links.length - 1 ? 'Cuối cùng &nbsp; →' : (p == 'current' ? paginations.current : p)) }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="modal fade" id="delete-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-body">
                <h5>Bạn có chắc chắn xóa điểm đến này?</h5>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal" id="btn-delete-work" @click="deleteDestination">Delete</button>
            </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        var vm = new Vue({
            el: '#destination-manage-pageadmin',
            data: {
                destinations: null,
                selectdestination: null,
                loading: false,
                roles: JSON.parse('<?= json_encode($roles, true) ?>'),
                page: 1
            },
            created: function() {
                var _this = this;
                _this.$nextTick(function() {
                    _this.getDestinations();
                })
            },

            methods: {
                getDestinations: function() {
                    var _this = this;
                    var api = '<?= CMSConfig::getUrl('destination/get-list?page=') ?>' + _this.page;
                    _this.loading = false;
                    
                    _this.sendAjax(api, {}, function(resp) {
                        if (resp.status) {
                            _this.destinations = resp.destinations;
                            _this.paginations = resp.paginations;
                            _this.getDestinations = true;
                        } else {
                            toastMessage('error', resp.message);
                        }
                    })
                },

                changePage: function(page) {
                    if(page != 'current') {
                        this.page = page;
                        this.loadWorks();
                    }
                },


                confirmDelete: function(id) {
                    this.selecteddes = id
                    $('#btn-delete-work').css('display', 'block');
                    $('#delete-modal').modal('show');
                },
                
                deleteDestination: function() {
                    var _this = this,
                        api = '<?= CMSConfig::getUrl('destination/delete')?>',
                        data = {
                            id: _this.selectdestination
                        }
                    _this.sendAjax(api, data, function(resp) {
                        if(resp.status) {
                            toastMessage('success', resp.message);
                            _this.getDestination();
                        } else {
                            toastMessage('error', resp.message);
                        }
                    })
                },

                sendAjax(api, data, callback) {
                    $.ajax({
                        url: api,
                        type: 'POST',
                        data: data,
                        success: function(resp) {
                            callback(resp)
                        },
                        error: (msg) => {
                            toastMessage('error', 'Lỗi!')
                        }
                    })
                },
            }
        })
    })
</script>