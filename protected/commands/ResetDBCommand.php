<?php
/**
 * USE THIS ONLY WHEN TESTING. VERY BAD THINGS COULD HAPPEN TO YOUR LIVE DATA OTHERWISE!!!!
 */
class ResetDBCommand extends CConsoleCommand {
    /**
     * Clears database tables of all file information
     */
    public function actionClearDB() {
        $tables = array('csv_meta_paths', 'Excel_Metadata', 'files_for_download', 'file_event_history', 'file_info',
            'Gif_Metadata', 'Jpg_Metadata','Mp3_Metadata', 'Mp4_Metadata', 'Ogg_Metadata', 'PDF_Metadata', 'Mp3_Metadata',
            'Mp4_Metadata', 'Ogg_Metadata', 'PNG_Metadata', 'problem_files', 'PPT_Metadata', 'QuickTime_Metadata',
            'Text_Metadata', 'upload', 'Word_Metadata', 'zip_gz_downloads');

        /* Just resets uploads and lists to download */
        //$tables = array('files_for_download', 'upload');

        foreach($tables as $table) {
            $sql = "TRUNCATE TABLE $table";
            $truncated = Yii::app()->db->createCommand($sql);
            $truncated->execute();

            if($truncated) {
                echo $table . " cleared\n";
            } else {
                echo $table . " not cleared\n";
            }
        }
    }

    /**
     * Clears out all downloaded and generated files for default 'admin' user.
     * Change the $dirs array values to match the user names of the users you want to delete.
     */
    public function actionClearFiles() {
        $dirs = array('admin');
        $cmd = 'rm -rf ';

        foreach($dirs as $dir) {
          $downloads = shell_exec(escapeshellcmd($cmd . Yii::getPathOfAlias('application.curl_downloads').'/'.$dir));
          if($downloads == 0) {
              echo "Downloaded files for $dir deleted.\n";
          } else {
              echo "Downloaded files for $dir couldn't be deleted!  Please manually delete them.\n";
          }
          $uploads = shell_exec(escapeshellcmd($cmd . Yii::getPathOfAlias('application.uploads').'/'.$dir));
          if($uploads == 0) {
              echo "Upload lists for $dir deleted.\n";
          } else {
              echo "Upload lists for $dir couldn't be deleted!  Please manually delete them.\n";
          }
        }
    }
}