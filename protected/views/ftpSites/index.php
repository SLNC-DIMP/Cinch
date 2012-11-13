<?php
$this->breadcrumbs=array(
	'Ftp Sites',
);
/*
$this->menu=array(
	array('label'=>'Create FtpSites', 'url'=>array('create')),
	array('label'=>'Manage FtpSites', 'url'=>array('admin')),
); */
?>

<h1>Upload FTP site information</h1>

<div class="upload_form">
    <?php echo  CHtml::beginForm(); ?>
        <?php echo CHtml::errorSummary($model); ?>


        <strong><?php echo CHtml::activeLabel($model,'FTP address:'); ?></strong>
        <?php echo CHtml::activeTextField($model,'path', array('size'=>55)) ?>
        <br /><br />

        <strong><?php echo CHtml::activeLabel($model,'FTP username:'); ?></strong>
        <?php echo CHtml::activeTextField($model,'username') ?>
        <br /><br />


        <strong><?php echo CHtml::activeLabel($model,'FTP password:'); ?></strong>
        <?php echo CHtml::activePasswordField($model,'password') ?>

        <br /><br />
        <strong><?php echo CHtml::activeLabel($model,'Port:'); ?></strong>
        <?php echo CHtml::activeRadiobuttonList($model, 'port', array(1=>'21 (FTP)', 2=>'22 (SFTP)'), array('separator'=> '')); ?>
        <br /><br />

    <?php // $this->renderPartial('//layouts/_file_process', array('model'=>$file_params)); ?>

    <div class="row submit">
        <?php echo CHtml::submitButton('Save'); ?>
    </div>
    <?php echo CHtml::activeHiddenField($model,'user_id', array('value'=>Yii::app()->user->id)) ?>
    <?php echo CHtml::endForm(); ?>
</div>

