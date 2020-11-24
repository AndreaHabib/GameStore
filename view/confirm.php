<!DOCTYPE html>

<?php
require("config.php");
if(!isset($_SESSION['source']))
{
    header("Location:index.php");
    exit();
}


$query = "SELECT products.product_id, products.name, products.price FROM products INNER JOIN shopping_cart ON 
products.product_id = shopping_cart.product_id WHERE shopping_cart.user_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$sumTotal=0;

if(isset($_GET['delete']) && isset($_GET['product_id'])){


	$query = "DELETE FROM shopping_cart WHERE product_id = ?";
	$stmt = $conn->prepare($query);
	$stmt->bind_param("i", $_GET['product_id'] );
	$stmt->execute();
	
	header("Location:cart.php");
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
        href="stylesheet/confirm.css"
        />
        <!-- <link
        rel="stylesheet"
        href="stylesheet/main.css"
        />
        <link
        rel="stylesheet"
        href="stylesheet/cart.css"
        /> -->
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">

        <title>Checkout</title>
    </head>

    <body>

        <?php include 'components/navbar.php';?>

    

        <div style="margin-bottom: 50px;"></div>
        <h2>Thank you for shopping at GamerStore, you will be redirected back to the homepage in 5 seonds. </h2>
        <?php header( "refresh:5;url=index.php" );?>
                    

        <?php include 'components/footer.html';?>

    </body>



</html>