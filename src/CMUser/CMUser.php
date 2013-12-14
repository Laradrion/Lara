<?php

/**
 * A model for an authenticated user.
 * 
 * @package LaraCMF
 */
class CMUser extends CObject implements IHasSQL, ArrayAccess, IModule {

    /**
     * Constructor
     */
    public function __construct($lara = null) {
        parent::__construct($lara);
    }

    /**
     * Implementing ArrayAccess for this->elements
     */
    public function offsetSet($offset, $value) {
        $profile = $this->session->GetAuthenticatedUser();
        if (is_null($offset)) {
            $profile[] = $value;
        } else {
            $profile[$offset] = $value;
        }
        $this->session->SetAuthenticatedUser($profile);
    }

    /**
     * Implementing ArrayAccess for this->elements
     */
    public function offsetExists($offset) {
        $profile = $this->session->GetAuthenticatedUser();
        return isset($profile[$offset]);
    }

    /**
     * Implementing ArrayAccess for this->elements
     */
    public function offsetUnset($offset) {
        $profile = $this->session->GetAuthenticatedUser();
        unset($profile[$offset]);
        $this->session->SetAuthenticatedUser($profile);
    }

    /**
     * Implementing ArrayAccess for this->elements
     */
    public function offsetGet($offset) {
        $profile = $this->session->GetAuthenticatedUser();
        return isset($profile[$offset]) ? $profile[$offset] : null;
    }

    /**
     * Implementing interface IHasSQL. Encapsulate all SQL used by this class.
     *
     * @param string $key the string that is the key of the wanted SQL-entry in the array.
     */
    public static function SQL($key = null) {
        $queries = array(
            'drop table user' => "DROP TABLE IF EXISTS User;",
            'drop table group' => "DROP TABLE IF EXISTS Groups;",
            'drop table user2group' => "DROP TABLE IF EXISTS User2Groups;",
            'create table user' => "CREATE TABLE IF NOT EXISTS User (id INTEGER PRIMARY KEY, acronym TEXT KEY, name TEXT, email TEXT, password TEXT, salt TEXT, algorithm TEXT, created DATETIME default (datetime('now')));",
            'create table group' => "CREATE TABLE IF NOT EXISTS Groups (id INTEGER PRIMARY KEY, acronym TEXT KEY, name TEXT, created DATETIME default (datetime('now')));",
            'create table user2group' => "CREATE TABLE IF NOT EXISTS User2Groups (idUser INTEGER, idGroups INTEGER, created DATETIME default (datetime('now')), PRIMARY KEY(idUser, idGroups));",
            'insert into user' => 'INSERT INTO User (acronym,name,email,password,salt,algorithm) VALUES (?,?,?,?,?,?);',
            'insert into group' => 'INSERT INTO Groups (acronym,name) VALUES (?,?);',
            'insert into user2group' => 'INSERT INTO User2Groups (idUser,idGroups) VALUES (?,?);',
            'update user password' => 'UPDATE User SET password = ?, salt = ?, algorithm = ? WHERE id = ?',
            'update user profile' => 'UPDATE User SET name = ?, email = ? WHERE id = ?',
            'check user password' => 'SELECT * FROM User WHERE (acronym=? OR email=?);',
            'get group memberships' => 'SELECT * FROM Groups AS g INNER JOIN User2Groups AS ug ON g.id=ug.idGroups WHERE ug.idUser=?;',
        );
        if (!isset($queries[$key])) {
            throw new Exception("No such SQL query, key '$key' was not found.");
        }
        return $queries[$key];
    }

    /**
     * Implementing interface IModule. Manage install/update/deinstall and equal actions.
     *
     * @param string $action what to do.
     */
    public function Manage($action = null) {
        switch ($action) {
            case 'install':
                try {
                    $this->db->ExecuteQuery(self::SQL('drop table user2group'));
                    $this->db->ExecuteQuery(self::SQL('drop table group'));
                    $this->db->ExecuteQuery(self::SQL('drop table user'));
                    $this->db->ExecuteQuery(self::SQL('create table user'));
                    $this->db->ExecuteQuery(self::SQL('create table group'));
                    $this->db->ExecuteQuery(self::SQL('create table user2group'));
                    $password = $this->CreatePassword('root');
                    $this->db->ExecuteQuery(self::SQL('insert into user'), array('root', 'The Administrator', 'root@laradrion.com', $password['password'], $password['salt'], $password['algorithm']));
                    $idRootUser = $this->db->LastInsertId();
                    $password = $this->CreatePassword('doe');
                    $this->db->ExecuteQuery(self::SQL('insert into user'), array('doe', 'John/Jane Doe', 'doe@laradrion.com', $password['password'], $password['salt'], $password['algorithm']));
                    $idDoeUser = $this->db->LastInsertId();
                    $this->db->ExecuteQuery(self::SQL('insert into group'), array('admin', 'The Administrator Group'));
                    $idAdminGroup = $this->db->LastInsertId();
                    $this->db->ExecuteQuery(self::SQL('insert into group'), array('user', 'The User Group'));
                    $idUserGroup = $this->db->LastInsertId();
                    $this->db->ExecuteQuery(self::SQL('insert into user2group'), array($idRootUser, $idAdminGroup));
                    $this->db->ExecuteQuery(self::SQL('insert into user2group'), array($idRootUser, $idUserGroup));
                    $this->db->ExecuteQuery(self::SQL('insert into user2group'), array($idDoeUser, $idUserGroup));
                    return array('success', 'Successfully created the database tables and created a default admin user as root:root and an ordinary user as doe:doe.');
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
     * Login by autenticate the user and password. Store user information in session if success.
     *
     * @param string $akronymOrEmail the emailadress or user akronym.
     * @param string $password the password that should match the akronym or emailadress.
     * @returns booelan true if match else false.
     */
    public function Login($akronymOrEmail, $password) {
        $user = $this->db->ExecuteSelectQueryAndFetchAll(self::SQL('check user password'), array($akronymOrEmail, $akronymOrEmail));
        $user = (isset($user[0])) ? $user[0] : null;
        if ($user) {
            if ($this->CheckPassword($password, $user['algorithm'], $user['salt'], $user['password'])) {
                unset($user['password']);
                $user['groups'] = $this->db->ExecuteSelectQueryAndFetchAll(self::SQL('get group memberships'), array($user['id']));
                foreach ($user['groups'] as $val) {
                    if ($val['id'] == 1) {
                        $user['hasRoleAdmin'] = true;
                    }
                    if ($val['id'] == 2) {
                        $user['hasRoleUser'] = true;
                    }
                }
                $user['isAuthenticated'] = true;
                $this->session->SetAuthenticatedUser($user);
                $this->session->AddMessage('success', "Welcome '{$user['name']}'.");
            } else {
                $this->session->AddMessage('notice', "Could not login, user does not exists or password did not match.");
            }
        } else {
            $this->session->AddMessage('notice', "Could not login, user does not exists or password did not match.");
        }
        return ($user != null);
    }

    /**
     * Create new user.
     *
     * @param $acronym string the acronym.
     * @param $password string the password plain text to use as base. 
     * @param $name string the user full name.
     * @param $email string the user email.
     * @returns boolean true if user was created or else false and sets failure message in session.
     */
    public function Create($acronym, $password, $name, $email) {
        $pwd = $this->CreatePassword($password);
        $this->db->ExecuteQuery(self::SQL('insert into user'), array($acronym, $name, $email, $pwd['password'], $pwd['salt'], $pwd['algorithm']));
        if ($this->db->RowCount() == 0) {
            $this->session->AddMessage('error', "Failed to create user.");
            return false;
        }
        return true;
    }

    /**
     * Logout.
     */
    public function Logout() {
        $this->session->UnsetAuthenticatedUser();
        $this->session->AddMessage('success', "You have logged out.");
    }

    /**
     * Does the session contain an authenticated user?
     *
     * @returns boolen true or false.
     */
    public function IsAuthenticated() {
        return ($this->session->GetAuthenticatedUser() != false);
    }

    /**
     * Get profile information on user.
     *
     * @returns array with user profile or null if anonymous user.
     */
    public function GetUserProfile() {
        return $this->session->GetAuthenticatedUser();
    }

    /**
     * Get the user acronym.
     *
     * @returns string with user acronym or null
     */
    public function GetAcronym() {
        $profile = $this->GetUserProfile();
        return isset($profile['acronym']) ? $profile['acronym'] : null;
    }

    /**
     * Does the user have the admin role?
     *
     * @returns boolen true or false.
     */
    public function IsAdministrator() {
        $profile = $this->GetUserProfile();
        return isset($profile['hasRoleAdmin']) ? $profile['hasRoleAdmin'] : null;
    }

    /**
     * Change the current users password.
     * 
     * @param string $password
     */
    public function ChangePassword($password) {
        $password = $this->CreatePassword($password);
        $this->db->ExecuteQuery(self::SQL('update user password'), array($password['password'], $password['salt'], $password['algorithm'], $this->user['id']));
    }

    /**
     * Save the current user profile.
     */
    public function Save() {
        $this->db->ExecuteQuery(self::SQL('update user profile'), array($this->user['name'], $this->user['email'], $this->user['id']));
    }

    /**
     * Create password.
     *
     * @param $plain string the password plain text to use as base.
     * @param $algorithm string stating what algorithm to use, plain, md5, md5salt, sha1, sha1salt. 
     * defaults to the settings of site/config.php.
     * @returns array with 'salt' and 'password'.
     */
    public function CreatePassword($plain, $algorithm = null) {
        $password = array(
            'algorithm' => ($algorithm ? $algoritm : CLara::Instance()->config['hashing_algorithm']),
            'salt' => null
        );
        switch ($password['algorithm']) {
            case 'sha1salt': $password['salt'] = sha1(microtime());
                $password['password'] = sha1($password['salt'] . $plain);
                break;
            case 'md5salt': $password['salt'] = md5(microtime());
                $password['password'] = md5($password['salt'] . $plain);
                break;
            case 'sha1': $password['password'] = sha1($plain);
                break;
            case 'md5': $password['password'] = md5($plain);
                break;
            case 'plain': $password['password'] = $plain;
                break;
            default: throw new Exception('Unknown hashing algorithm');
        }
        return $password;
    }

    /**
     * Check if password matches.
     *
     * @param $plain string the password plain text to use as base.
     * @param $algorithm string the algorithm mused to hash the user salt/password.
     * @param $salt string the user salted string to use to hash the password.
     * @param $password string the hashed user password that should match.
     * @returns boolean true if match, else false.
     */
    public function CheckPassword($plain, $algorithm, $salt, $password) {
        switch ($algorithm) {
            case 'sha1salt': return $password === sha1($salt . $plain);
                break;
            case 'md5salt': return $password === md5($salt . $plain);
                break;
            case 'sha1': return $password === sha1($plain);
                break;
            case 'md5': return $password === md5($plain);
                break;
            case 'plain': return $password === $plain;
                break;
            default: throw new Exception('Unknown hashing algorithm');
        }
    }

}

?>