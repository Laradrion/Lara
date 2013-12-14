<?php

/**
 * Main class for Lara, holds everything.
 *
 * @package LaraCore
 */
class CLara implements ISingleton {

    /**
     * Members
     */
    private static $instance = null;
    public $config = array();
    public $request;
    public $data;
    public $db;
    public $views;
    public $session;
    public $timer = array();

    /**
     * Singleton pattern. Get the instance of the latest created object or create a new one. 
     * @return CLara The instance of this class.
     */
    public static function Instance() {
        if (self::$instance == null) {
            self::$instance = new CLara();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    protected function __construct() {
        // time page generation
        $this->timer['first'] = microtime(true);

        // include the site specific config.php and create a ref to $lara to be used by config.php
        $lara = &$this;
        require(LARA_SITE_PATH . '/config.php');

        // Start a named session
        session_name($this->config['session_name']);
        session_start();
        $this->session = new CSession($this->config['session_key']);
        $this->session->PopulateFromSession();

        // Set default date/time-zone
        date_default_timezone_set($this->config['timezone']);

        // Create a database object.
        if (isset($this->config['database'][0]['dsn'])) {
            $this->db = new CMDatabase($this->config['database'][0]['dsn']);
        }

        // Create a container for all views and theme data
        $this->views = new CViewContainer();

        // Create a object for the user
        $this->user = new CMUser($this);
    }

    /**
     * Frontcontroller, check url and route to controllers.
     */
    public function FrontControllerRoute() {
        // Take current url and divide it in controller, method and parameters
        $this->request = new CRequest();
        $this->request->Init($this->config['base_url'], $this->config['routing']);
        $controller = $this->request->controller;
        $method = $this->request->method;
        $arguments = $this->request->arguments;

        // Is the controller enabled in config.php?
        $controllerExists = isset($this->config['controllers'][$controller]);
        $controllerEnabled = false;
        $className = false;
        $classExists = false;

        if ($controllerExists) {
            $controllerEnabled = ($this->config['controllers'][$controller]['enabled'] == true);
            $className = $this->config['controllers'][$controller]['class'];
            $classExists = class_exists($className);
        }

        $formattedMethod = str_replace(array('_', '-'), '', $method);

        // Check if controller has a callable method in the controller class, if then call it
        if ($controllerExists && $controllerEnabled && $classExists) {
            $rc = new ReflectionClass($className);
            if ($rc->implementsInterface('IController')) {
                $formattedMethod = str_replace(array('_', '-'), '', $method);
                if ($rc->hasMethod($formattedMethod)) {
                    $controllerObj = $rc->newInstance();
                    $methodObj = $rc->getMethod($formattedMethod);
                    if ($methodObj->isPublic()) {
                        $methodObj->invokeArgs($controllerObj, $arguments);
                    } else {
                        die("404. " . get_class() . ' error: Controller method not public.');
                    }
                } else {
                    die("404. " . get_class() . ' error: Controller does not contain method.');
                }
            } else {
                die('404. ' . get_class() . ' error: Controller does not implement interface IController.');
            }
        } else {
            die('404. Page is not found.');
        }
    }

    /**
     * ThemeEngineRender, renders the reply of the request.
     */
    public function ThemeEngineRender() {
        // Save to session before output anything
        $this->session->StoreInSession();

        // Is theme enabled?
        if (!isset($this->config['theme'])) {
            return;
        }

        // Get the paths and settings for the theme
        $themePath = LARA_INSTALL_PATH . '/' . $this->config['theme']['path'];
        $themeUrl = $this->request->base_url . $this->config['theme']['path'];

        // Is there a parent theme?
        $parentPath = null;
        $parentUrl = null;
        if (isset($this->config['theme']['parent'])) {
            $parentPath = LARA_INSTALL_PATH . '/' . $this->config['theme']['parent'];
            $parentUrl = $this->request->base_url . $this->config['theme']['parent'];
        }
        // Add stylesheet path to the $lara->data array
        $this->data['stylesheet'] = $this->config['theme']['stylesheet'];

        // Make the theme urls and paths available as part of $lara
        $this->themeUrl = $themeUrl;
        $this->themeParentUrl = $parentUrl;
        $this->themePath = $themePath;
        $this->themeParentPath = $parentPath;

        // Include the global functions.php and the functions.php that are part of the theme
        $lara = &$this;
        include(LARA_INSTALL_PATH . "/themes/functions.php");
        // Then the functions.php from the parent theme
        if ($parentPath) {
            if (is_file("{$parentPath}/functions.php")) {
                include "{$parentPath}/functions.php";
            }
        }
        $functionsPath = "{$themePath}/functions.php";
        if (is_file($functionsPath)) {
            include $functionsPath;
        }

        // Map menu to region if defined
        if (is_array($this->config['theme']['menu_to_region'])) {
            foreach ($this->config['theme']['menu_to_region'] as $key => $val) {
                if(class_exists($this->config['menus'][$val]['callback']))
                {
                    $rc = new ReflectionClass($this->config['menus'][$val]['callback']);
                    if ($rc->implementsInterface('IMenu')) {
                        if ($rc->hasMethod('DrawMenu')) {
                            $menuObj = $rc->newInstance();
                            $methodObj = $rc->getMethod('DrawMenu');
                            if ($methodObj->isPublic()) {
                                $this->views->AddString($methodObj->invokeArgs($menuObj, array($key)), null, $val);
                            }
                        }
                    }
                }
            }
        }

        // Extract $lara->data to own variables and handover to the template file
        if (isset($this->config['theme']['data'])) {
            extract($this->config['theme']['data']);
        }
        extract($this->data);
        extract($this->views->GetData());

        // Check for title or view title
        if (!isset($title)) {
            $title = extract($this->views->GetData());
            "Lara - A PHP-based MVC-inspired CMF";
        }
        $templateFile = (isset($this->config['theme']['template_file'])) ? $this->config['theme']['template_file'] : 'default.tpl.php';

        if (is_file("{$themePath}/{$templateFile}")) {
            include("{$themePath}/{$templateFile}");
        } else if (is_file("{$parentPath}/{$templateFile}")) {
            include("{$parentPath}/{$templateFile}");
        } else {
            throw new Exception('No such template file.');
        }
    }

    /**
     * Redirect to another url and store the session
     */
    public function RedirectTo($url) {
        if (isset($this->config['debug']['db-num-queries']) && $this->config['debug']['db-num-queries'] && isset($this->db)) {
            $this->session->SetFlash('database_numQueries', $this->db->GetNumQueries());
        }
        if (isset($this->config['debug']['db-queries']) && $this->config['debug']['db-queries'] && isset($this->db)) {
            $this->session->SetFlash('database_queries', $this->db->GetQueries());
        }
        if (isset($this->config['debug']['timer']) && $this->config['debug']['timer']) {
            $this->session->SetFlash('timer', $this->timer);
        }
        $this->session->StoreInSession();
        header('Location: ' . $this->request->CreateUrl($url));
    }

    /**
     * Redirect to a method within the current controller. Defaults to index-method. Uses RedirectTo().
     *
     * @param string method name the method, default is index method.
     */
    public function RedirectToController($method = null) {
        $this->RedirectTo($this->request->CreateUrl($controller, $method));
    }

    /**
     * Redirect to a controller and method. Uses RedirectTo().
     *
     * @param string controller name the controller or null for current controller.
     * @param string method name the method, default is current method.
     */
    public function RedirectToControllerMethod($controller = null, $method = null) {
        $controller = is_null($controller) ? $this->request->controller : null;
        $method = is_null($method) ? $this->request->method : null;
        $this->RedirectTo($this->request->CreateUrl($controller, $method));
    }
}

?>