<?php
/**
 * User.
 *
 * @author Juan Giordana <juangiordana@gmail.com>
 */

namespace App\Users;

class User
{
    private $_dbh;

    protected $user;

    /**
     * Construct
     *
     * @access public
     * @param  $dbh
     * @param  $userId
     */
    public function __construct(\PDO $dbh, $userId)
    {
        $this->_dbh = $dbh;

        if (empty($userId)) {
            throw new \Exception('$userId must be set.');
        }

        $this->find($userId);
    }

    private function find($userId)
    {
        $query = <<< 'EOD'
SELECT
    `users`.`id`,
    `users`.`username`,
    `users`.`password`,
    `users_meta`.`last_activity`,
    `users_meta`.`last_login`
FROM
    `users`
INNER JOIN
    `users_meta`
ON
    `users_meta`.`user_id` = `users`.`id`
WHERE
    `users`.`id` = :id
LIMIT 1
EOD;
        $stmt = $this->_dbh->prepare($query);
        $stmt->bindParam(':id', $userId, \PDO::PARAM_INT);
        $stmt->execute();

        if ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $this->user = $row;
            return true;
        }

        return false;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->user)) {
            return $this->user[$name];
        }

        throw new \Exception('Undefined property via __get(): ' . $name);
    }

    public function __toString()
    {
        return print_r($this->user, 1);
    }

    /**
     * Return this user's e-mail address.
     *
     * @return string The user's e-mail address.
     */
    public function getEmail()
    {
        return $this->user['email'];
    }

    /**
     * Return the last time this user had activity.
     *
     * @return integer The user's last activity Unix timestamp.
     */
    public function getLastActivity()
    {
        return $this->user['last_activity'];
    }

    /**
     * Return the last time this user logged in.
     *
     * @return integer The user's last login Unix timestamp.
     */
    public function getLastLogin()
    {
        return $this->user['last_login'];
    }


    public function setPassword($hash)
    {
        $query = 'UPDATE `users` SET `password` = ? WHERE `id` = ? LIMIT 1';
        $stmt = $this->_dbh->prepare($query);
        $stmt->execute([ $hash, $this->user['id'] ]);

        return true;
    }
}
