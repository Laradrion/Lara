<?php

/**
 * Admin Control Panel Controller.
 * 
 * @package LaraCMF
 */
class CCAdminControlPanel extends CObject implements IController {

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
        $this->Menu();
    }

    /**
     * Create a method that shows the menu, same for all methods
     */
    private function Menu() {
        $html = null;

        $this->data['title'] = "The Admin Control Panel Controller";
        $this->data['main'] = <<<EOD
<h1>The Admin Control Panel Controller</h1>
<p>Not yet implemented.</p>
$html
EOD;
    }

}

?>