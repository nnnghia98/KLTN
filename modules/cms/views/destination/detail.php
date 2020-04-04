<?php 
use app\modules\cms\CMSConfig;
use app\modules\cms\PathConfig;
use app\modules\contrib\gxassets\GxLaddaAsset;

GxLaddaAsset::register($this);
$pageData = [
    'pageTitle' => 'Thông tin điểm đến',
    'headerElements' => [],
];
?>
<?= $this->render(PathConfig::getAppViewPath('tagPageHeader', true), $pageData); ?>

<div class="content" id="detail-destination-pageadmin">
    <div class="card">
        <div class="card-header">
            
        </div>
        <div class="card-body">
            <div class="destination-detail">
                <div class="row form-group">
                    <div class="col-3 col-form-label">Điểm đến</div>
                    <div class="col-9">
                        <input type="text" class="form-control" :value="destination.name" disabled>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-3 col-form-label">Mô tả</div>
                    <div class="col-9">
                        <input type="text" class="form-control" :value="destination.subtitle" disabled>
                    </div>
                </div>
            
                </div>
                <div class="row form-group">
                    <div class="col-3 col-form-label">tên</div>
                    <div class="col-9">
                        <input type="text" class="form-control" :value="destination.slug" disabled>
                    </div>
                </div>
                
                <div class="row form-group">
                    <div class="col-3 col-form-label">Lượt xem</div>
                    <div class="col-9">
                        <select class="form-control" :value="destination.viewed" disabled>
                            <option v-for="(label, value) in roles" :value="value">{{ label }}</option>
                        </select>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-3 col-form-label">hình ảnh</div>
                    <div class="col-9">
                        <input type="text" class="form-control" :value="destination.thumbnail" disabled>
                    </div>
                </div>
               
            </div>
            <hr>

        </div>
    </div>
</div>

<script>

    var vm = new Vue({
        el: '#detail-destination-pageadmin',
        data: {
            user: JSON.parse('<?= json_encode($destination, true) ?>'),
            roles: JSON.parse('<?= json_encode($roles, true) ?>'),
        }
        
           
</script>