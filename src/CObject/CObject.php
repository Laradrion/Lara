<?php

/**
 * Holding a instance of CLara to enable use of $this in subclasses.
 *
 * @package LaraCore
 */
class CObject {

    /**
     * Members
     */
    public $config;
    public $request;
    public $data;
    public $db;
    public $views;
    public $session;

    /**
     * Constructor
     */
    protected function __construct($lara = null) {
        if (!$lara) {
            $lara = CLara::Instance();
        }
        $this->config = &$lara->config;
        $this->request = &$lara->request;
        $this->data = &$lara->data;
        $this->db = &$lara->db;
        $this->views = &$lara->views;
        $this->user = &$lara->user;
        $this->session = &$lara->session;
    }

    /**
     * Redirect to another url and store the session
     */
    protected function RedirectTo($url) {
        CLara::Instance()->RedirectTo($url);
    }

    /**
     * Redirect to a method within the current controller. Defaults to index-method. Uses RedirectTo().
     *
     * @param string method name the method, default is index method.
     */
    protected function RedirectToController($method = null) {
        CLara::Instance()->RedirectToController($method);
    }

    /**
     * Redirect to a controller and method. Uses RedirectTo().
     *
     * @param string controller name the controller or null for current controller.
     * @param string method name the method, default is current method.
     */
    protected function RedirectToControllerMethod($controller = null, $method = null) {
        CLara::Instance()->RedirectToControllerMethod($controller, $method);
    }

}

?>