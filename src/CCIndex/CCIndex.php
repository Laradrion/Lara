<?php
/**
* Standard controller layout.
* 
* @package LaraCore
*/
class CCIndex implements IController {

   /**
    * Implementing interface IController. All controllers must have an index action.
    */
   public function Index() {   
      global $lara;
	  $lara->data['main'] = "<h1>The Index Controller</h1>";
	  $menu = array('index', 'index/index', 'developer', 'developer/index', 'developer/links');
		
	  $html = null;
	  foreach($menu as $val) {
		  $html .= "<li><a href='" . $lara->request->CreateUrl($val) . "'>$val</a>";  
	  }
		
      $lara->data['title'] = "The Index Controller";
	  $lara->data['main'] = <<<EOD
<h1>The Index Controller</h1>
<p>This is what you can do for now:</p>
<ul>
$html
</ul>
EOD;
   }

}
?>