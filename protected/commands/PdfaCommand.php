<?php
// See http://ghostscript.com/doc/current/Ps2pdf.htm#PDFA
// See http://ghostscript.com/pipermail/gs-devel/2009-February/008256.html
// See http://ghostscript.com/doc/current/Ps2pdf.htm#PDFA
class PdfaCommand extends CConsoleCommand {
	/**
	* Converts PDF files to PDF/A files
	* Will ignore non-compliant features.  So certian things, watermarks, for example, will be missing.
	* @param $pdf_path
	* @return integer
	*/
	public function createPdfa($pdf_path) {
		$command = 'gs -dPDFA -dBATCH -dNOPAUSE -dNOOUTERSAVE -dUseCIEColor -sPDFACompatibilityPolicy=1 -sProcessColorModel=DeviceCMYK -sDEVICE=pdfwrite -sOutputFile=out-' . $pdf_path . ' PDFA_def.ps ' . $pdf_path;
		passthru(escapeshellcmd($command), $retval);
		
		return $retval;
	}
	
	public function run() {
		$pdfas = '';
		if(empty($pdfas)) { echo "No files to convert to pdfa\n"; exit; }
		
		foreach($pdfas as $pdfa) {
			$created = $this->createPdfa($pdfa['temp_file_path']);
			if($created == 1) { 
			
			}
		}
	}
}