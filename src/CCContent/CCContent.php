<?php

/**
 * A user controller to manage content.
 * 
 * @package LaraCMF
 */
class CCContent extends CObject implements IController {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Show a listing of all content.
     */
    public function Index() {
        $content = new CMContent();
        $this->views->SetTitle('Content Controller');
        $this->views->AddInclude('Content/index.tpl.php', array(
            'contents' => $content->ListAll(),
        ));
    }

    /**
     * Edit a selected content, or prepare to create new content if argument is missing.
     *
     * @param id integer the id of the content.
     */
    public function Edit($id = null) {
        $content = new CMContent($id);
        $form = new CFormContent($content);
        $status = $form->Check();
        if ($status === false) {
            $this->AddMessage('notice', 'The form could not be processed.');
            $this->RedirectToController('edit', $id);
        } else if ($status === true) {
            $this->RedirectToController('edit', $content['id']);
        }

        $title = isset($id) ? 'Edit' : 'Create';
        $this->views->SetTitle("$title content: $id");
        $this->views->AddInclude('Content/edit.tpl.php', array(
            'user' => $this->user,
            'content' => $content,
            'form' => $form,
        ));
    }

    /**
     * Create new content.
     */
    public function Create() {
        $this->Edit();
    }
    
    /**
     * Undelete content
     * 
     * @param int $id = The id of the content to undelete
     */
    public function Undelete($id=null) {
        $content = new CMContent();
        $content->Undelete($id);
        $this->RedirectTo('content');
    }
    
    /**
     * Trash content
     * 
     * @param int $id = The id of the content to permanently delete.
     */
    public function Trash($id=null) {
        $content = new CMContent();
        $content->Trash($id);
        $this->RedirectTo('content');
    }

}

?>