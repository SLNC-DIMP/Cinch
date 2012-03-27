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

<a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by-nc-sa/3.0/88x31.png" /></a>
<br />This work is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/">Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License</a>.

-------------------------
Requirements

* Currently Cinch will only run on *nix systems
* PHP 5.3+ (PHP 5.4+ is recommended)
* MySQL or SQLite

After download, you may need to run the setup.sh shell file before initially running application.

-------------------------

Parts of Cinch include:

- Yii Framework <http://www.yiiframework.com>
- jQuery <http://jquery.com>
- jQuery UI <http://jqueryui.com>
- Apache Tika <http://tika.apache.org>
- ClamAV <http://www.clamav.net>