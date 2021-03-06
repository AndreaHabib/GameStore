<!DOCTYPE html>
<?php

$errors = []; //array for errors
session_start(); //sesion start
require("php/config.php");

if(!isset($_SESSION['source']) && !isset($_SESSION['guest'])) { //checking if user is logged in or guest
    header("Location:index.php");
    exit();
}
if(isset($_SESSION['guest'])) { //if guest send them to games
    header("Location:games.php");
    exit();
}

if(isset($_SESSION['isAdmin'])) { //if not admin send to games
    if($_SESSION['isAdmin'] == false) {
        header("Location:games.php");
        exit();
    }
}

if(!isset($_SESSION['product_id'])) { //if not product_id send to games
    header("Location:games.php");
    exit();
}


if(isset($_FILES["photo"]["type"]) && $_FILES["photo"]["error"] == UPLOAD_ERR_OK){ //button to upload game images

    $save_dir = "images/";
    $target = $save_dir.basename($_FILES["photo"]['name']); //saving name
    
    $fileType = pathinfo($target,PATHINFO_EXTENSION); //saving path of image
    $allowedFormat = array("jpg", "JPG", "png", "gif"); //checking if file is correct format

    if(!in_array($fileType, $allowedFormat)){ //error is not correct format
        $errors['format'] = "Format Not Allowed!";
    }
    else if(!move_uploaded_file($_FILES["photo"]["tmp_name"], $target)) { //error is problem uploading
        $errors['problem'] = "Problem Uploading!";
    }
    else{
        switch($_FILES["photo"]["error"]){ //file upload errors
            case UPLOAD_ERR_INI_SIZE:
                $errors['large1'] = "File is too large!";
            break;
            case UPLOAD_ERR_FORM_SIZE:
                $errors['large2'] = "File is too large!";
            break;
            case UPLOAD_ERR_NO_FILE:
                $errors['nofile'] = "No file was uploaded!";
            break;
        }
    }
    if(count($errors) === 0) { //if there are no errors allow picture path to be insert into db
        $query = "SELECT picture_path FROM pictures WHERE product_id = ?"; //query
        $stmt = $conn->prepare($query); //prepare
        $stmt->bind_param('i', $_SESSION['product_id']); //bind param product_id
        $stmt->execute(); //execute
        $stmt->store_result(); //store
        if($stmt->num_rows>0){ //if image already exsist replace image
            $query = "UPDATE pictures SET picture_path= ? WHERE product_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('si', $target, $_SESSION['product_id']);
            if($stmt->execute()){
                unset($_SESSION['product_id']);
                header("Location:games.php");
            }
        }
        else{ //if no image exsists save image into DB
            $query = "INSERT INTO pictures (picture_path, product_id) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('si', $target, $_SESSION['product_id']);
            if($stmt->execute()){
                unset($_SESSION['product_id']);
                header("Location:games.php");
            }
        }
    }
}


?>
<html lang="en" class="text-primary">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        
        <script src="https://kit.fontawesome.com/ed9be0132c.js" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">


        <link
        rel="stylesheet"
        href="stylesheet/navbar.css"
        />
        <link
        rel="stylesheet"
        href="stylesheet/footer.css"
        />
        <link
        rel="stylesheet"
        href="stylesheet/main.css"
        />
        <link
        rel="stylesheet"
        href="stylesheet/profile.css"
        />
        <link
        rel="stylesheet"
        href="stylesheet/addgame.css"
        />
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">

        <title>Upload Game Picture</title>
    </head>

    <body>

        <?php include 'components/navbar.php';?>

        <div class="container">
        <h3>Game Picture</h3>
        <form id="form" action="gamePic.php" method="post" enctype="multipart/form-data">   
        
            <fieldset>
                <input type="hidden" name="MAX_FILE_SIZE" value="500000" />
                <label for="photo">Upload a Photo</label>
                <input type="file" name="photo" id="photo" value="" />
            <fieldset>
            <fieldset>
                <input type="submit" name="uploadPic" value="Upload" id="form-submit"/>
            </fieldset>

            <?php if(count($errors) > 0): //Display error messages
                ?>
                    <div class="d-flex justify-content-center">
                        <h5 class="bg-danger"><?php echo ''.implode(" " , $errors); ?></h5>
                    </div>
                <?php endif; ?>
        
        </form>
        </div>

        <div style="margin-bottom: 120px;"></div>

        <?php include 'components/footer.html';?>
    </body>



</html>