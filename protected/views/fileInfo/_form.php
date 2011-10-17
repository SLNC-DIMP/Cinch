<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'file-info-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'org_file_path'); ?>
		<?php echo $form->textField($model,'org_file_path',array('size'=>60,'maxlength'=>2084)); ?>
		<?php echo $form->error($model,'org_file_path'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'temp_file_path'); ?>
		<?php echo $form->textField($model,'temp_file_path',array('size'=>60,'maxlength'=>1000)); ?>
		<?php echo $form->error($model,'temp_file_path'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'file_type_id'); ?>
		<?php echo $form->textField($model,'file_type_id'); ?>
		<?php echo $form->error($model,'file_type_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'checksum_created'); ?>
		<?php echo $form->textField($model,'checksum_created'); ?>
		<?php echo $form->error($model,'checksum_created'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'checksum'); ?>
		<?php echo $form->textField($model,'checksum',array('size'=>40,'maxlength'=>40)); ?>
		<?php echo $form->error($model,'checksum'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'virus_check'); ?>
		<?php echo $form->textField($model,'virus_check'); ?>
		<?php echo $form->error($model,'virus_check'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'dynamic_file'); ?>
		<?php echo $form->textField($model,'dynamic_file'); ?>
		<?php echo $form->error($model,'dynamic_file'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'last_modified'); ?>
		<?php echo $form->textField($model,'last_modified',array('size'=>15,'maxlength'=>15)); ?>
		<?php echo $form->error($model,'last_modified'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'problem_file'); ?>
		<?php echo $form->textField($model,'problem_file'); ?>
		<?php echo $form->error($model,'problem_file'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'user_id'); ?>
		<?php echo $form->textField($model,'user_id'); ?>
		<?php echo $form->error($model,'user_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'upload_file_id'); ?>
		<?php echo $form->textField($model,'upload_file_id'); ?>
		<?php echo $form->error($model,'upload_file_id'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->