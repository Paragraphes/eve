<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
		<title>EVE</title>
	</head>
	<body>
	
	<?php
	if ($user->isAuthenticated()) {
		?>
		<a href="?deco=1"><i class="icon-off"></i><?php echo MENUDECO ;?></a>
		<?php
	}
	?>
	
		<?php if ($user->hasFlash()) echo '<p style="text-align: center;">', $user->getFlash(), '</p>'; ?>
		<?php echo $content;?>
	</body>
</html>
