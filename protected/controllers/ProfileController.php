<?php

class ProfileController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
    public function accessRules()
    {
        return array(
            array('allow',  // allow all users to perform 'list' and 'show' actions
                'actions'=>array('index', 'view', 'adduser'),
                'users'=>array('@'),
            ),
            array('allow', // allow authenticated users to perform any action
                'users'=>array('@'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    /**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView()
	{
        $profile=$this->loadModel();
        $leave = $this->newLeave($profile);
        //$uid= Yii::app()->user->id;
        $this->render('view',array(
            'model'=>$profile,
            'leave'=>$leave,
        ));

	}

    protected function newLeave($profile)
    {
        $leave=new Leave;
        if(isset($_POST['Leave']))
        {
            $leave->attributes=$_POST['Leave'];
            if($profile->addLeave($leave))
            {
                if($leave->status==Leave::STATUS_PENDING)
                    Yii::app()->user->setFlash('leaveSubmitted','Your leave request will be posted to your manager.');
                $this->refresh();
            }
        }
        return $leave;
    }

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Profile;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Profile']))
		{
            $model->attributes=$_POST['Profile'];
            $rnd = rand(0,9999);
            $uploadedFile=CUploadedFile::getInstance($model,'picture');
            $fileName = "{$rnd}-{$uploadedFile}";  // random number + file name
            $model->picture = $fileName;

			if($model->save())
            {
                $uploadedFile->saveAs(Yii::app()->basePath.'/image/'.$fileName);
                $this->redirect(array('view','id'=>$model->id));
            }

		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
        //$uid = Yii::app()->user->id;
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Profile']))
		{
            $_POST['Profile']['picture'] = $model->picture;
			$model->attributes=$_POST['Profile'];
            $uploadedFile=CUploadedFile::getInstance($model,'picture');

            if($model->save())
            {
                if(!empty($uploadedFile))
                {
                    $uploadedFile->saveAs(Yii::app()->basePath.'/image/'.$model->picture);
                }
                $this->redirect(array('view','id'=>$model->id));
            }

		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
        //$uid = Yii::app()->user->id;
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
    */

	public function actionIndex()
	{
        $profile = $this->loadModel();
        if(!Yii::app()->user->checkAccess('createUser', array('profile'=>$profile)))
        {
            $this->forward('view');
        }
        else
        {
            $dataProvider=new CActiveDataProvider('Profile');
            $this->render('index',array(
                'dataProvider'=>$dataProvider,
            ));
        }

	}


	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Profile('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Profile']))
			$model->attributes=$_GET['Profile'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Profile the loaded model
	 * @throws CHttpException
	 */
	public function loadModel()
	{
		$model=Profile::model()->findByPk(Yii::app()->user->id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Profile $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='profile-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

    public function actionAdduser()
    {
        $form = new ProfileUserForm;
        $profile = $this->loadModel();

        if(!Yii::app()->user->checkAccess('createUser', array('profile'=>$profile)))
        {
            throw new CHttpException(403, 'You are not authorized to view this page');
        }

        //collect user data
        if(isset($_POST['ProfileUserForm']))
        {
            $form->attributes = $_POST['ProfileUserForm'];
            $form->profile = $profile;

            //validate user input
            if($form->validate())
            {
                Yii::app()->user->setFlash('success', $form->username . "has been added as a new manager");
                $form = new ProfileUserForm;
            }
        }

        //display the adduser form
        $users = User::model()->findAll();
        $usernames = array();
        foreach($users as $user)
        {
            $usernames[]=$user->username;
        }
        $form->profile = $profile;
        $this->render('adduser', array('model'=>$form, 'usernames'=>$usernames));
    }
}
