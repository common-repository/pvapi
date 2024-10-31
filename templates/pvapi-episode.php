<div class="pink_visual_episode">
	<h4 class="pink_visual_episode_title">
		<a href="<?php echo PVPlugin::join($episode->getJoin()); ?>"><?php echo $episode->getName(); ?></a>
	</h4>
		<img class="pink_visual_episode_tall_image" src="<?php echo $episode->getTallImage();?>" />
		<span class="pink_visal_episode_date">Published <?php echo date("F j, Y",$episode->getDate()); ?></span>
		<p class="pink_visual_episode_description"><?php echo $episode->getDescription(); ?></p>
	</div>
<div style="clear: both">&nbsp;</div>
