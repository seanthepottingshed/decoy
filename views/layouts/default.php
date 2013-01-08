<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
	<head>
		<meta charset="utf-8"/>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
		<?= HTML::title() ?>
		<meta name="viewport" content="width=device-width"/>
		<meta name="csrf" content="<?=Session::token()?>"/>
		<?= render('decoy::layouts.buk_builder._header') ?>
	</head>
	<body class="<?=Request::route()->controller?> <?=Request::route()->controller_action?>">
		
		<?// Nav ?>
		<?= render('decoy::layouts._nav') ?>
		
		<?// If breadcrumbs haven't been nested, manually render now  ?>
		<?= empty($breadcrumbs) ? render('decoy::layouts._breadcrumbs') : $breadcrumbs ?>
	
		<?// Container for notifications ?>
		<div class='notifications top-right'></div>
		
		<?// The main page content ?>
		<div id="main" class="container">
			<?= $content?>
		</div>
		
		<?= render('decoy::layouts.buk_builder._footer') ?>
</body>
</html>