<style>
	.page-list-header {
		height: 50vh;
		background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url('<?= $backgoundHeader ?>');
		background-size: cover;
		background-position: center;
	}

	.page-title h1 {
		font-size: 2rem;
	}

	.breadcrumb-link {
		padding: .3125rem .875rem;
		position: relative;
		margin-right: .625rem;
		display: flex;
		justify-content: center;
		align-items: center;
		font-weight: 500;
		z-index: 0;
		cursor: pointer;
		margin-bottom: .3125rem;
		width: fit-content;
	}

	.breadcrumb-link::before,
	.breadcrumb-link::after {
		content: "";
		position: absolute;
		left: 0;
		height: 50%;
		width: 100%;
		background: #f0f0f0;
		border-left: #ccc solid 1px;
		border-right: #ccc solid 1px;
		z-index: -1;
	}

	.breadcrumb-link::after {
		transform: skew(-30deg);
		border-bottom: #ccc solid 1px;
		bottom: 0;
	}

	.breadcrumb-link::before {
		transform: skew(30deg);
		border-top: #ccc solid 1px;
		top: 0;
	}
</style>
<div class="page-list-header d-flex justify-content-center align-items-center flex-column">
	<div class="page-title">
		<h1 class="font-weight-bold text-uppercase text-white text-center"><?= $pageTitle ?></h1>
	</div>
	<div class="page-breadcrumbs d-flex flex-column flex-md-row justify-content-center align-items-center">
		<a href="<?= Yii::$app->homeUrl ?>" class="breadcrumb-link breadcrumb-homepage"><i class="icon-home mr-1"></i> <span>Trang chủ</span></a>
		<a class="breadcrumb-link breadcrumb-current-page"><span><?= $pageBreadcrumb ?></span></a>
	</div>
</div>