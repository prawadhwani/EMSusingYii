<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class RegisterForm extends CFormModel
{
    public $username;
    public $password;
    public $email;

    private $_identity;

    /**
     * Declares the validation rules.
     * The rules state that username and password are required,
     * and password needs to be authenticated.
     */
    public function rules()
    {
        return array(
            // username, email and password are required
            array('username, password, email', 'required'),
            //email and pass are unique
            array('username, email', 'unique', 'className'=>'User'),
            array('email','email'),
        );
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return array(
            'username'=>'Your username',
            'password'=>'Your password',
            'email'=>'Your email',
        );
    }
}
