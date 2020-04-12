
<style>
    .homepage_top{
        height: 50vh;
		background: linear-gradient(rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2)), url('<?= Yii::$app->homeUrl . 'resources/images/banner1.jpg' ?>');
		background-size: cover;
		background-position: center;
        border: 1px solid #ccc;
        border-radius: .1875rem;
    }
    .homepage_top #page-title-heading{
        color:white;
        font-size: 28pt;
        margin-top: 80px;
        
    }
    .homepage_top .text-search{
    border-top-color: #ccc !important;
    border: 1px solid #ccc;
    border-radius: .1875rem;
    margin-left: 43%;

    }
    #title_home{
        font-style: italic;
        font-size: 18pt;
        color:black;
    }
    #title-home h1{
        font-style: italic;
        font-size: 13pt;
        color:red;
    }
    #top{
        height: 20vh;
    }
    .plan {
        margin-top: 2vh;
        border: 1px solid #ccc;
        border-radius: .1875rem;
        height: 40vh;
		background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url('<?= Yii::$app->homeUrl . 'resources/images/plan.jpg' ?>');
		background-size: cover;
		background-position: center;
    }
    .plan h1{
        color:red;
        font-family: blue;
        margin-top: 45px;
    }
    .plan h1 a{
        color:antiquewhite;
    }
    .container {
    margin-right: auto;
    margin-left: auto;
    padding-left: 15px;
    padding-right: 15px;
}
.destination-grid-item {
    background-position-x: 50%;
    background-position-y: 50%;
    background-size: cover;
    background-attachment: scroll;
    margin-bottom: 5px;
    margin-top: 0px;
    padding: 0px;
    height: 300px;
}
   
</style>
<div id="homepage" class="homepage" style="height: 200vh">
<div class="homepage_top">
<div class="text-center py-3" id="page-title-heading">Cuộc đời là những chuyến đi</div>
<div class="input-group">
      <input type="text" class="text-search" placeholder="Bạn muốn đi đâu?" v-model="keyword">
            <span class="input-group-text bg-secondary border-secondary text-white">
                <i class="icon-search4"></i>
            </span>
         
        </div>
</div>

   
     <div class="text-center py-3" id="top">
     <div id="title_home">CẨM NANG DU LỊCH<br><h1> Tất cả những thông tin hữu ích bạn cần tham khảo để lên kế hoạch cho chuyến du lịch của mình</h1></div>
       <div id='logo'>
          <button  type="btn_lg" class="btn btn-outline bg-indigo-400 border-indigo400" ><a href="<?= Yii::$app->homeUrl . 'app/destination/map' ?>"><img src="<?= Yii::$app->homeUrl ?>resources/images/lgbd.jpg" alt=""></a></button>
          <button  type="btn_lg" class="btn btn-outline bg-indigo-400 border-indigo400" ><a href="<?= Yii::$app->homeUrl . 'app/destination' ?>"><img src="<?= Yii::$app->homeUrl ?>resources/images/dd.jpg" alt=""></a></button>
          <button  type="btn_lg" class="btn btn-outline bg-indigo-400 border-indigo400" ><a href="<?= Yii::$app->homeUrl . 'app/plan' ?>"><img src="<?= Yii::$app->homeUrl ?>resources/images/lt.jpg" alt=""></a></button>
          <button  type="btn_lg" class="btn btn-outline bg-indigo-400 border-indigo400" ><a href="<?= Yii::$app->homeUrl . 'app/place/rest' ?>"><img src="<?= Yii::$app->homeUrl ?>resources/images/ks.jpg" alt=""></a></button>
          <button  type="btn_lg" class="btn btn-outline bg-indigo-400 border-indigo400" ><a href="<?= Yii::$app->homeUrl . 'app/place/food' ?>"><img src="<?= Yii::$app->homeUrl ?>resources/images/qa.png" alt=""></a></button>
        </div>
    </div>
    <div class="plan">
        
         <div class="text-center py-3">
         <h1>Hãy đến với chúng tôi và viết nên câu chuyện của chính bạn<h1>
         <button type="btn_plan" class="btn btn-danger rounded-round">
         <a href="<?= Yii::$app->homeUrl . 'app/plan' ?>">Tạo lịch trình</a></button>
        </div>
     </div>
    <div class="home_destination">
    <a href="<?= Yii::$app->homeUrl . 'app/destination' ?>"><h1>Những địa điểm nổi bật</h1></a>
          <img src="<?= Yii::$app->homeUrl ?>resources/images/HaN.jpg" alt="">
          <img src="<?= Yii::$app->homeUrl ?>resources/images/DN.jpg" alt="">
          <img src="<?= Yii::$app->homeUrl ?>resources/images/Hue.jpg" alt="">
          <img src="<?= Yii::$app->homeUrl ?>resources/images/DL.jpg" alt="">
          <a href="<?= Yii::$app->homeUrl . 'app/destination' ?>"><button type="btn_plan" class="btn btn-outline-danger">Xem Thêm</button></a>
       </div> 
    <div class="home_visit">
    <a href="<?= Yii::$app->homeUrl . 'app/place' ?>"> <h1>Điểm dừng chân tuyệt vời</h1></a>
       <img src="<?= Yii::$app->homeUrl ?>resources/images/dct.png" alt="">
       <img src="<?= Yii::$app->homeUrl ?>resources/images/gbt.jpg" alt="">
        <img src="<?= Yii::$app->homeUrl ?>resources/images/lvh.jpg" alt="">
        <img src="<?= Yii::$app->homeUrl ?>resources/images/bali.png" alt="">
        <a href="<?= Yii::$app->homeUrl . 'app/place/visit' ?>"><button type="btn_plan" class="btn btn-outline-danger">Xem Thêm</button>
       </div>
    </div>

</div>
