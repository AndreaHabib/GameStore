<?php
$errors = []; //empty errors array
session_start(); //start session
if($_SERVER["REQUEST_METHOD"] == "POST"){ //check if request method is POST

  require("config.php"); //make sure config is working properly

  function cleanInput($data){ //sanitize data 
      $data = trim($data);
      $data = stripslashes($data);
      $data = htmlspecialchars($data);
      return $data;
  }

  $firstname = cleanInput($_POST['firstname']);
  $lastname = cleanInput($_POST['lastname']);
  $phoneNumber = cleanInput($_POST['phoneNumber']);
  $street = cleanInput($_POST['street']);
  $city = cleanInput($_POST['city']);
  $state = cleanInput($_POST['state']);
  $zip = cleanInput($_POST['zip']);
  $email = cleanInput($_POST['email']);
  $password = cleanInput($_POST['password']);
  $passwordCHECK = cleanInput($_POST['passwordCHECK']);
  $admin = 0;

  //phone regex pattern
  $patternPhone = "/^[1-9][0-9]{2}(\.|\-)[0-9]{3}(\.|\-)[0-9]{4}$/";

  //all patterns needed
  $patternPass = "/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/";
  //Minimum eight characters, at least one letter, one number and one special character:
      
  if(!filter_var($email, FILTER_VALIDATE_EMAIL)){ //check for email format
    $errors['email'] = "Incorrect email format!";
        
  }

  if($password !== $passwordCHECK){ //check for password retyped
    $errors['rePass'] = "Password doesn't match!";
      
  }

  if(!preg_match($patternPass, $password)){ //check for password pattern
    $errors['password'] = "Password must be minimum eight characters, at least one letter, one number and one special character!";
        
  }

  if(!preg_match($patternPhone, $phoneNumber)){ //check for phone pattern
    $errors['phoneNumber'] = "Phone Number must be in this format: xxx.xxx.xxxx or xxx-xxx-xxxx";
        
  }

  $queryCheck = "SELECT email FROM users WHERE email=? LIMIT 1"; //query to check email
        
  //Checking if Aleady signed up
  $stmt = $conn->prepare($queryCheck);
  $stmt->bind_param('s', $email);
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($email);
  if($stmt->num_rows>0){
    $errors['signedUP'] = "Account already exits!";
  }

  if(count($errors) === 0){ //if no errors

    $password = password_hash($password, PASSWORD_DEFAULT); //hash password

    $query = "INSERT INTO users(PASSWORD, FIRST_NAME, LAST_NAME, PHONE_NUM, STREET, CITY, STATE, ZIP, EMAIL, ADMIN) VALUES(?,?,?,?,?,?,?,?,?,?)"; //insert all the data into table
      
    $stmt2 = $conn->prepare($query); //prepare
        
    $stmt2->bind_param('sssssssssi', $password, $firstname, $lastname, $phoneNumber, $street, $city, $state, $zip, $email, $admin); //bind
        
    if($stmt2->execute()) { //if execute is successful
         
      $user_id = $conn->insert_id; //save user_id
      $_SESSION['user_id'] = $user_id;
      $_SESSION['firstname'] = $firstname; // setting the session variables
      $_SESSION['lastname'] = $lastname;
      $_SESSION['email'] = $email;
      $_SESSION['isAdmin'] = $admin;
      if(isset($_SESSION['guest'])) {
        unset($_SESSION['guest']); //if user logs in while being a guest, unset guest
    }
      $_SESSION['source'] = "signUp";

      header("Location:../profile.php"); //direct to profile
          
    }
    else {
      $errors['db_error'] = "Database error"; //database error
    }
  }
}
?>