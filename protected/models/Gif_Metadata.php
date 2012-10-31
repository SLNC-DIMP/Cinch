<?php
/**
* Gif_Metadata class file
*
* Writes extracted Gif image metadata to the database.
* @category Gif_Metadata
* @package Gif_Metadata
* @author State Library of North Carolina - Digital Information Management Program <digital.info@ncdcr.gov>
* @author Dean Farrell
* @version 1.0
* @license Unlicense {@link http://unlicense.org/}
*/

/**
* Writes extracted Gif image metadata to the database.
* @author State Library of North Carolina - Digital Information Management Program <digital.info@ncdcr.gov>
* @author Dean Farrell
* @version 1.0
* @license Unlicense {@link http://unlicense.org/}
*/
class Gif_Metadata extends FileTypeActiveRecord {
	/**
	* Writes extracted Gif image metadata to the database
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
			"Data SampleFormat" => "data_sample_format",
			"Dimension HorizontalPixelOffset" => "horizontal_pixel_offset",
			"Dimension ImageOrientation" => "orientation",
			"Dimension VerticalPixelOffset" => "vertical_pixel_offset",
			"GraphicControlExtension" => "graphic_control_extension",
			"ImageDescriptor" => "image_descriptor",
			"height" => "height",
			"resourceName" => "file_name",
			"width" => "width",
			'file_id' => 'file_id',
			'user_id' => 'user_id'
		);
		
		$actual_fields = $this->returnedFields($possible_fields, $metadata);
		$query_fields = $this->addIdInfo($actual_fields, array('file_id' => 'file_id', 'user_id' => 'user_id'));
		$full_metadata = $this->addIdInfo($metadata, array('file_id' => $file_id, 'user_id' => $user_id));
		$bind_params = $this->bindValuesBuilder($query_fields, $full_metadata);
		
		$sql = 'INSERT INTO Gif_Metadata(' . $this->queryBuilder($query_fields) . ') 
			VALUES(' . $this->queryBuilder($query_fields, true) . ')';
		
		$write_files = Yii::app()->db->createCommand($sql);
		$done = $write_files->execute($bind_params);
	}
}