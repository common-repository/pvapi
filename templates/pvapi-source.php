<h3 class="pink_visual_source_title"><?php echo $source->getName(); ?></h3>
<div class="pink_visual_episode_list">
	<?php foreach ($episodes as $episode): ?>
		<?php require(dirname(__FILE__)."/pvapi-episode.php"); ?>
	<?php endforeach; ?>
</div>
