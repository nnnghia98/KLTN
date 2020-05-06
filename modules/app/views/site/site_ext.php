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
</style>