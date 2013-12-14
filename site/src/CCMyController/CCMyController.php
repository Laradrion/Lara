<?php

/**
 * Sample controller for a site builder.
 * 
 * @package LaraExample
 */
class CCMycontroller extends CObject implements IController {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * The home page
     */
    public function Index() {
        $content = new CMContent(1);
        $this->views->SetTitle('Home' . htmlEnt($content['title']));
        $this->views->AddInclude('MyController/page.tpl.php', array(
            'content' => $content,
        ));
    }

    /**
     * The about page
     */
    public function About() {
        $content = new CMContent(2);
        $this->views->SetTitle('About' . htmlEnt($content['title']));
        $this->views->AddInclude('MyController/page.tpl.php', array(
            'content' => $content,
        ));
    }

    /**
     * The contact page
     */
    public function Contact() {
        $content = new CMContent(3);
        $this->views->SetTitle('Contact' . htmlEnt($content['title']));
        $this->views->AddInclude('MyController/page.tpl.php', array(
            'content' => $content,
        ));
    }

    /**
     * The blog.
     */
    public function Blog() {
        $content = new CMContent();
        $this->views->SetTitle('My blog');
        $this->views->AddInclude('MyController/blog.tpl.php', array(
            'contents' => $content->ListAll(array('type' => 'post', 'order-by' => 'title', 'order-order' => 'DESC')),
        ));
    }

    /**
     * The guestbook.
     */
    public function Guestbook() {
        $guestbook = new CMGuestbook();
        $form = new CFormMyGuestbook($guestbook);
        $status = $form->Check();
        if ($status === false) {
            $this->AddMessage('notice', 'The form could not be processed.');
            $this->RedirectToControllerMethod();
        } else if ($status === true) {
            $this->RedirectToControllerMethod();
        }

        $this->views->SetTitle('My Guestbook');
        $this->views->AddInclude('MyController/guestbook.tpl.php', array(
            'entries' => $guestbook->ReadAll(),
            'form' => $form,
        ));
    }

}

/**
 * Form for the guestbook
 */
class CFormMyGuestbook extends CForm {

    /**
     * Properties
     */
    private $object;

    /**
     * Constructor
     */
    public function __construct($object) {
        parent::__construct();
        $this->object = $object;
        $this->AddElement(new CFormElementTextarea('data', array('label' => 'Add entry:')));
        $this->AddElement(new CFormElementSubmit('add', array('callback' => array($this, 'DoAdd'), 'callback-args' => array($object))));
    }

    /**
     * Callback to add the form content to database.
     */
    public function DoAdd($form, $object) {
        return $object->Add(strip_tags($form['data']['value']));
    }

}