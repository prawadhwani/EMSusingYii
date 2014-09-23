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
			array(' name, birthdate, picture', 'required'),
			array('name, website', 'length', 'max'=>45),
			array('bio, picture', 'safe'),
            //picture uplaoding
            array('picture', 'file','types'=>'jpg, gif, png', 'allowEmpty'=>true, 'on'=>'update'),
            array('picture', 'length', 'max'=>255, 'on'=>'insert,update'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, birthdate, website, bio, picture', 'safe', 'on'=>'search'),
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
			'id' => array(self::HAS_ONE, 'User', 'id'),
            'leaves' => array(self::HAS_MANY, 'Leave', 'user_id','condition'=>'leaves.status='.Leave::STATUS_APPROVED),
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

    protected function beforeSave()
    {
        if(parent::beforeSave())
        {
            if($this->isNewRecord)
            {
                $this->id=Yii::app()->user->id;
            }
            return true;
        }
        else
            return false;
    }

    public function associateUserToRole($role, $userId)
    {
        $sql = "INSERT INTO tbl_profile_user_role (profile_id, user_id, role) VALUES (:profileId, :userId, :role)";
        $command = Yii::app()->db->createCommand($sql);
        $command->bindValue(":profileId", $this->id, PDO::PARAM_INT);
        $command->bindValue(":userId", $userId, PDO::PARAM_INT);
        $command->bindValue(":role", $role, PDO::PARAM_STR);
        return $command->execute();
    }

    public function removeUserFromRole($role, $userId)
    {
        $sql = "DELETE FROM tbl_profile_user_role WHERE profile_id=:profileId AND user_id=:userId AND role=:role";
        $command = Yii::app()->db->createCommand($sql);
        $command->bindValue(":profileId", $this->id, PDO::PARAM_INT);
        $command->bindValue(":userId", $userId, PDO::PARAM_INT);
        $command->bindValue(":role", $role, PDO::PARAM_STR);
        return $command->execute();
    }

    public function isUserInRole($role)
    {
        $sql = "SELECT role FROM tbl_profile_user_role WHERE profile_id=:profileId AND user_id=:userId AND role=:role";
        $command = Yii::app()->db->createCommand($sql);
        $command->bindValue(":profileId", $this->id, PDO::PARAM_INT);
        $command->bindValue(":userId", Yii::app()->user->getId(),
            PDO::PARAM_INT);
        $command->bindValue(":role", $role, PDO::PARAM_STR);
        return $command->execute()==1 ? true : false;
    }

    public static function getUserRoleOptions()
    {
        return CHtml::listData(Yii::app()->authManager->getRoles(),'name', 'name');
    }

    public function associateUserToProfile($user)
    {
        $sql = "INSERT INTO tbl_profile_user_assignment (profile_id, user_id) VALUES (:profileId, :userId)";
        $command = Yii::app()->db->createCommand($sql);
        $command->bindValue(":profileId", $this->id, PDO::PARAM_INT);
        $command->bindValue(":userId", $user->id, PDO::PARAM_INT);
        return $command->execute();
    }

    public function isUserInProfile($user)
    {
        $sql = "SELECT user_id FROM tbl_profile_user_assignment WHERE profile_id=:profileId AND user_id=:userId";
        $command = Yii::app()->db->createCommand($sql);
        $command->bindValue(":profileId", $this->id, PDO::PARAM_INT);
        $command->bindValue(":userId", $user->id, PDO::PARAM_INT);
        return $command->execute()==1 ? true : false;
    }

    public function addLeave($leave)
    {
        if(Yii::app()->params['leaveNeedApproval'])
            $leave->status=Leave::STATUS_PENDING;
        else
            $leave->status=Leave::STATUS_APPROVED;
        $leave->user_id=$this->id;
        return $leave->save();
    }


}
