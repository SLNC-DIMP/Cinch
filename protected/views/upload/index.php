<?php if($uploaded):?>
	<p>Your file was successfully uploaded. We'll sent you an email when your files are ready for download.</p>
<?php endif ?>

<?php echo CHtml::beginForm('', 'post', array('enctype'=>'multipart/form-data')); ?>
<?php echo CHtml::error($model, 'file'); ?>
<?php echo CHtml::activeFileField($model, 'file'); ?>
<?php echo CHtml::submitButton('Upload'); ?>
<?php echo CHtml::endForm(); ?>