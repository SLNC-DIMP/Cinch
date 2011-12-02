<?php

/**
 * This is the model class for table "file_info".
 *
 * The followings are the available columns in table 'file_info':
 * @property integer $id
 * @property string $org_file_path
 * @property string $temp_file_path
 * @property integer $file_type_id
 * @property integer $checksum_created
 * @property string $checksum
 * @property integer $virus_check
 * @property integer $dynamic_file
 * @property string $last_modified
 * @property integer $problem_file
 * @property integer $user_id
 * @property integer $upload_file_id
 *
 * The followings are the available model relations:
 * @property User $user
 * @property UserUploads $uploadFile
 */
class FileInfo extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return FileInfo the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'file_info';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('file_type_id, virus_check, dynamic_file, problem_file, user_id, upload_file_id', 'numerical', 'integerOnly'=>true),
			array('org_file_path', 'length', 'max'=>2084),
			array('temp_file_path', 'length', 'max'=>1000),
			array('checksum', 'length', 'max'=>40),
			array('last_modified', 'length', 'max'=>15),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, org_file_path, temp_file_path, file_type_id, checksum, virus_check, dynamic_file, last_modified, problem_file, user_id, upload_file_id', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'user' => array(self::BELONGS_TO, 'User', 'user_id'),
			'uploadFile' => array(self::BELONGS_TO, 'UserUploads', 'upload_file_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'org_file_path' => 'Org File Path',
			'temp_file_path' => 'Temp File Path',
			'file_type_id' => 'File Type',
			'checksum' => 'Checksum',
			'virus_check' => 'Virus Check',
			'dynamic_file' => 'Dynamic File',
			'last_modified' => 'Last Modified',
			'problem_file' => 'Problem File',
			'user_id' => 'User',
			'upload_file_id' => 'Upload File',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('org_file_path',$this->org_file_path,true);
		$criteria->compare('temp_file_path',$this->temp_file_path,true);
		$criteria->compare('file_type_id',$this->file_type_id);
		$criteria->compare('checksum',$this->checksum,true);
		$criteria->compare('virus_check',$this->virus_check);
		$criteria->compare('dynamic_file',$this->dynamic_file);
		$criteria->compare('last_modified',$this->last_modified,true);
		$criteria->compare('problem_file',$this->problem_file);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('upload_file_id',$this->upload_file_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}