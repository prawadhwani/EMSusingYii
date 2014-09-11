<?php

/**
 * This is the model class for table "profile".
 *
 * The followings are the available columns in table 'profile':
 * @property integer $id
 * @property string $userid
 * @property string $name
 * @property string $birthdate
 * @property string $website
 * @property string $bio
 * @property string $picture
 *
 * The followings are the available model relations:
 * @property User $id0
 */
class Profile extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'profile';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array(' name, birthdate', 'required'),
			array('name, website', 'length', 'max'=>45),
			array('bio, picture', 'safe'),
            //picture uplaoding
            array('picture', 'file','types'=>'jpg, gif, png', 'allowEmpty'=>true, 'on'=>'update'),
            array('picture', 'length', 'max'=>255, 'on'=>'insert,update'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, userid, name, birthdate, website, bio, picture', 'safe', 'on'=>'search'),
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
			'id0' => array(self::BELONGS_TO, 'User', 'id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'userid' => 'Userid',
			'name' => 'Name',
			'birthdate' => 'Birthdate',
			'website' => 'Website',
			'bio' => 'Bio',
			'picture' => 'Picture',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('userid',$this->userid,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('birthdate',$this->birthdate,true);
		$criteria->compare('website',$this->website,true);
		$criteria->compare('bio',$this->bio,true);
		$criteria->compare('picture',$this->picture,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Profile the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
