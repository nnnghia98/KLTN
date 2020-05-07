<style>
    .carousel-item img {
        height: 90vh;
        object-fit: cover;
    }

    .carousel-caption-custom {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 80%;
        transform: translate(-50%, -50%);
        display: flex;
        justify-content: center;
        flex-direction: column;
        align-items: center;
    }

    .carousel-caption-custom .caption-label {
        font-size: 2.5rem;
        color: #fff;
        font-weight: bold;
    }

    .homepage-section {
        padding: 2.5rem 0;
    }

    .plan-introduction-wrap {
        height: 500px;
        background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('<?= Yii::$app->homeUrl . 'resources/images/plan-introduction.jpg' ?>');
        background-attachment: fixed;
        background-position: center;
        background-size: cover;
    }

    .feature-wrap {
        background: #dbdff1;
    }

    .destination-wrap .card-image:before{
        bottom: 0;
        content: "";
        display: block;
        height: 100%;
        left: 0;
        opacity: 0.85;
        position: absolute;
        width: 100%;
        z-index: 1;
        -webkit-box-shadow: 0 -100px 92px -35px #000 inset;
        box-shadow: 0 -100px 92px -35px #000 inset;
        -webkit-transition: all 0.5s ease-in-out;
        -o-transition: all 0.5s ease-in-out;
        transition: all 0.5s ease-in-out;
    }

    .destination-wrap .card-image:hover:before {
        -webkit-box-shadow: 0 -175px 92px -35px rgba(0,0,0,.5) inset;
        box-shadow: 0 -175px 92px -35px rgba(0,0,0,.5) inset;
    }

    .destination-wrap .card-image-overlay {
        position: absolute;
        z-index: 2;
        bottom: 0;
        left: 0;
        right: 0;
        width: 100%;
        transform: translateY(50%);
        -webkit-transition: all 0.5s ease-in-out;
        -o-transition: all 0.5s ease-in-out;
        transition: all 0.5s ease-in-out;
    }

    .destination-wrap .card-image-overlay a {
        color: #fff;
    }

    .destination-wrap .card-image:hover .card-image-overlay {
        transform: translateY(0);
    }

    .plan-wrap .card, .plan-wrap .card img,
    .place-wrap .card, .place-wrap .card img {
        -webkit-transition: all 0.3s ease-in-out;
        -o-transition: all 0.3s ease-in-out;
        transition: all 0.3s ease-in-out;
    }

    .plan-wrap .card:hover,
    .place-wrap .card:hover {
        box-shadow: 0 2px 10px rgba(0,0,0,.12), 0 2px 10px rgba(0,0,0,.24)
    }

    .plan-wrap .card:hover img.card-img,
    .place-wrap .card:hover img.card-img {
        -webkit-transform: rotate(5deg) scale(1.2);
        transform: rotate(5deg) scale(1.2);
    }
</style>