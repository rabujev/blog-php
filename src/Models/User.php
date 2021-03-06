<?php
namespace App\Models;

use Slim\Container;

class User{
    private $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    //check if the user is in database
    public function authenticate($username, $password) : bool {
        $username = htmlspecialchars($username);
        $password = htmlspecialchars($password);

        $sql = "SELECT user_id, passwd, permission_lvl FROM users WHERE username = :username AND is_active = 'True'";
        $stmt= $this->container->db->prepare($sql);
        $stmt->bindValue('username', $username, \PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!password_verify($password, $result['passwd'])) {
            return false;
        } else {
            $_SESSION['auth'] = [
                'login' => true,
                'username' => $username,
                'user_id' => $result['user_id'],
                'permission' => $result['permission_lvl'],
            ];
            return true;
        }
    }

    //add a user into the database
    public function addUser($firstname, $lastname, $email, $username, $password) : bool{
        $firstname = htmlspecialchars($firstname);
        $lastname = htmlspecialchars($lastname);
        $email = htmlspecialchars($email);
        $username = htmlspecialchars($username);
        $password = password_hash(htmlspecialchars($password),PASSWORD_BCRYPT, ['cost' => 10]);
        try{
            $sql = "INSERT INTO users (last_name, first_name, username, passwd, email, permission_lvl, is_active)
            VALUES (:last_name, :first_name, :username, :passwd, :email, 0, 'True')";
            $stmt = $this->container->db->prepare($sql);
            $req = $stmt->execute([
                'last_name' => $lastname,
                'first_name' => $firstname,
                'username' => $username,
                'passwd' => $password,
                'email' => $email
            ]);
            return true;
        }
        catch(Exception $e){
            return false;
        }
    }

    //edit a user in the database
    public function editUser($id, $firstname, $lastname, $email, $username, $password, $permission) : bool{
        $id = htmlspecialchars($id);
        $firstname = htmlspecialchars($firstname);
        $lastname = htmlspecialchars($lastname);
        $email = htmlspecialchars($email);
        $username = htmlspecialchars($username);
        if (empty($password)) {
          $passwordEmpty = true;
        }
        $password = password_hash(htmlspecialchars($password),PASSWORD_BCRYPT, ['cost' => 10]);
        $permission = htmlspecialchars($permission);

        if (!$passwordEmpty) {
        try{
            $sql = 'UPDATE users SET username = :username, first_name = :firstname, last_name = :lastname, passwd = :password, email = :email, permission_lvl = :permission WHERE user_id = :id'; //alter table to do
            $stmt= $this->container->db->prepare($sql);
            $stmt->bindValue('id', $id, \PDO::PARAM_INT);
            $stmt->bindValue('lastname', $lastname, \PDO::PARAM_STR);
            $stmt->bindValue('firstname', $firstname, \PDO::PARAM_STR);
            $stmt->bindValue('username', $username, \PDO::PARAM_STR);
            $stmt->bindValue('password', $password, \PDO::PARAM_STR);
            $stmt->bindValue('email', $email, \PDO::PARAM_STR);
            $stmt->bindValue('permission', $permission, \PDO::PARAM_INT);  //replaced STR with INT, not sure tho, Jam.
            $stmt->execute();
            return true;
        }
        catch(Exception $e){
            return false;
        }
      }
      else {
        try{
            $sql = 'UPDATE users SET username = :username, first_name = :firstname, last_name = :lastname, email = :email, permission_lvl = :permission WHERE user_id = :id'; //alter table to do
            $stmt= $this->container->db->prepare($sql);
            $stmt->bindValue('id', $id, \PDO::PARAM_INT);
            $stmt->bindValue('lastname', $lastname, \PDO::PARAM_STR);
            $stmt->bindValue('firstname', $firstname, \PDO::PARAM_STR);
            $stmt->bindValue('username', $username, \PDO::PARAM_STR);
            $stmt->bindValue('email', $email, \PDO::PARAM_STR);
            $stmt->bindValue('permission', $permission, \PDO::PARAM_INT);  //replaced STR with INT, not sure tho, Jam.
            $stmt->execute();
            return true;
        }
        catch(Exception $e){
            return false;
        }
      }
    }

    //delete the user in the database, (change a flag, not a real deletion)
    public function deleteUser($username) : bool{
      $username = htmlspecialchars($username);
      try{
          $sql = "UPDATE users SET is_active = 'False' WHERE username = :username";
          $stmt= $this->container->db->prepare($sql);
          $stmt->bindValue('username', $username, \PDO::PARAM_STR);
          $stmt->execute();
          return true;
      }
      catch(Exception $e){
          return false;
      }
  }

    // Display users
    public function displayUsers(){
      $sql = "SELECT u.user_id, u.username, u.permission_lvl
      FROM users u
      WHERE u.is_active = 'True'";
      $stmt= $this->container->db->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll();

      return $result;
    }

    public function displayNumUsers(){
      $sql = "SELECT COUNT(*) FROM users WHERE is_active = 'True'";
      $stmt= $this->container->db->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetch(\PDO::FETCH_ASSOC);
      return $result;
    }

    //New Jam : 13/03/2019 11H27
    public function getUserInfoById($id){
      $id = htmlspecialchars($id);
      $sql = "SELECT user_id, last_name, first_name, username, passwd, email, permission_lvl FROM users WHERE user_id = :id";
      $stmt= $this->container->db->prepare($sql);
      $stmt->bindValue('id', $id, \PDO::PARAM_INT);
      $stmt->execute();
      $result = $stmt->fetchAll();
      return $result;
    }
}
