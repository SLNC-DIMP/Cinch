<h5>Select file checksum type: </h5>

<?php echo CHtml::activeRadiobuttonList($model, 'checksum_type', array(1=>'SHA1', 2=>'MD5'), array('separator'=> '')); ?>
<br /><br />

<h5>Select file download type: </h5>
<?php echo CHtml::activeRadiobuttonList($model, 'download_type', array(1=>'Zip', 2=>'Bagit (tar.gz compression)'), array('separator'=> '')); ?>
<br /><br />

<h5>Select whether images and/or pdfs should be converted to preservation formats (optional): </h5>
<?php echo CHtml::activeLabel($model, 'JPEG2000'); ?>
<?php echo CHtml::activeCheckbox($model, 'jp2'); ?>

<?php echo CHtml::activeLabel($model, 'PDFA'); ?>
<?php echo CHtml::activeCheckbox($model, 'pdfa'); ?>
<br /><br />

<div id="pdfa_select" class='hide'>
    <h5>Force PDF/a conversion: </h5>
    <?php echo CHtml::activeRadiobuttonList($model, 'pdfa_convert', array(1=>'Yes', 0=>'No'), array('separator'=> '')); ?>
</div>