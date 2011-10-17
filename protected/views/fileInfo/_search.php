<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'id'); ?>
		<?php echo $form->textField($model,'id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'org_file_path'); ?>
		<?php echo $form->textField($model,'org_file_path',array('size'=>60,'maxlength'=>2084)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'temp_file_path'); ?>
		<?php echo $form->textField($model,'temp_file_path',array('size'=>60,'maxlength'=>1000)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'file_type_id'); ?>
		<?php echo $form->textField($model,'file_type_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'checksum_created'); ?>
		<?php echo $form->textField($model,'checksum_created'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'checksum'); ?>
		<?php echo $form->textField($model,'checksum',array('size'=>40,'maxlength'=>40)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'virus_check'); ?>
		<?php echo $form->textField($model,'virus_check'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'dynamic_file'); ?>
		<?php echo $form->textField($model,'dynamic_file'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'last_modified'); ?>
		<?php echo $form->textField($model,'last_modified',array('size'=>15,'maxlength'=>15)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'problem_file'); ?>
		<?php echo $form->textField($model,'problem_file'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'user_id'); ?>
		<?php echo $form->textField($model,'user_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'upload_file_id'); ?>
		<?php echo $form->textField($model,'upload_file_id'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->