<?php 
    include "php/route.php";
    $userid = 1;//temporary userid
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?= $baseUrl ?>/" />
    <title>Chicken Market</title>
    <link rel="stylesheet" href="css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="css/utility-style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="css/cart.css?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.2/papaparse.min.js"></script>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="cart" class="left-navbar" tabindex="2"><i class="fa-solid fa-bars"></i></a>
            <a href="cart" class="left-navbar" tabindex="2">Cart</a>
            <a href="index" tabindex="1"><img class="logo" src="images/Logo.png" alt="Logo"></a>
            <a href="search" class="search-icon" tabindex="3"><i class="fa-solid fa-magnifying-glass"></i></a>
            <a href="login" class="right-navbar" tabindex="4"><i class="fa-solid fa-arrow-right-from-bracket"></i></a>
            <a href="login" class="right-navbar" tabindex="4">Sign In</a>
        </div>
    </nav>
    <header>
        <div class="hero-image">
            <img src="images/Hero.jpeg" alt="Home Picture" class="hero-image">
        </div>
    </header>
    <section>
        <!--cart-->
        <div class="cartpanel">
            <h2>Your Cart</h2>
            <div id="cart-display" class="container"></div>
            <div id="address-section">
                <label for="address">Enter your address:</label>
                <input type="text" id="address" placeholder="Enter your delivery address" required />
            </div>
            <button type="button" id="checkout-button">Check Out</button>
        </div>

        <!--order-->
        <div class="cartpanel">
            <h2>Orders</h2>
            <div id="orders-display" class="container"></div>
        </div>
    </section>
    
    <footer>
        <div class="footer-links">
            <a href="#">Terms of Use</a>
            <a href="#">Privacy Policy</a>
        </div>
        <br>
        <p>Copyright &copy; Chicken Shop 2025</p>
    </footer>

    <script src="js/getCart.js?v=<?= time() ?>"></script>
</body>
</html>