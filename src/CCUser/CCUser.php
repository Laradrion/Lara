<?php

/**
 * A user controller  to manage login and view edit the user profile.
 * 
 * @package LaraCMF
 */
class CCUser extends CObject implements IController {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Show profile information of the user.
     */
    public function Index() {
        $this->views->SetTitle('User Profile');
        $this->views->AddInclude('/User/index.tpl.php', array(
            'is_authenticated' => $this->user->IsAuthenticated(),
            'user' => $this->user->GetUserProfile(),
        ));
    }

    /**
     * Show profile information of the user.
     */
    public function Profile() {
        $form = new CFormUserProfile($this, $this->user);
        $status = $form->Check();

        $this->views->SetTitle('User Profile');
        $this->views->AddInclude('User/profile.tpl.php', array(
            'is_authenticated' => $this->user['isAuthenticated'],
            'user' => $this->user->GetUserProfile(),
            'profile_form' => $form->GetHTML(),
        ));
    }

    /**
     * Change the password.
     */
    public function DoChangePassword($form) {
        if ($form['password']['value'] != $form['password1']['value'] || empty($form['password']['value']) || empty($form['password1']['value'])) {
            $this->session->AddMessage('error', 'Password does not match or is empty.');
        } else {
            $ret = $this->user->ChangePassword($form['password']['value']);
            $this->session->AddMessage($ret, 'Saved new password.', 'Failed updating password.');
        }
        $this->RedirectToController('profile');
    }

    /**
     * Save updates to profile information.
     */
    public function DoProfileSave($form) {
        $this->user['name'] = $form['name']['value'];
        $this->user['email'] = $form['email']['value'];
        $ret = $this->user->Save();
        $this->session->AddMessage($ret, 'Saved profile.', 'Failed saving profile.');
        $this->RedirectToController('profile');
    }

    /**
     * Authenticate and login a user.
     */
    public function Login($akronymOrEmail = null, $password = null) {
        //$this->user->Login($akronymOrEmail, $password);
        //$this->RedirectTo("user/profile");
        $form = new CForm(array(), array(
            'acronym' => array('label' => 'Acronym or email:', 'type' => 'text', 'required' => true,
                'validation' => array('not_empty')),
            'password' => array('label' => 'Password:', 'type' => 'password', 'required' => true,
                'validation' => array('not_empty')),
            'doLogin' => array('value' => 'Login', 'type' => 'submit', 'callback' => array($this, 'DoLogin'))));
        if ($form->Check() === false) {
            $this->session->AddMessage('notice', 'Some fields did not validate and the form could not be processed.');
            $this->RedirectToController('login');
        }

        $this->views->SetTitle('Login');
        $this->views->AddInclude('User/login.tpl.php', array(
            'login_form' => $form,
            'allow_create_user' => CLara::Instance()->config['create_new_users'],
            'create_user_url' => $this->request->CreateUrl(null, 'create'),
        ));
    }

    /**
     * Create a new user.
     */
    public function Create() {
        $form = new CForm(array(), array(
            'acronym' => array('label' => 'Acronym:', 'type' => 'text', 'required' => true,
                'validation' => array('not_empty')),
            'password' => array('label' => 'Password:', 'type' => 'password', 'required' => true,
                'validation' => array('not_empty')),
            'password1' => array('label' => 'Password again:', 'type' => 'password', 'required' => true,
                'validation' => array('not_empty')),
            'name' => array('label' => 'Name:', 'type' => 'text', 'required' => true,
                'validation' => array('not_empty')),
            'email' => array('label' => 'Email:', 'type' => 'text', 'required' => true,
                'validation' => array('not_empty')),
            'doCreate' => array('value' => 'Create', 'type' => 'submit', 'callback' => array($this, 'DoCreate'))));
        if ($form->Check() === false) {
            $this->session->AddMessage('notice', 'You must fill in all values.');
            $this->RedirectToController('Create');
        }
        $this->views->SetTitle('Create user');
        $this->views->AddInclude('User/create.tpl.php', array('form' => $form->GetHTML()));
    }

    /**
     * Perform a creation of a user as callback on a submitted form.
     *
     * @param $form CForm the form that was submitted
     */
    public function DoCreate($form) {
        if ($form['password']['value'] != $form['password1']['value'] || empty($form['password']['value']) || empty($form['password1']['value'])) {
            $this->session->AddMessage('error', 'Password does not match or is empty.');
            $this->RedirectToController('create');
        } else if ($this->user->Create($form['acronym']['value'], $form['password']['value'], $form['name']['value'], $form['email']['value']
                )) {
            $this->session->AddMessage('success', "Welcome {$this->user['name']}. Your have successfully created a new account.");
            $this->user->Login($form['acronym']['value'], $form['password']['value']);
            $this->RedirectToController('profile');
        } else {
            $this->session->AddMessage('notice', "Failed to create an account.");
            $this->RedirectToController('create');
        }
    }

    /**
     * Perform a login of the user as callback on a submitted form.
     */
    public function DoLogin($form) {
        if ($this->user->Login($form->Value('acronym'), $form->Value('password'))) {
            $this->RedirectToController('profile');
        } else {
            $this->RedirectToController('login');
        }
    }

    /**
     * Logout a user.
     */
    public function Logout() {
        $this->user->Logout();
        $this->RedirectTo("user/index");
    }
}

class CFormUserProfile extends CForm {

    /**
     * Constructor
     */
    public function __construct($object, $user) {
        parent::__construct();
        $this->AddElement(new CFormElementText('acronym', array('readonly' => true, 'value' => $user['acronym'])))
                ->AddElement(new CFormElementPassword('password'))
                ->AddElement(new CFormElementPassword('password1', array('label' => 'Password again:')))
                ->AddElement(new CFormElementSubmit('change_password', array('callback' => array($object, 'DoChangePassword'))))
                ->AddElement(new CFormElementText('name', array('value' => $user['name'], 'required' => true)))
                ->AddElement(new CFormElementText('email', array('value' => $user['email'], 'required' => true)))
                ->AddElement(new CFormElementSubmit('save', array('callback' => array($object, 'DoProfileSave'))));
    }

}

?>