<div class="view">

	<b>Download zip file (created on <?php echo CHtml::encode($data->creationdate); ?>):<br /></b>
	<?php echo CHtml::link('Download this file' , CHtml::encode('zipGzDownloads/download/'. $data->id)); ?>
	<br />

</div>