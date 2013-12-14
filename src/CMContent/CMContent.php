<?php

/**
 * A model for content stored in database.
 * 
 * @package LaraCMF
 */
class CMContent extends CObject implements IHasSQL, ArrayAccess, IModule {

    /**
     * Properties
     */
    public $data;

    /**
     * Constructor
     */
    public function __construct($id = null) {
        parent::__construct();
        if ($id) {
            $this->LoadById($id);
        } else {
            $this->data = array();
        }
    }

    /**
     * Implementing ArrayAccess for $this->data
     */
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    /**
     * Implementing ArrayAccess for $this->data
     */
    public function offsetExists($offset) {
        return isset($this->data[$offset]);
    }

    /**
     * Implementing ArrayAccess for $this->data
     */
    public function offsetUnset($offset) {
        unset($this->data[$offset]);
    }

    /**
     * Implementing ArrayAccess for $this->data
     */
    public function offsetGet($offset) {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }

    /**
     * Implementing interface IHasSQL. Encapsulate all SQL used by this class.
     *
     * @param string $key the string that is the key of the wanted SQL-entry in the array.
     */
    public static function SQL($key = null) {
        $queries = array(
            'drop table content' => "DROP TABLE IF EXISTS Content;",
            'create table content' => "CREATE TABLE IF NOT EXISTS Content (id INTEGER PRIMARY KEY, key TEXT KEY, type TEXT, title TEXT, data TEXT, filter TEXT, idUser INT, created DATETIME default (datetime('now')), updated DATETIME default NULL, deleted DATETIME default NULL, FOREIGN KEY(idUser) REFERENCES User(id));",
            'insert content' => 'INSERT INTO Content (key,type,title,data,filter,idUser) VALUES (?,?,?,?,?,?);',
            'select * by id' => 'SELECT c.*, u.acronym as owner FROM Content AS c INNER JOIN User as u ON c.idUser=u.id WHERE c.id=? AND deleted IS NULL;',
            'select * by key' => 'SELECT c.*, u.acronym as owner FROM Content AS c INNER JOIN User as u ON c.idUser=u.id WHERE c.key=? AND deleted IS NULL;',
            'select *' => 'SELECT c.*, u.acronym as owner FROM Content AS c INNER JOIN User as u ON c.idUser=u.id;',
            'select all blogs' => 'SELECT c.*, u.acronym as owner FROM Content AS c INNER JOIN User as u ON c.idUser=u.id WHERE c.type = ? AND deleted IS NULL',
            'update content' => "UPDATE Content SET key=?, type=?, title=?, data=?, filter=?, updated=datetime('now') WHERE id=?;",
            'update content as deleted' => "UPDATE Content SET deleted=datetime('now') WHERE id=?;",
            'update content as undeleted' => "UPDATE Content SET deleted = NULL WHERE id=?;",
            'delete content' => "DELETE FROM Content WHERE id=?;",
        );
        if (!isset($queries[$key])) {
            throw new Exception("No such SQL query, key '$key' was not found.");
        }
        return $queries[$key];
    }

    /**
     * Implementing interface IModule. Manage install/update/deinstall and equal actions.
     */
    public function Manage($action = null) {
        switch ($action) {
            case 'install':
                try {
                    $this->db->ExecuteQuery(self::SQL('drop table content'));
                    $this->db->ExecuteQuery(self::SQL('create table content'));
                    $this->db->ExecuteQuery(self::SQL('insert content'), array('first-page', 'page', 'First page', '[b]This is a demo page![/b]
                
                Showing "some filters" in work...
                
                You should edit this page to show a nice front page for the site.
                ', 'bbcode,nl2br,typographer,htmlpurify', 1));
                    $this->db->ExecuteQuery(self::SQL('insert content'), array('about-page', 'page', 'About page', '[b]This is a demo page![/b]
                
                Here you can tell your visitors about yourself.
                ', 'bbcode,nl2br,typographer,htmlpurify', 1));
                    $this->db->ExecuteQuery(self::SQL('insert content'), array('contact-page', 'page', 'Contact page', '[b]This is a demo page![/b]
                
                Here you can tell your visitors how to make contact.
                ', 'bbcode,nl2br,typographer,htmlpurify', 1));
                    $this->db->ExecuteQuery(self::SQL('insert content'), array('hello-world', 'post', 'Hello World', '[b]This is a demo post[/b]
                
                Hello world! This is showing some of the blog capabilities.
                ', 'bbcode,nl2br,typographer,htmlpurify', 1));
                    $this->db->ExecuteQuery(self::SQL('insert content'), array('demo-blog', 'post', 'Demo Blog', '[b]This is a demo post[/b]
                
                Another entry in the blog.
                ', 'bbcode,nl2br,typographer,htmlpurify', 1));
                    return array('success', 'Successfully created the database tables and created a default "Hello World" blog post, owned by root.');
                } catch (Exception$e) {
                    die("$e<br/>Failed to open database: " . $this->config['database'][0]['dsn']);
                }
                break;

            default:
                throw new Exception('Unsupported action for this module.');
                break;
        }
    }

    /**
     * Save content. If it has a id, use it to update current entry or else insert new entry.
     *
     * @returns boolean true if success else false.
     */
    public function Save() {
        $msg = null;
        $failed = false;
        if ($this['id']) {
            $this->db->ExecuteQuery(self::SQL('update content'), array($this['key'], $this['type'], $this['title'], $this['data'], $this['filter'], $this['id']));
            $msg = 'update';
        } else {
            if($this->db->ExecuteQuery(self::SQL('insert content'), array($this['key'], $this['type'], $this['title'], $this['data'], $this['filter'], $this->user['id'])))
            {
                $this['id'] = $this->db->LastInsertId();
            } else {
                $failed = true;
            }
            $msg = 'created';
        }
        $rowcount = $this->db->RowCount();
        if ($rowcount || !$failed) {
            $this->session->AddMessage('success', "Successfully {$msg} content '{$this['key']}'.");
        } else {
            $this->session->AddMessage('error', "Failed to {$msg} content '{$this['key']}'.");
        }
        return $rowcount === 1;
    }

    /**
     * Load content by id.
     *
     * @param id integer the id of the content.
     * @returns boolean true if success else false.
     */
    public function LoadById($id) {
        $res = $this->db->ExecuteSelectQueryAndFetchAll(self::SQL('select * by id'), array($id));
        if (empty($res)) {
            $this->session->AddMessage('error', "Failed to load content with id '$id'.");
            return false;
        } else {
            $this->data = $res[0];
        }
        return true;
    }

    /**
     * List all content.
     *
     * @returns array with listing or null if empty.
     */
    public function ListAll($arguments = null) {
        try {
            $sql = "";
            $order = "";
            $params = array();
            if (is_null($arguments)) {
                $sql = "select *";
            } else {
                $sql = "select all blogs";
                $params[] = $arguments['type'];
                if (isset($arguments['order-by'])) {
                    $order .= " ORDER BY {$arguments['order-by']} ";
                    if (isset($arguments['order-order'])) {
                        $order .= $arguments['order-order'];
                    } else {
                        $order .= "DESC";
                    }
                }
            }
            $order .= ";";
            $sql = self::SQL($sql) . $order;
            return $this->db->ExecuteSelectQueryAndFetchAll($sql, $params);
        } catch (Exception$e) {
            return null;
        }
    }

    /**
     * Delete content. Set its deletion-date to enable wastebasket functionality.
     *
     * @returns boolean true if success else false.
     */
    public function Delete() {
        if ($this['id']) {
            $this->db->ExecuteQuery(self::SQL('update content as deleted'), array($this['id']));
        }
        $rowcount = $this->db->RowCount();
        if ($rowcount) {
            $this->session->AddMessage('success', "Successfully set content '" . htmlEnt($this['key']) . "' as deleted.");
        } else {
            $this->session->AddMessage('error', "Failed to set content '" . htmlEnt($this['key']) . "' as deleted.");
        }
        return $rowcount === 1;
    }
    
    /**
     * Undelete content. Set its deletion-date to null.
     *
     * @returns boolean true if success else false.
     */
    public function Undelete($id=null) {
        if (!is_null($id)) {
            $this->db->ExecuteQuery(self::SQL('update content as undeleted'), array($id));
        }
        $rowcount = $this->db->RowCount();
        if ($rowcount) {
            $this->session->AddMessage('success', "Successfully set content witd id: '" . $id . "' as available.");
        } else {
            $this->session->AddMessage('error', "Failed to set content with id: '" . $id . "' as available.");
        }
        return $rowcount === 1;
    }

    /**
     * Trash content. Deletes content permanently.
     *
     * @returns boolean true if success else false.
     */
    public function Trash($id=null) {
        if (!is_null($id)) {
            $this->db->ExecuteQuery(self::SQL('delete content'), array($id));
        }
        $rowcount = $this->db->RowCount();
        if ($rowcount) {
            $this->session->AddMessage('success', "Successfully deleted content with id: '" . $id . "'.");
        } else {
            $this->session->AddMessage('error', "Failed to delete content with id: '" . $id . "'.");
        }
        return $rowcount === 1;
    }

}

?>