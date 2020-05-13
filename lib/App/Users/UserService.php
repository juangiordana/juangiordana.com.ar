<?php
/**
 * User Service.
 *
 * The UserService provides functionality for handling users, roles and
 * authentication. The UserService is often used to handle logins and
 * permissions (authentication and authorization).
 *
 * The UserService provides information useful for forcing a user to log in or
 * out, and retrieving information about the user who is currently logged-in.
 *
 * @author Juan Giordana <juangiordana@gmail.com>
 */

namespace App\Users;

use App\Users\User;

class UserService
{
    private $_dbh;

    protected $userId = null;

    /**
     * Construct.
     *
     * @access public
     * @param $dbh
     */
    public function __construct(\PDO $dbh)
    {
        $this->_dbh = $dbh;

        if (isset($_SESSION['user_id'])) {
            $this->userId = $_SESSION['user_id'];
        }
    }

    /**
     * Create a new user account.
     *
     * @return boolean TRUE if the new account was successfully created.
     *
     * @throws UsersException If userId has already been set.
     * @throws UsersException If there was a problem with the storing the
     * records in the Database.
     */
    public function register()
    {
        if (isset($this->userId)) {
            throw new UsersException('$userId is already set.');
        }

        try {
            $this->_dbh->beginTransaction();

            $password = $this->passwordHash($r['password']);

            $query = <<< 'EOD'
INSERT INTO
    `users`
SET
    `username` = ?,
    `password` = ?,
    `first_name` = ?,
    `last_name` = ?
EOD;
            $stmt = $this->_dbh->prepare($query);
            $stmt->execute([
                $r['username'],
                $password,
                $r['first_name'],
                $r['last_name'],
            ]);

            $userId = $this->_dbh->lastInsertId();

            $query = 'INSERT INTO `users_meta` SET `user_id` = ?';
            $stmt = $this->_dbh->prepare($query);
            $stmt->execute([ $userId ]);

            $query = 'INSERT INTO `users_emails` SET `user_id` = ?, `main` = ?, `email` = ?';
            $stmt = $this->_dbh->prepare($query);
            $stmt->execute([
                $userId,
                1,
                $r['email']
            ]);

            $this->_dbh->commit();
        } catch (Execption $e) {
            $this->_dbh->rollback();
            throw new UsersException('Error Processing Request: ', $e->getMessage());
        }

        $this->userId = $userId;

        return true;
    }

    /**
     * Get current logged in user.
     *
     * @return User The object representing the current signed in user, or null
     * if no user is signed in.
     */
    public function getCurrentUser()
    {
        if ($this->userId !== null) {
            return new User($this->_dbh, $this->userId);
        }

        return null;
    }

    /**
     * Login: begin user session on the application.
     *
     * @return boolean TRUE if a session was successfully started, otherwise
     * FALSE.
     *
     * @throws UsersException If no userId has been set.
     */
    public function login()
    {
        if (!isset($this->userId)) {
            throw new UsersException('$userId must be set.');
        }

        $_SESSION['user_id'] = $this->userId;

        $this->setLastLogin();

        return true;
    }

    /**
     * Logout: end user session on the application.
     *
     * @return boolean TRUE if a session was successfully ended, otherwise
     * FALSE.
     *
     * @throws UsersException If no userId has been set.
     */
    public function logout()
    {
        if (!isset($this->userId)) {
            throw new UsersException('$userId must be set.');
        }

        if (isset($_SESSION['user_id'])) {
            unset($_SESSION['user_id']);
        }

        return true;
    }

    /**
     * Authenticate a user by checking credentials.
     *
     * @return boolean TRUE if the authentication was successful, otherwise
     * FALSE.
     *
     * @throws UsersException If userId has already been set.
     */
    public function authenticate($username, $password)
    {
        if (isset($this->userId)) {
            throw new UsersException('$userId is already set.');
        }

        $query = 'SELECT `id`, `password` FROM `users` WHERE `username` = ? LIMIT 1';
        $stmt = $this->_dbh->prepare($query);
        $stmt->execute([ $username ]);
        if ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            if ($this->passwordVerify($password, $row['password'])) {
                $this->userId = $row['id'];
                return true;
            }
        }

        return false;
    }

    /**
     * Set the last time this user had activity.
     *
     * @return integer A Unix timestamp representing the user's last activity
     * date.
     *
     * @throws UsersException If no userId has been set.
     */
    public function setLastActivity()
    {
        if (!isset($this->userId)) {
            throw new UsersException('$userId must be set.');
        }

        $lastActivity = time();

        $query = 'UPDATE `users_meta` SET `last_activity` = ? WHERE `user_id` = ? LIMIT 1';
        $stmt = $this->_dbh->prepare($query);
        $stmt->execute([ $lastActivity, $this->userId ]);

        return $lastActivity;
    }

    /**
     * Set the last time this user logged in.
     *
     * @return integer A Unix timestamp representing the user's last login time.
     *
     * @throws UsersException If no userId has been set.
     */
    public function setLastLogin()
    {
        if (!isset($this->userId)) {
            throw new UsersException('$userId must be set.');
        }

        $lastLogin = time();

        $query = 'UPDATE `users_meta` SET `last_login` = ? WHERE `user_id` = ? LIMIT 1';
        $stmt = $this->_dbh->prepare($query);
        $stmt->execute([ $lastLogin, $this->userId ]);

        return $lastLogin;
    }

    public static function passwordHash($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public static function passwordVerify($password, $hash)
    {
        return password_verify($password, $hash);
    }
}
