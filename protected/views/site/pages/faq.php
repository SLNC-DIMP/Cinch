<?php
$this->pageTitle=Yii::app()->name . ' - FAQ';
$this->breadcrumbs=array(
	'FAQ',
);
?>
<h1>FAQ</h1>
<h3>How can I use CINCH?</h3>
<p>If you are a library, archives, or other cultural heritage institution in North Carolina, you can use the hosted version of CINCH without installing it locally.
Please <a href="<?php echo Yii::app()->request->baseUrl; ?>/site/contact">contact us</a> and we'll set you up with an account.  </p>
<p>If you are not one of these institutions or would prefer to host CINCH
  yourself, please see our <a href="http://slnc-dimp.github.com/Cinch/">GitHub site</a> for instructions on installing CINCH.</p>

<h3>How do I create an upload list?</h3>
<p>Simply create a file with a .txt or .csv extension, placing the full url for each file to download on its own line like so:</p>
<ul id="no_bullets">
	<li>http://mysite.gov/pdfs/important.pdf</li>
    <li>https://agency.org/files/2012/my_file.docx</li>
    <li>http://anothersite.gov/docs/another_file.doc</li>
</ul>
<p>You may want to use a website crawler or sitemap generator to facilitate this process.</p>
<h3>Why can my upload list only have 10,000 files listed for CINCH to collect?</h3>
<p>Our server has problems dealing with a large number files at one pass.  However feel free to upload as many lists as you like.  We'll get to them all.</p>

<h3>Should I save a copy of my upload list(s)?</h3>
<p>Yes, if you want to compare your upload list(s) with the file manifest contained in each zip file.  Note:  the files from any particular list might be split up over multiple zip files.</p>

<h3>Why don't all the files on my list(s) appear in the same zip file?</h3>
<p>There are several reasons this might happen:</p>
<ul>
	<li>A limit has been set on how large zip files can be and still be downloaded by our system.  This necessiates splitting your files across multiple zip files if their total size is greater than 0.5GB.</li>
    <li>We have a  limit on how many total files CINCH can process at a given time, which means your files might need to be split up.</li>
</ul>

<h3>Why are my files deleted after 30 days?</h3>
<p>Simply put, we don't have enough room on our server to keep everyone's files in perpetuity.  You will receive an email 20 days after your files are processed notifying you that your files will be deleted soon.</p>

<h3>What is the life cycle of my files in CINCH?</h3>
<p>Here's a high level overview of what happens after you upload your url list:</p>
<ol>
	<li>Each url from your list is written to a database for processing.</li>
    <li>Each file is downloaded.</li>
    <li>Each file is run through a virus check.</li>
    <li>Each file's checksum is generated.</li>
    <li>Each file's metadata is extracted.</li>
    <li>Each file's metadata is added to a metadata.csv file for inclusion in a zip file.</li>
    <li>Any files with errors have their error information added to an error.csv for inclusion in a zip file.</li>
    <li>Each file is added to a zip file along with the metadata.csv file, errors.csv file, and a file_events_history.csv file.</li>
    <li>You receive a message that you have files are ready for download.</li>
    <li>After 20 days you receive an email that your files will be deleted soon.</li>
    <li>After 30 days your files are deleted from the system.</li>
</ol>
<p>For more information, you may want to review the <a href="<?php echo Yii::app()->request->baseUrl; ?>/CINCHdocumentation.pdf" target="_blank">documentation</a> or the overview video.</p>
