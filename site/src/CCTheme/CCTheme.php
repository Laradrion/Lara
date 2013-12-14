<?php

/**
 * A test controller for themes.
 * 
 * @package LaraTest
 */
class CCTheme extends CObject implements IController {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Display what can be done with this controller.
     */
    public function Index() {
        // Get a list of all kontroller methods
        $this->views->AddStyle('body:hover{background:#fff url(' . $this->request->base_url . 'themes/grid/grid_12_60_20.png) repeat-y center top;}');
        $rc = new ReflectionClass(__CLASS__);
        $methods = $rc->getMethods(ReflectionMethod::IS_PUBLIC);
        $items = array();
        foreach ($methods as $method) {
            if ($method->name != '__construct' && $method->name != '__destruct' && $method->name != 'Index') {
                $items[] = $this->request->controller . '/' . mb_strtolower($method->name);
            }
        }

        $this->views->SetTitle('Theme');
        $this->views->AddInclude('Theme/index.tpl.php', array(
            'theme_name' => $this->config['theme']['name'],
            'methods' => $items,
        ));
    }

    /**
     * Put content in some regions.
     */
    public function SomeRegions() {
        $this->views->AddStyle('body:hover{background:#fff url(' . $this->request->base_url . 'themes/grid/grid_12_60_20.png) repeat-y center top;}');
        $this->views->SetTitle('Theme display content for some regions');
        $this->views->AddString('This is the primary region', array(), 'primary');

        if (func_num_args()) {
            foreach (func_get_args() as $val) {
                $this->views->AddString("This is region: $val", array(), $val);
                $this->views->AddStyle('#' . $val . '{background-color:hsla(0,0%,90%,0.5);}');
            }
        }
    }

    /**
     * Put content in all regions.
     */
    public function AllRegions() {
        $this->views->AddStyle('body:hover{background:#fff url(' . $this->request->base_url . 'themes/grid/grid_12_60_20.png) repeat-y center top;}');
        $this->views->SetTitle('Theme display content for all regions');
        foreach ($this->config['theme']['regions'] as $val) {
            $this->views->AddString("This is region: $val", array(), $val);
            $this->views->AddStyle('#' . $val . '{background-color:hsla(0,0%,90%,0.5);}');
        }
    }

    /**
     * Put content in all regions.
     */
    public function h1h6() {
        $this->views->AddStyle('body:hover{background:#fff url(' . $this->request->base_url . 'themes/grid/grid_12_60_20.png) repeat-y center top;}');
        $this->views->SetTitle('Theme testing headers and paragraphs');
        $this->views->AddInclude('Theme/h1h6.tpl.php', array(
            'theme_name' => $this->config['theme']['name'],
        ));
    }

    /**
     * The main method showing the bootstrap theme.
     */
    public function Bootstrap($regions = null) {
        if (is_null($regions))
            $regions = "Default";
        $this->config['theme']['name'] = "bootstrap";
        $this->config['theme']['path'] = "themes/bootstrap";
        $this->config['theme']['parent'] = null;
        $this->config['theme']['stylesheet'] = "style.css";
        $this->config['theme']['template_file'] = "default.tpl.php";
        $this->config['theme']['regions'] = array('flash', 'featured-first', 'featured-middle', 'featured-last',
            'primary', 'sidebar', 'triptych-first', 'triptych-middle', 'triptych-last',
            'footer-column-one', 'footer-column-two', 'footer-column-three', 'footer-column-four',
            'footer',
        );
        switch ($regions) {
            case "someregions":
                $args=null;
                if (func_num_args()>1) {
                    $i=0;
                    $args=array();
                    foreach (func_get_args() as $val) {
                        if($i != 0)
                        {
                            $args[] = $val;
                        }
                        $i++;
                    }
                }
                $this->BootstrapSomeRegions($args);
                break;
            case "allregions":
                $this->BootstrapAllRegions($regions);
                break;
            case "Default":
                $this->BootstrapDefault();
                break;
        }
    }

    /**
     * Put content in some regions using the bootstrap theme.
     */
    private function BootstrapSomeRegions($args=null) {
        $this->views->SetTitle('Theme display content for some regions');
        $this->views->AddString('This is the primary region', array(), 'primary');
        $this->views->AddStyle('#primary{background-color:hsla(0,0%,90%,0.5);}');

        if (!is_null($args)) {
            foreach ($args as $val) {
                $this->views->AddString("This is region: $val", array(), $val);
                $this->views->AddStyle('#' . $val . '{background-color:hsla(0,0%,90%,0.5);}');
            }
        }
    }

    /**
     * Put content in all regions using the bootstrap theme.
     */
    private function BootstrapAllRegions() {
        $this->views->SetTitle('Theme display content for all regions');
        foreach ($this->config['theme']['regions'] as $val) {
            $this->views->AddString("This is region: $val", array(), $val);
            $this->views->AddStyle('#' . $val . '{background-color:hsla(0,0%,90%,0.5);}');
        }
    }

    /**
     * Put some default content using the bootstrap theme.
     */
    private function BootstrapDefault() {
        $this->views->SetTitle('Theme bootstrap');
        $items = array();
        $items[] = $this->request->controller . '/bootstrap/someregions';
        $items[] = $this->request->controller . '/bootstrap/allregions';
        $items[] = $this->request->controller . '/bootstrap/someregions/footer-column-two';
        $this->views->AddInclude('Theme/index.tpl.php', array(
            'theme_name' => $this->config['theme']['name'],
            'methods' => $items,
        ));

    }

}

?>