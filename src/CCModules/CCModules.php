<?php

/**
 * To manage and analyse all modules of Lara.
 * 
 * @package LaraCMF
 */
class CCModules extends CObject implements IController {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Show a index-page and display what can be done through this controller.
     */
    public function Index() {
        $modules = new CMModules();
        $controllers = $modules->AvailableControllers();
        $allModules = $modules->ReadAndAnalyse();
        $this->views->SetTitle('Manage Modules');
        $this->views->AddInclude('Modules/index.tpl.php', array('controllers' => $controllers), 'primary');
        $this->views->AddInclude('Modules/sidebar.tpl.php', array('modules' => $allModules), 'sidebar');
    }

    /**
     * Show and install manageable modules.
     */
    public function Install() {
        $modules = new CMModules();
        $results = $modules->Install();
        $allModules = $modules->ReadAndAnalyse();
        $this->views->SetTitle('Install Modules');
        $this->views->AddInclude('Modules/install.tpl.php', array('modules' => $results), 'primary');
        $this->views->AddInclude('Modules/sidebar.tpl.php', array('modules' => $allModules), 'sidebar');
    }

    /**
     * Show a module and its parts.
     */
    public function View($module) {
        if (!preg_match('/^C[a-zA-Z]+$/', $module)) {
            throw new Exception('Invalid characters in module name.');
        }
        $modules = new CMModules();
        $controllers = $modules->AvailableControllers();
        $allModules = $modules->ReadAndAnalyse();
        $aModule = $modules->ReadAndAnalyseModule($module);
        $this->views->SetTitle('Manage Modules');
        $this->views->AddInclude('Modules/view.tpl.php', array('module' => $aModule), 'primary');
        $this->views->AddInclude('Modules/sidebar.tpl.php', array('modules' => $allModules), 'sidebar');
    }

}

?>