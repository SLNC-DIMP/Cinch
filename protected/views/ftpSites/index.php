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

<div class="form">
    <?php echo  CHtml::beginForm(); ?>
        <?php echo CHtml::errorSummary($model); ?>

    <div class="row">
        <?php echo CHtml::activeLabel($model,'FTP address:'); ?>
        <?php echo CHtml::activeTextField($model,'path', array('size'=>55)) ?>
    </div>

    <div class="row">
        <?php echo CHtml::activeLabel($model,'FTP username:'); ?>
        <?php echo CHtml::activeTextField($model,'username') ?>
    </div>

    <div class="row">
        <?php echo CHtml::activeLabel($model,'FTP password:'); ?>
        <?php echo CHtml::activePasswordField($model,'password') ?>
    </div>

    <div class="row submit">
        <?php echo CHtml::submitButton('Save'); ?>
    </div>
    <?php echo CHtml::activeHiddenField($model,'user_id', array('value'=>Yii::app()->user->id)) ?>
    <?php echo CHtml::endForm(); ?>
</div>

