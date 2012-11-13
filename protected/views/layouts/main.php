<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
	<![endif]-->

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/cinch.css" />

    <?php Yii::app()->clientScript->registerCoreScript('jquery');  ?>

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>

<div class="container" id="page">

	<div id="header">
    	<div><?php echo CHtml::image(Yii::app()->request->baseUrl.'/images/cinch-header.png'); ?></div>
	<!--	<div id="logo"><?php // echo CHtml::encode(Yii::app()->name); ?></div> -->
        
	</div><!-- header -->

	<div id="mainmenu">
    	<?php
			$menu_items = array(
				array('label'=>'Home', 'url'=>array('/site/index')),
				array('label'=>'About the Tool', 'url'=>array('/site/page', 'view'=>'about')),
				array('label'=>'About the Project', 'url'=>array('/site/page', 'view'=>'project')),
				array('label'=>'FAQ', 'url'=>array('/site/page', 'view'=>'faq')),
				array('label'=>'Contact', 'url'=>array('/site/contact')),
			);
			if(!Yii::app()->user->isGuest) {
				$menu_items[] =  array('label'=>'Upload', 'url'=>array('/upload'));
                $menu_items[] =  array('label'=>'FTP', 'url'=>array('/ftpSites'));
				$menu_items[] =  array('label'=>'Download Files', 'url'=>array('/zipGzDownloads'));
				$menu_items[] =  array('label'=>'Change Password', 'url'=>array('/user/pass/' . Yii::app()->user->id));
			}
			if(Yii::app()->authManager->checkAccess('Admin', Yii::app()->user->id)) {
				$menu_items[] =  array('label'=>'Admin', 'url'=>array('/admin'));
			}
			$menu_items[] = array('label'=>'Login', 'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest);
			$menu_items[] = array('label'=>'Logout ('.Yii::app()->user->name.')', 'url'=>array('/site/logout'), 
			'visible'=>!Yii::app()->user->isGuest);
		?>
		
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>$menu_items)); ?>
	</div><!-- mainmenu -->
	<?php if(isset($this->breadcrumbs)):?>
		<?php $this->widget('zii.widgets.CBreadcrumbs', array(
			'links'=>$this->breadcrumbs,
		)); ?><!-- breadcrumbs -->
	<?php endif?>

	<?php echo $content; ?>

	<div id="footer">
    <?php echo CHtml::link(CHtml::image(Yii::app()->request->baseUrl.'/images/footerncdcr75.png'), "http://www.ncdcr.gov/", array('target'=>'_blank')); ?>
    <?php echo CHtml::link(CHtml::image(Yii::app()->request->baseUrl.'/images/footerslnc75.png'), "http://statelibrary.ncdcr.gov/", array('target'=>'_blank')); ?>
    <?php echo CHtml::link(CHtml::image(Yii::app()->request->baseUrl.'/images/footernclive75.png'), "http://www.nclive.org/", array('target'=>'_blank')); ?>
    <?php echo CHtml::link(CHtml::image(Yii::app()->request->baseUrl.'/images/IMLS_Logo.png'), "http://www.imls.gov/", array('target'=>'_blank')); ?>
	</div><!-- footer -->

</div><!-- page -->
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-1152148-28']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</body>
</html>