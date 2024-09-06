<?php 
require_once __DIR__.'/../../../database/dbconnection.php';
include_once __DIR__.'/../../../config/settings-configuration.php';
class ADMIN
{
    private $conn;
    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->dbconnection();
    }

    public function addAdmin($csrf_token, $username, $email, $password)
    {
        $stmt = $this->conn->prepare("SELECT * FROM user WHERE email = :email");
        $stmt->execute(array(":email" => $email));
    
        if ($stmt->rowCount() > 0) 
        {    
            echo "<script>alert('Email already exists.'); window.location.href = '../../../';</script>";
            exit;
        }
    
        if (!isset($csrf_token) || !hash_equals($_SESSION['csrf_token'], $csrf_token))
        {
            echo "<script>alert('Invalid CSRF token!'); window.location.href = '../../../';</script>";
            exit;
        }
    
        unset($_SESSION['csrf_token']);

        $hash_password = md5($password);
        $stmt = $this->runQuery('INSERT INTO user (username, email, password) VALUES (:username, :email, :password)');
        
        $exec = $stmt->execute(array
        (
            ":username" => $username,
            ":email" => $email,
            ":password" => $hash_password
        ));
    
        if ($exec)
        {
            echo "<script>alert('Admin Added Successfully!'); window.location.href = '../../../';</script>";
            exit;
        }
        else
        {
            echo "<script>alert('Error Adding admin!'); window.location.href = '../../../';</script>";
            exit;
        }
    }    
    public function adminSignIn ($email, $password , $csrf_token)
    {
        try{
            if (!isset($csrf_token) || !hash_equals($_SESSION['csrf_token'], $csrf_token))
            {
                echo "<script>alert('Invalid CSRF token!'); window.location.href = '../../../';</script>";
                exit;
            }

            unset($_SESSION['csrf_token']);

            $stmt = $this->conn->prepare("SELECT * FROM user WHERE email = :email");
            $stmt->execute(array(":email"=> $email));
            $userrow = $stmt->fetch(PDO::FETCH_ASSOC);
            
              if($stmt->rowCount() == 1 && $userrow['password'] == md5($password))
                {
                  $activity = "Has successfully signed in!";
                  $user_id = $userrow['id'];
                  $this->logs($activity,$user_id);

                  $_SESSION['adminSession'] = $user_id;

                  echo "<script>alert ('WELCOME.'); window.location.href = '../'</script>";
                  exit;

                }  else 
                { 
                  echo "<script>alert ('Invalid Credentials!'); window.location.href = '../../../'</script>";
                  exit;
                }
             }catch(PDOException $ex)
             {
              echo $ex->getMessage(); 
             }
    }
    public function adminSignOut ()
    {
        unset($_SESSION['adminSession']);
        echo "<script>alert('Sign Out Successfully!'); window.location.href = '../../../';</script>";
        exit;
    }
    public function logs ($activity,$user_id)
    {
        $stmt = $this->conn->prepare("INSERT INTO logs (user_id, activity) VALUES (:user_id, :activity)");
        $stmt->execute(array(":user_id"=> $user_id,":activity" => $activity));
    }

    public function IsUserLoggedIn()
    {
        if(isset($_SESSION['adminSession']))
        {
            return true;
        }
    }

    public function redirect()
    {
        echo "<script>alert('Admin must log in first'); window.location.href = '../../../';</script>";
        exit;
    }

    public function runQuery ($sql)
    {
        $stmt = $this->conn->prepare($sql);
        return $stmt;
    }
}

if(isset($_POST['btn-signup']))
{
    $csrf_token = trim($_POST['csrf_token']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $addAdmin = new ADMIN();
    $addAdmin ->addAdmin($csrf_token,$username, $email, $password);
}

if(isset($_POST['btn-signin']))
{
    $csrf_token = trim($_POST['csrf_token']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $adminSignin = new ADMIN();
    $adminSignin->adminSignIn($email,$password,$csrf_token);
}

if(isset($_GET['admin_signout']))
{
    $adminSignout = new ADMIN();
    $adminSignout->adminSignOut();
}
?>