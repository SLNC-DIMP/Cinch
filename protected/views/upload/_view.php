<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('user_id')); ?>:</b>
	<?php echo CHtml::encode($data->user_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('upload_path')); ?>:</b>
	<?php echo CHtml::encode($data->upload_path); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('processed')); ?>:</b>
	<?php echo CHtml::encode($data->processed); ?>
	<br />


</div>