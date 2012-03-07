<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'zip-gz-downloads-form',
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
		<?php echo $form->labelEx($model,'path'); ?>
		<?php echo $form->textField($model,'path',array('size'=>60,'maxlength'=>500)); ?>
		<?php echo $form->error($model,'path'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'downloaded'); ?>
		<?php echo $form->textField($model,'downloaded'); ?>
		<?php echo $form->error($model,'downloaded'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'creationdate'); ?>
		<?php echo $form->textField($model,'creationdate'); ?>
		<?php echo $form->error($model,'creationdate'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->