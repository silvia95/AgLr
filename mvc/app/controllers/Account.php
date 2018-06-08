<?php
/**
 * Created by PhpStorm.
 * User: Ana-Maria
 * Date: 06.06.2018
 * Time: 22:04
 */
class Account extends Controller
{

    public function RegisterUser()
    {
        $user = json_decode($_POST['user']);
        if (!filter_var($user->Email, FILTER_VALIDATE_EMAIL)) {
            echo "$user->Email is not a valid email";
            return;
        }
        $servername = "localhost";
        $username = "root";
        $dbname = "aglr";
// Create connection
        $conn = new mysqli($servername, $username, null ,$dbname);

// Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        //echo "Connected successfully. ";
        $userEmail = $conn->prepare("SELECT * FROM users where Email = ?");
        $userEmail->bind_param('s',$user->Email);
        $userEmail->execute();
        $userEmail->store_result();
        if($userEmail->num_rows) {
            echo "User already exists";
            $userEmail->close();
            return;
        }
        else{
            $userEmail->close();
            $insertUser = $conn->prepare ("INSERT INTO users (FirstName, LastName, Email, Password) VALUES(?,?,?,?)");
            if($insertUser == false)  {
                print_r($conn->error);
                return;
            }
            $passRef = sha1($user->Password);
            $insertUser->bind_param('ssss',$user->FirstName, $user->LastName, $user->Email, $passRef);
            $insertUser->execute();
            echo "User Created";
        }

        $conn->close();
    }
    public function Login()
    {
        $user = json_decode($_POST['user']);
        if (!filter_var($user->Email, FILTER_VALIDATE_EMAIL)) {
            echo "$user->Email is not a valid email";
            return;
        }
        $servername = "localhost";
        $username = "root";
        $dbname = "aglr";
// Create connection
        $conn = new mysqli($servername, $username, null ,$dbname);

// Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        //echo "Connected successfully. ";
        $userEmail = $conn->prepare("SELECT * FROM users where Email = ? and Password = ?");
        $passwd = sha1($user->Password);
        $userEmail->bind_param('ss',$user->Email, $passwd);
        $userEmail->execute();
        $userEmail->store_result();
        if($userEmail->num_rows > 0) {
            echo "You have been successful connected!";
            $userEmail->close();
            return;
        }
        else{
            echo "Incorrect email or password!";
            $userEmail->close();
        }

        $conn->close();
    }
}