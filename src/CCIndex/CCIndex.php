<?php

/**
 * Standard controller layout.
 * 
 * @package LaraCMF
 */
class CCIndex extends CObject implements IController {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Implementing interface IController. All controllers must have an index action.
     */
    public function Index() {
        $modules = new CMModules();
        $controllers = $modules->AvailableControllers();
        $this->views->SetTitle('Index');
        $this->views->AddInclude('Index/index.tpl.php', array(), 'primary');
        $this->views->AddInclude('Index/sidebar.tpl.php', array('controllers' => $controllers), 'sidebar');
    }

}

?>