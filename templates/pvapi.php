<?php if(!isset($episodes)) : ?>
<h3>PinkVisual API</h3>
<p>Congratulations, you have the PinkVisual API correctly set up.</p>
<?php else: ?>
	<?php foreach($episodes as $episode): ?>
		<?php require(dirname(__FILE__)."/pvapi-episode.php"); ?>
	<?php endforeach; ?>
<?php endif; ?>