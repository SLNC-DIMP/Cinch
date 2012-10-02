<?php
/**
 * Mp3_Metadata class file
 *
 * Writes extracted Mp3 metadata to the database.
 * @catagory Mp3_Metadata
 * @package Mp3_Metadata
 * @author State Library of North Carolina - Digital Information Management Program <digital.info@ncdcr.gov>
 * @author Dean Farrell
 * @version 1.0
 * @license Unlicense {@link http://unlicense.org/}
 */

/**
 * Writes extracted Mp3 metadata to the database.
 * @author State Library of North Carolina - Digital Information Management Program <digital.info@ncdcr.gov>
 * @author Dean Farrell
 * @version 1.0
 * @license Unlicense {@link http://unlicense.org/}
 */
class Mp3_Metadata extends FileTypeActiveRecord {
    /**
     * Writes extracted Mp3 metadata to the database
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
            'channels' => 'channels',
            'creator' => 'creator',
            'resourceName' => 'resource_name',
            'samplerate' => 'sample_rate',
            'title' => 'title',
            'version' => 'version',
            'xmpDM:album' => 'album',
            'xmpDM:artist' => 'artist',
            'xmpDM:audioChannelType' => 'audio_channel_type',
            'xmpDM:audioCompressor' => 'audio_compressor',
            'xmpDM:audioSampleRate' => 'audio_sample_rate',
            'xmpDM:composer' => 'composer',
            'xmpDM:genre' => 'genre',
            'xmpDM:releaseDate' => 'release_date',
            'file_id' => 'file_id',
            'user_id' => 'user_id'
        );

        $actual_fields = $this->returnedFields($possible_fields, $metadata);
        $query_fields = $this->addIdInfo($actual_fields, array('file_id' => 'file_id', 'user_id' => 'user_id'));
        $full_metadata = $this->addIdInfo($metadata, array('file_id' => $file_id, 'user_id' => $user_id));
        $bind_params = $this->bindValuesBuilder($query_fields, $full_metadata);

        $sql = 'INSERT INTO Mp3_Metadata(' . $this->queryBuilder($query_fields) . ')
				VALUES(' . $this->queryBuilder($query_fields, true) . ')';

        Yii::app()->db->createCommand($sql)
            ->execute($bind_params);

    }
}