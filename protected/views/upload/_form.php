<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'upload-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'user_id'); ?>
		<?php echo $form->textField($model,'user_id'); ?>
		<?php echo $form->error($model,'user_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'upload_path'); ?>
		<?php echo $form->textField($model,'upload_path',array('size'=>60,'maxlength'=>250)); ?>
		<?php echo $form->error($model,'upload_path'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'processed'); ?>
		<?php echo $form->textField($model,'processed'); ?>
		<?php echo $form->error($model,'processed'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->