<?php
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl."/js/upload_form.js", CClientScript::POS_HEAD);

$this->breadcrumbs=array(
	'Uploads',
); ?>
<div id="upload_form">
	<h2>Select a file to upload to Cinch</h2>
    <p>Files must be .txt or .cvs files.  Please make sure each URL listed is on its own line in your file, and limit your list to a maximum of 10,000 urls.</p>
	<?php if($uploaded):?>
        <p>Your file was successfully uploaded.  Feel free to to logoff now. We'll send you an email with download instructions after we've retrieved and processed the files in your list (Typically 24 hours).</p>
        <br />
    <?php endif ?>
    <div class="pull-left">
    <?php echo CHtml::beginForm('', 'post', array('enctype'=>'multipart/form-data')); ?>
    <?php echo CHtml::error($model, 'path'); ?>
    <?php echo CHtml::error($model, 'urls_in_list'); ?>
    <br />
     <?php echo CHtml::activeFileField($model, 'path'); ?>
     <br /><br />

    <?php $this->renderPartial('//layouts/_file_process', array('model'=>$model)); ?>

    <?php echo CHtml::submitButton('Upload File'); ?>
    <?php echo CHtml::endForm(); ?>
   
</div>