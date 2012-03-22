<div class="view">
	<?php $short_path = str_replace('/', '', strrchr($data->path, '/')); // hacky but works ?>
	<b>Download zip file (created on <?php echo CHtml::encode($data->creationdate); ?>):<br /></b>
	Download this file: <?php echo CHtml::link(CHtml::encode($short_path), CHtml::encode('zipGzDownloads/download/'. $data->id)); ?>
	<br />

</div>