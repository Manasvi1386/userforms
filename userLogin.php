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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login and Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .registration-container {
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border-radius: 8px;
            width: 300px;
        }

        h2 {
            text-align: center;
            color: #333333;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555555;
        }

        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 16px;
            border: 1px solid #cccccc;
            border-radius: 4px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #4caf50;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>
    <script src="jquery-3.7.1.min.js"></script>
</head>
<body>
<div class="registration-container">
    <span class="error">
        <?php 
        if(isset($message['successMessage'])){
           echo "<span colo='red>'".$message['successMessage']."<span>";
        }
        if(isset($message['errorMessage'])){
            echo "<span colo='red>'".$message['errorMessage'].'<span>';
        }
    ?></span>
    <div class="userRegdiv">
    <h2>Register</h2>

        <form action="userLogin.php" method="post" enctype="multipart/form-data">
            <label for="register-username">Username:</label>
            <input type="text" id="register-username" name="username" required><br>
            <label for="register-password">Password:</label>
            <input type="password" id="register-password" name="password" required><br>
            <label for="register-password">Confirm Password:</label>
            <input type="password" id="register-confirm-password" onblur="passwordcheck()"><br>
            <span id="passwordError" style="color:red;"></span>
            <label for="register-email" id="register-email">Email :</label>
            <input type="text" id="register-email"  name="email">
            <label for="register-mobile" id="register-mobile">Mobile :</label>
            <input type="text" id="register-mobile" name="mobile">
            <label for="register-file" id="register-file" required>Upload File:</label><input type="file" name="userFile"><br>
            <input type="submit" name="register" value="Register" >
            <input type="button" id="login-form" name="login-form" value="( Already have an account?)SignIn" onclick="signInForm()">
        </form>
        <hr>

    </div>
    <div class="loginClass" style="display:none;">
    <h2>Login</h2>
    <form action="userLogin.php" method="post" >
        <label for="login-username">Username:</label>
        <input type="text" id="login-username" name="username" required><br>
        <label for="login-password">Password:</label>
        <input type="password" id="login-password" name="password" required><br>
        <input type="submit" name="login" value="Login">
        <input type="button" id="register-form" name="register-form" value="( Don't have an account?)Register" onclick="RegisterForm()">
    </form>
    </div>
</div>
</body>
</html>
<script>

function signInForm(){
    // alert("ssss");return false;
    $(".loginClass").show();
    $(".userRegdiv").hide();
}

function RegisterForm(){
    // alert("ssss");return false;
    $(".loginClass").hide();
    $(".userRegdiv").show();
}

function passwordcheck(){
    var password = $("#register-password").val();
    var confirmpassword = $("#register-confirm-password").val();
    if (password === confirmpassword) {
                    // Passwords match, you can proceed with form submission or other actions
                    $('#passwordError').text('');
                    return true;
                } else {
                    // Passwords do not match, display an error message
                    $('#passwordError').text('Passwords do not match');
                    return false;
                }
}
</script>
