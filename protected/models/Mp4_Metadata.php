<?php
    /**
     * Mp4_Metadata class file
     *
     * Writes extracted mp4 metadata to the database.
     * @catagory Mp4_Metadata
     * @package Mp4_Metadata
     * @author State Library of North Carolina - Digital Information Management Program <digital.info@ncdcr.gov>
     * @author Dean Farrell
     * @version 1.0
     * @license Unlicense {@link http://unlicense.org/}
     */

    /**
     * Writes extracted mp4 metadata to the database.
     * @author State Library of North Carolina - Digital Information Management Program <digital.info@ncdcr.gov>
     * @author Dean Farrell
     * @version 1.0
     * @license Unlicense {@link http://unlicense.org/}
     */
class Mp4_Metadata extends FileTypeActiveRecord {
    /**
     * Writes extracted mp4 metadata to the database
     * @param array $metadata
     * @param $file_id
     * @param $user_id
     * @access public
     * @return object.  Yii DAO object
     */
    public function writeMetadata(array $metadata, $file_id, $user_id) {
        $possible_fields = array(
            'Content-Length' => 'content_length',
            'Content-Type' => 'content_type',
            'Creation-Date' => 'creation_date',
            'Last-Modified' => 'last_modified',
            'Last-Save-Date' => 'last_save_date',
            'resourceName' => 'resource_name',
            'tiff:ImageLength' => 'tiff_image_length',
            'tiff:ImageWidth' => 'tiff_image_width',
            'xmpDM:audioChannelType' => 'audio_channel_type',
            'xmpDM:audioSampleRate' => 'audio_sample_rate',
            'file_id' => 'file_id',
            'user_id' => 'user_id'
        );

        $actual_fields = $this->returnedFields($possible_fields, $metadata);
        $query_fields = $this->addIdInfo($actual_fields, array('file_id' => 'file_id', 'user_id' => 'user_id'));
        $full_metadata = $this->addIdInfo($metadata, array('file_id' => $file_id, 'user_id' => $user_id));
        $bind_params = $this->bindValuesBuilder($query_fields, $full_metadata);

        $sql = 'INSERT INTO Mp4_Metadata(' . $this->queryBuilder($query_fields) . ')
				VALUES(' . $this->queryBuilder($query_fields, true) . ')';

        Yii::app()->db->createCommand($sql)
            ->execute($bind_params);

    }
}