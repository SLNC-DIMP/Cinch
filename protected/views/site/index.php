<?php $this->pageTitle=Yii::app()->name; ?>

<!-- <h1>Welcome to <i><?php // echo CHtml::encode(Yii::app()->name); ?></i></h1> -->
<div id="cinch_image"><?php echo CHtml::image(Yii::app()->request->baseUrl.'/images/cinch.png'); ?></div>

<div id="cinch_text">
<p>
CINCH  (Capture INgest CHecksum) is a tool that automates the transfer of online  content to a repository, using ingest technologies appropriate for digital  preservation.</p>
<p>How does CINCH work? Users:</p>
<ol>
	<li>Upload a list of URLs to files they want CINCH to retrieve.</li>
	<li>Run the tool.</li>
    <li>Download the retrieved files and file metadata to their local environment.</li>
</ol>

<p>The tool will maintain original file timestamps; rename files with unique, intelligent identifiers; run virus scans; extract file-level metadata; create file checksums; validate checksums for file integrity; and track all actions in an audit trail document. With repeated uses, the CINCH tool will track file duplicates.
<h3 align="center"><a href="http://slnc-dimp.github.com/Cinch/">GET CINCH</a> | <a href="<?php echo Yii::app()->request->baseUrl; ?>/site/login">USE CINCH</a>  | <a href="<?php echo Yii::app()->request->baseUrl; ?>/CINCHdocumentation.pdf" target="_blank">Documentation</a> (.pdf)</h3>
<p>Funding for the CINCH: Capture, Ingest, &amp; Checksum tool is made possible through an IMLS Sparks! Ignition grant.</p>
</div>