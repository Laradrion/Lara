<?php

/**
 * The basic menu that comes with Lara.
 *
 * @package LaraCMF
 */
class CCoreMenu extends CObject implements IMenu {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * DrawMenu HTML for a menu defined in $lara->config['menus'].
     *
     * @param string $menu then key to the menu in the config-array.
     * @returns string with the HTML representing the menu.
     */
    public function DrawMenu($menu) {
        $items = null;
        if (isset($this->config['menus'][$menu])) {
            foreach ($this->config['menus'][$menu] as $key => $val) {
                $selected = null;
                if ($key != "callback") {
                    if ($val['url'] == $this->request->request) {
                        $selected = " class='selected'";
                    }
                    $items .= "<li><a {$selected} href='" . $this->request->CreateUrl($val['url']) . "'>{$val['label']}</a></li>\n";
                }
            }
        } else {
            throw new Exception('No such menu.');
        }
        return "<ul class='menu {$menu}'>\n{$items}</ul>\n";
    }

}

?>
