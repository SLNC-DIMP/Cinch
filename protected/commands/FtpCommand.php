<?php
class FtpCommand extends CConsoleCommand {
    public function getFtp() {
        return Yii::app()->db->createCommand()
                ->select('*')
                ->from('ftp')
                ->where('processed=0')
                ->limit(2)
                ->queryAll();
    }

    public function ftpConnect($path, $port=21) {
        return ftp_connect($path, $port);
    }

    public function ftpClose($connection) {
        return ftp_close($connection);
    }

    public function login($connection, $user, $password) {
        return @ftp_login($connection, $user, $password);
    }

    /**
     * Recursively lists the raw ftp output.
     * @param $connection
     * @param string $directory
     * @return array
     */
    public function listFiles($connection, $directory = '/') {
        return ftp_rawlist($connection, $directory, true);
    }

    /**
     * See comment by vijay dot mahrra at fronter dot com
     * at http://php.net/manual/en/function.ftp-nlist.php
     * ftpRecursiveFileListing
     *
     * Get a recursive listing of all files in all subfolders given an ftp handle and path
     *
     * @param resource $ftpConnection  the ftp connection handle
     * @param string $path  the folder/directory path
     * @return array $allFiles the list of files in the format: directory => $filename
     * @author Niklas Berglund
     * @author Vijay Mahrra
     */
    public function ftpRecursiveFileListing($connection, $path = '/') {
        static $allFiles = array();
        $contents = ftp_nlist($connection, $path);

        foreach($contents as $currentFile) {
            // assuming its a folder if there's no dot in the name
            if (strpos($currentFile, '.') === false) {
                ftpRecursiveFileListing($connection, $currentFile);
            }
            $allFiles[$path][] = substr($currentFile, strlen($path) + 1);
        }
        return $allFiles;
    }

    public function getRemoteFile($connection, $remote_file) {
        $local_file = '';
        ftp_nb_fget($connection, $local_file, $remote_file, FTP_BINARY);
    }

    public function run() {
        $ftps = $this->getFtp();
        if(empty($ftps)) { echo "No FTP sites to process\n"; exit; }

        foreach($ftps as $ftp) {
            $fh = ftp_connect($ftp['path'], $ftp['port']);

            if($ftp && @ftp_login($fh, $ftp['username'], $ftp['password'])) {
                ftp_pasv($fh, true);

                $files = $this->ftpRecursiveFileListing($fh, $ftp['path']);

                foreach($files as $file) {
                    $this->getRemoteFile($fh, $file);
                }

                ftp_close($fh);
            } else {
                echo 'FTP connection error';
            }


        }
    }
}