<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'user-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>
	<?php echo $form->errorSummary($model); ?>
    <?php if(Yii::app()->user->hasFlash('success')):?>
    <div class="info">
        <?php echo Yii::app()->user->getFlash('success'); ?>
    </div>
<?php endif; ?>
	<div class="row">
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->passwordField($model,'password',array('id'=>'pass', 'size'=>25,'maxlength'=>25)); ?>
		<?php echo $form->error($model,'password'); ?>
	</div>
     <div class="row">
		<?php echo $form->labelEx($model,'password_repeat'); ?>
		<?php echo $form->passwordField($model,'password_repeat',array('id'=>'pass_repeat', 'size'=>25,'maxlength'=>25)); ?>
		<?php echo $form->error($model,'password_repeat'); ?>
	</div>
    <?php echo $form->hiddenField($model,'username'); ?>
    <?php echo $form->hiddenField($model,'email'); ?>
	<div class="row buttons">
		<?php echo CHtml::submitButton('Update'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->