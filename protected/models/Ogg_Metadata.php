<?php
/**
 * Ogg_Metadata class file
 *
 * Writes extracted Ogg metadata to the database.
 * @catagory Ogg_Metadata
 * @package Ogg_Metadata
 * @author State Library of North Carolina - Digital Information Management Program <digital.info@ncdcr.gov>
 * @author Dean Farrell
 * @version 1.0
 * @license Unlicense {@link http://unlicense.org/}
 */

/**
 * Writes extracted Ogg metadata to the database.
 * @author State Library of North Carolina - Digital Information Management Program <digital.info@ncdcr.gov>
 * @author Dean Farrell
 * @version 1.0
 * @license Unlicense {@link http://unlicense.org/}
 */
class Ogg_Metadata extends FileTypeActiveRecord {
    /**
     * Writes extracted Ogg metadata to the database
     * @param array $metadata
     * @param $file_id
     * @param $user_id
     * @access public
     * @return object.  Yii DAO object
     */
    public function writeMetadata(array $metadata, $file_id, $user_id) {
        $possible_fields = array(
            'Author' => 'author',
            'Content-Length' => 'content_length',
            'Content-Type' => 'content_type',
            'resourceName' => 'resource_name',
            'title' => 'title',
            'vendor' => 'vendor',
            'version' => 'version',
            'xmpDM:album' => 'album',
            'xmpDM:artist' => 'artist',
            'xmpDM:audioChannelType' => 'audio_channel_type',
            'xmpDM:audioCompressor' => 'audio_compressor',
            'xmpDM:audioSampleRate' => 'audio_sample_rate',
            'xmpDM:genre' => 'genre',
            'xmpDM:logComment' => 'comment',
            'xmpDM:releaseDate' => 'release_date',
            'file_id' => 'file_id',
            'user_id' => 'user_id'
        );

        $actual_fields = $this->returnedFields($possible_fields, $metadata);
        $query_fields = $this->addIdInfo($actual_fields, array('file_id' => 'file_id', 'user_id' => 'user_id'));
        $full_metadata = $this->addIdInfo($metadata, array('file_id' => $file_id, 'user_id' => $user_id));
        $bind_params = $this->bindValuesBuilder($query_fields, $full_metadata);

        $sql = 'INSERT INTO Ogg_Metadata(' . $this->queryBuilder($query_fields) . ')
				VALUES(' . $this->queryBuilder($query_fields, true) . ')';

        Yii::app()->db->createCommand($sql)
            ->execute($bind_params);

    }
}