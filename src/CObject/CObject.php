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
  protected function __construct() {
    $lara = CLara::Instance();
    $this->config   = &$lara->config;
    $this->request  = &$lara->request;
    $this->data     = &$lara->data;
    $this->db       = &$lara->db;
    $this->views    = &$lara->views;
    $this->session  = &$lara->session;
  }
  
  /**
   * Redirect to another url and store the session
   */
  protected function RedirectTo($url) {
    $lara = CLara::Instance();
    if(isset($lara->config['debug']['db-num-queries']) && $lara->config['debug']['db-num-queries'] && isset($lara->db)) {
      $this->session->SetFlash('database_numQueries', $this->db->GetNumQueries());
    }    
    if(isset($lara->config['debug']['db-queries']) && $lara->config['debug']['db-queries'] && isset($lara->db)) {
      $this->session->SetFlash('database_queries', $this->db->GetQueries());
    }    
    if(isset($lara->config['debug']['timer']) && $lara->config['debug']['timer']) {
	    $this->session->SetFlash('timer', $lara->timer);
    }    
    $this->session->StoreInSession();
    header('Location: ' . $this->request->CreateUrl($url));
  }

}
?>