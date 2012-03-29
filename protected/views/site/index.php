<?php $this->pageTitle=Yii::app()->name; ?>

<!-- <h1>Welcome to <i><?php // echo CHtml::encode(Yii::app()->name); ?></i></h1> -->
<div id="cinch_image"><?php echo CHtml::image(Yii::app()->request->baseUrl.'/images/cinch.png'); ?></div>

<div id="cinch_text">
<p>
The CINCH project will develop a tool that allows users to bulk download files that are publicly available on the internet to their local storage. The benefit of the CINCH tool is its ability to retrieve large numbers of files in a preservation friendly way.  How does CINCH work? Users:</p>
<ol>
	<li>Upload a list of URLs to files they want the CINCH tool to retrieve.</li>
	<li>Run the tool.</li>
    <li>Download the retrieved files and file metadata to their local environment.</li>
</ol>

<p>The tool will maintain original file timestamps; rename files with a unique, intelligent identifier; run virus scans; extract file-level metadata; create file checksums; validate checksums for file integrity; and track all actions in an audit trail document. With repeated uses, the CINCH tool will track file duplicates.<p>
 
<p>Funding for the CINCH: Capture, Ingest, &amp; Checksum tool is made possible through an IMLS Sparks! Ignition grant.</p>
</div>