<?php

/**
 * The basic menu that comes with Lara.
 *
 * @package LaraCMF
 */
class CDropdownMenu extends CObject implements IMenu {

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
            $return = $this->GenerateNavBar($menu, $this->config['menus'][$menu]);
            $items .= $return['items'];
        } else {
            throw new Exception('No such menu.');
        }
        return $items;
    }

    private function GenerateNavBar($menu, $menuitems) {
        $items = "";
        $isselected = false;
        foreach ($menuitems as $key => $val) {
            $selected = null;
            $return = null;
            if ($key != "callback") {
                if ($val['type'] == 'parent') {
                    $return = $this->GenerateNavbar($menu, $val['children']);
                    if ($return['selected']) {
                        $selected = " class='selected'";
                        $isselected = true;
                    }
                }
                if ($val['url'] == $this->request->request && $val['url'] != null) {
                    $selected = " class='selected'";
                    $isselected = true;
                }
                $children = "";
                if ($val['type'] == 'parent') {
                    $children .= $return['items'];
                }
                if (!is_null($val['url'])) {
                    $items .= "<li><a {$selected} href='" . $this->request->CreateUrl($val['url']) . "'>{$val['label']}</a>{$children}</li>\n";
                } else {
                    $items .= "<li><span {$selected}>{$val['label']}</span>{$children}</li>\n";
                }
            }
        }
        return array('selected' => $isselected, 'items' => "<ul class='menu {$menu}'>\n{$items}</ul>\n");
    }

}

?>
