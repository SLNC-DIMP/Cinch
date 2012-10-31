<?php
$this->pageTitle=Yii::app()->name . ' - About the Tool';
$this->breadcrumbs=array(
	'About the Tool',
);
?>
<h2>CINCH Tool</h2>
<div id="cinch_image"><a href="http://cinch.nclive.org/Cinch/CINCH_workflow.pdf"><?php echo CHtml::image(Yii::app()->request->baseUrl.'/images/CINCH_workflow.png'); ?></a></div>
<p>CINCH is a web-based, open source, lightweight tool that was designed to help   libraries, archives, and agencies with similar mandates  to collect   and authenticate digital content that is freely available on the web.</p>
<p>CINCH currently works with the following file types:</p>
<ul>
  <li>PDF</li>
  <li>Microsoft Word</li>
  <li>Microsoft Excel</li>
  <li>Microsoft PowerPoint</li>
  <li>Jpeg</li>
  <li>PNG</li>
  <li>Gif</li>
  <li>Text files (e.g. files with .txt or .csv extensions)</li>
</ul>
<p>CINCH <a href="<?php echo Yii::app()->request->baseUrl; ?>/CINCHdocumentation.pdf" target="_blank">documentation</a> and
    an <a href="<?php echo Yii::app()->request->baseUrl; ?>/site/page?view=faq">FAQ</a> give more details about how CINCH works. You can also
    view the <a href="http://youtu.be/zTqLPRwNuYg" target="_blank">video tutorial</a>.  For developers, CINCH API documentation
    can be found <a href="http://cinch.nclive.org/c_docs/packages/db_Default.html">here</a>.</p>
<p>Anyone can install CINCH using the files available through <a href="http://slnc-dimp.github.com/Cinch/">GitHub</a>. North Carolina
    institutions can use a <a href="<?php echo Yii::app()->request->baseUrl; ?>/site/login">hosted
        version</a>. <a href="<?php echo Yii::app()->request->baseUrl; ?>/site/contact">Contact us</a> for more details.</p>