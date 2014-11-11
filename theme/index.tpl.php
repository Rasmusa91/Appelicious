<!doctype html>
<html lang = "<?= $lang; ?>">
	<head>
		<meta charset = "<?= $charset; ?>"/>
		
		<title><?= getTitle($appelicious); ?></title>
		
		<?php if(isset($favicon) && !empty($favicon)): ?>
			<link rel="shortcut icon" href="<?= $favicon; ?>"/>
		<?php endif; ?>
		
		<?php foreach($stylesheets as $stylesheet): ?>
			<link rel="stylesheet" type="text/css" href="<?= $stylesheet; ?>"/>
		<?php endforeach; ?>

		<script type="text/javascript" src="<?= $modernizr; ?>"></script>	
	</head>
	
	<body>
		<?php
		foreach($content as $cont){
			$cont->render($appelicious);
		} 
		?>
		
		<script type="text/javascript" src="<?= $jquery; ?>"></script>
		<?php foreach($javascripts as $javascript): ?>
			<script type="text/javascript" src="<?= $javascript; ?>"></script>
		<?php endforeach; ?>		
		
		<?php if(isset($googleAnalyticsID) && !empty($googleAnalyticsID)): ?>
			<script type="text/javascript">
				var _gaq=[['_setAccount','<?= $googleAnalyticsID; ?>'],['_trackPageview']];
				(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
				g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
				s.parentNode.insertBefore(g,s)}(document,'script'));
			</script>
		<?php endif; ?>
	</body>
</html>