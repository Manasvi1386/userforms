<?php


// PHP code for handling registration and login
if (isset($_POST)) {
    // Database connection code here
    $pdo = new PDO("mysql:host=localhost;dbname=userFormDB", "root", "");
    if (isset($_POST['register'])){
        // Registration
        $username        = $_POST['username'];
        $password        = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $email           = $_POST['email'];
        $mobile          = $_POST['mobile'];


         // Define the directory where you want to upload the file
        $uploadDirectory = "uploadDir/" ;
        $targetFile      = $uploadDirectory . basename($_FILES['userFile']['name']);
        // Check if the directory exists, and create it if not
        if (!is_dir($uploadDirectory)) {
           
            mkdir($uploadDirectory, 0777, true);
        }
        else{  
            if(move_uploaded_file($_FILES['userFile']['tmp_name'], $targetFile)) {
                    // File uploaded successfully
                $filePath = $targetFile;
                /** Check if username already exist */
                $sql ="SELECT * from users where username = ? ";
                $statement =  $pdo->prepare($sql);
                $statement->execute([$username]);
                //print_r($execution); exit;
                $userData = $statement->fetch();
                if($userData){
                    echo "Username Already Exist";
                }
                else{
                    $sql = "INSERT INTO users(username, password, file_path, email, mobile) VALUES (?, ?, ?, ?, ?)";
                    $stmt = $pdo->prepare($sql);
                    if ($stmt->execute([$username, $password, $filePath, $email, $mobile])){
                        // echo "Registration successful!";
                        header("Location: userFormPage.php?msg=Registration successful", true, 301);

                    } else {
                       echo "Registration Failed!";

                    }
                }
            }
            else{
                   echo "Failed to upload the file";
                }
        }

    }elseif (isset($_POST['login'])) {
        // Login
        $username = $_POST['username'];
        $password = $_POST['password'];
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            // echo "Login successful!";
            header("Location: userFormPage.php?msg=Login Successfull", true, 301);

        } else {
            echo "Invalid Username Or Password.";
        }
    }
}
include('userLoginHtml.php');
?>
