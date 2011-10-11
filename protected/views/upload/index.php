<div id="upload_form">
	<h2>Select a file to upload to Cinch</h2>
    <p>Files must be .txt or .cvs files.  Please make sure each URL listed is on its own line in your file.</p>
	<?php if($uploaded):?>
        <p>Your file was successfully uploaded. We'll send you an email when your files are ready for download.</p>
        <br />
    <?php endif ?>
    
    <?php echo CHtml::beginForm('', 'post', array('enctype'=>'multipart/form-data')); ?>
    <?php echo CHtml::error($model, 'file'); ?>
    <br />
    <?php echo CHtml::activeFileField($model, 'file'); ?>
    <br /><br />
    <?php echo CHtml::submitButton('Upload'); ?>
    <?php echo CHtml::endForm(); ?>
</div>