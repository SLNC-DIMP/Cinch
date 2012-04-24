A project to develop a bulk download service to a central repository 
that will maintain original file timestamps, extract file level metadata, 
create file checksums and periodically validate checksums for continued file integrity. 

Users merely need to upload a list of URLs to download and 
when the process completes they can download the requested files and file metadata 
to their local environment.

Currently supported file types:
 
 * PDF
 * Microsoft Word
 * Microsoft Excel
 * Microsoft PowerPoint
 * Jpeg
 * PNG
 * GIF
 * Text (e.g. files with .txt, .csv extensions, etc.)

Learn more at: http://digitalpreservation.ncdcr.gov/cinch/.

Funding for the CINCH: Capture, Ingest, & Checksum tool is made possible through 
an IMLS Sparks! Ignition grant.

<p xmlns:dct="http://purl.org/dc/terms/" xmlns:vcard="http://www.w3.org/2001/vcard-rdf/3.0#">
  <a rel="license"
     href="http://creativecommons.org/publicdomain/zero/1.0/">
    <img src="http://i.creativecommons.org/p/zero/1.0/80x15.png" style="border-style: none;" alt="CC0" />
  </a>
  <br />
  To the extent possible under law,
  <span rel="dct:publisher" resource="[_:publisher]">the person who associated CC0</span>
  with CINCH has waived all copyright and related or neighboring rights to
  <span property="dct:title">CINCH</span>.
This work is published from the:
<span property="vcard:Country" datatype="dct:ISO3166"
      content="US" about="[_:publisher]">
  United States</span>.
</p>

-------------------------
Requirements

* Currently Cinch will only run on *nix systems
* PHP 5.3+ (PHP 5.4+ is recommended)
* MySQL
* ClamAV

After download, you may need to run the setup.sh shell file before initially running application to set file level permissions.

Setting up Cinch on your system:

<ol>
<li>Place the Cinch files in a web accessible directory</li>
<li>Create a new database MySQL database and import the project.sql file into it.</li>
<li>Open protected/config/main.php</li>
<li>Scroll down to the db settings (line 68 or so). Set the database name, the username and password for your new Cinch database.</li>
<li>Scroll to the bottom of main.php and set 'adminEmail' email address to your email address.
<li>You'll now need to repeat step four in protected/config/console.php.
<li>Go to http://tika.apache.org/download.html and download the Apache Tika jar file.</li>
<li>Place the Apache Tika jar file at the root of the Cinch/protected directory.</li>
<li>Configure Cinch cron tasks.  See the sample cron.txt file the root of Cinch for suggestions on how you might want to configure it.
</ol>
You should now be able to login to the web interface as: admin admin.
You should then go the change password tab and update your password.

If you don't want to run Cinch via cron you can run it from the command line.  If you navigate to Cinch/protected and run the following: path/to/php yiic.php you should be presented with a list of available commands.  The general way to run a command is: path/to/php yiic.php command.
Several commands such as checksum and purgeystem have subcommands, which have to be run like so from the command line: path/to/php yiic.php command sub-command.

You should run the commands in the following order:
<ol>
<li>readfile</li>
<li>download</li>
<li>viruscheck</li>
<li>checksum create</li>
<li>metadata</li>
<li>metadatacsv</li>
<li>errorcsv</li>
<li>zipcreation</li>
<li>purgesystem check (optional, Notifies users after 20 days that they have files marked for deletion in 10 days.)</li>
<li>purgesystem delete (optional, deletes user files older than 30 days old.  Note this deletes upload lists, and all csv file information from the database, but downloaded files, metdata, errors, and event, information is retained.)</li>
</ol>

Useful Notes:  

* Uploaded url lists are saved into protected/uploads/"user's username". With the user's directory being created on first upload and being deleted thereafter if it's empty.
* Downloaded user files are saved  into protected/curl_downloads/"user's username". With the user's directory being created on first file downloaded and being deleted thereafter if it's empty.

If you want to run the unit tests you'll need to have Pear, PHPUnit and vfsStream installed.

Adding PHPUnit:
<ol>
	<li>pear config-set auto_discover 1</li>
	<li>pear install pear.phpunit.de/PHPUnit</li>
	<li>pear install phpunit/DbUnit</li>
</ol>

Adding vfsStream:
<ol>
	<li>pear channel-discover pear.php-tools.net</li>
	<li>pear install pat/vfsStream-beta</li>
</ol>

Then navigate to Cinch/protected/test and run the following command:  phpunit unit 

-------------------------

Parts of Cinch include:

- Yii Framework <http://www.yiiframework.com>
- jQuery <http://jquery.com>
- jQuery UI <http://jqueryui.com>
- Apache Tika <http://tika.apache.org>
- ClamAV <http://www.clamav.net>