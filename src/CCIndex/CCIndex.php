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
      $lara->data['title'] = "The Index Controller";
   }

}
?>