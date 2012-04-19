<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'user-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'username'); ?>
		<?php echo $form->textField($model,'username',array('size'=>25,'maxlength'=>25)); ?>
		<?php echo $form->error($model,'username'); ?>
	</div>
    <div class="row">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>25,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'email'); ?>
	</div>
    <?php if($model->isNewRecord): ?>
    <div class="row">
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->passwordField($model,'password',array('size'=>25,'maxlength'=>25)); ?>
		<?php echo $form->error($model,'password'); ?>
	</div>
    <div class="row">
		<?php echo $form->labelEx($model,'password_repeat'); ?>
		<?php echo $form->passwordField($model,'password_repeat',array('size'=>25,'maxlength'=>25)); ?>
		<?php echo $form->error($model,'password_repeat'); ?>
	</div>
	<?php endif; ?>
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->