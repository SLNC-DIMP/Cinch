<?php
/**
* PNG_Metadata class file
*
* Writes extracted PNG image metadata to the database.
* @catagory PNG_Metadata
* @package PNG_Metadata
* @author State Library of North Carolina - Digital Information Management Program <digital.info@ncdcr.gov>
* @author Dean Farrell
* @version 1.0
* @license CC0 1.0 Universal {@link http://creativecommons.org/publicdomain/zero/1.0/}
*/

/**
* Writes extracted PNG image metadata to the database.
* @author State Library of North Carolina - Digital Information Management Program <digital.info@ncdcr.gov>
* @author Dean Farrell
* @version 1.0
* @license CC0 1.0 Universal {@link http://creativecommons.org/publicdomain/zero/1.0/}
*/
class PNG_Metadata extends FileTypeActiveRecord {
	/**
	* Writes extracted PNG image metadata to the database
	* @param array $metadata
	* @param $file_id
	* @param $user_id
	* @access public
	* @return object.  Yii DAO object
	*/
	public function writeMetadata(array $metadata, $file_id, $user_id) {
		$possible_fields = array(
			"Chroma BlackIsZero" => "black_is_zero",
			"Chroma ColorSpaceType" => "color_space_type",
			"Chroma NumChannels" => "num_channels",
			"Compression CompressionTypeName" => "compression_type",
			"Compression Lossless" => "lossless_compression",
			"Compression NumProgressiveScans" => "compression_num_progressive_scans",
			"Content-Length" => "file_size",
			"Data BitsPerSample" => "bits_per_sample",
			"Data PlanarConfiguration" => "planar_configuration",
			"Data SampleFormat" => "data_sample_format",
			"Dimension ImageOrientation" => "orientation",
			"Dimension PixelAspectRatio" => "pixel_aspect_ratio",
			"Dimension VerticalPixelSize" => "vertical_pixel_size",
			"Dimension HorizontalPixelSize" => "horizontal_pixel_size",
			"IHDR" => "ihdr",
			"Text TextEntry" => "text_entry",
			"Transparency Alpha" => "transparency_alpha",
			"height" => "height",
			"pHYs" => "phys",
			"resourceName" => "file_name",
			"width" => "width",
			'file_id' => 'file_id',
			'user_id' => 'user_id'
		);
		
		$actual_fields = $this->returnedFields($possible_fields, $metadata);
		$query_fields = $this->addIdInfo($actual_fields, array('file_id' => 'file_id', 'user_id' => 'user_id'));
		$full_metadata = $this->addIdInfo($metadata, array('file_id' => $file_id, 'user_id' => $user_id));
		$bind_params = $this->bindValuesBuilder($query_fields, $full_metadata);
		
		$sql = 'INSERT INTO PNG_Metadata(' . $this->queryBuilder($query_fields) . ') 
			VALUES(' . $this->queryBuilder($query_fields, true) . ')';
		
		$write_files = Yii::app()->db->createCommand($sql);
		$done = $write_files->execute($bind_params);
	}
}