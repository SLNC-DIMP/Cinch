<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('org_file_path')); ?>:</b>
	<?php echo CHtml::encode($data->org_file_path); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('temp_file_path')); ?>:</b>
	<?php echo CHtml::encode($data->temp_file_path); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('file_type_id')); ?>:</b>
	<?php echo CHtml::encode($data->file_type_id); ?>
	<br />


	<b><?php echo CHtml::encode($data->getAttributeLabel('checksum')); ?>:</b>
	<?php echo CHtml::encode($data->checksum); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('virus_check')); ?>:</b>
	<?php echo CHtml::encode($data->virus_check); ?>
	<br />

	
	<b><?php echo CHtml::encode($data->getAttributeLabel('dynamic_file')); ?>:</b>
	<?php echo CHtml::encode($data->dynamic_file); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('last_modified')); ?>:</b>
	<?php echo CHtml::encode($data->last_modified); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('problem_file')); ?>:</b>
	<?php echo CHtml::encode($data->problem_file); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('user_id')); ?>:</b>
	<?php echo CHtml::encode($data->user_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('upload_file_id')); ?>:</b>
	<?php echo CHtml::encode($data->upload_file_id); ?>
	<br />

	

</div>