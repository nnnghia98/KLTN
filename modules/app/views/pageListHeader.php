<style>
	.page-list-header {
		height: 60vh;
		background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('<?= $backgoundHeader ?>');
		background-size: cover;
		background-position: center;
	}

	.page-list-title h1 {
		font-size: 2rem;
	}

	.breadcrumb-link {
		font-weight: 600;
		font-size: 1rem;
	}

	.breadcrumb-link:last-child {
		font-size: 1.2rem;
		color: #fff !important
	}

</style>
<div class="page-list-header d-flex justify-content-center align-items-center flex-column">
	<div class="container h-100 position-relative">
		<div class="position-absolute bottom-0 left-0 pb-4">
			<div class="page-list-title">
				<h1 class="font-weight-bold text-white text-center mb-0"><?= $pageTitle ?></h1>
			</div>
			<div class="page-breadcrumbs d-flex flex-column flex-md-row align-items-center">
				<a href="<?= Yii::$app->homeUrl ?>" class="breadcrumb-link breadcrumb-homepage text-white"><i class="icon-home mr-1"></i> <span>Trang chủ</span></a>
				<span class="mx-2 text-white">/</span>
				<a class="breadcrumb-link breadcrumb-current-page"><span><?= $pageBreadcrumb ?></span></a>
			</div>
		</div>
	</div>
</div>