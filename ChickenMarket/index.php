<?php 
include "php/route.php";
$currentUserRole = 1;//Temporarily used to define the current user's role. If role is 1, it is admin, else would be other role.
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
    <link rel="stylesheet" href="css/index.css?v=<?= time() ?>">
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
        <div class="overlay">
            <h1>Chicken Market</h1>
            <p>We are an E-commerce website selling chicken from multiple sellers on our site.</p>    
        </div>
        <div class="hero-image">
            <img src="images/Hero.jpeg" alt="Home Picture" class="hero-image">
        </div>
    </header>
    <section>
<!--Product-->
        <h2>Products</h2>
        <!--Show all product-->
        <div id="all-product-display" class="container"></div>

        <!--admin editing tool-->
        <!--Admin tools only admin can use, identified with the role designation 1.
        To prevent other user role from accessing, check if it is 1. If it is, have the admin
        tools printed. if else, this will be skipped and inaccessible, thus preventing injection
        from unauthorized users.-->
        <?php
        if ($currentUserRole === 1) {
            echo '<div class="decorbox" id="addProductSection">
                    <h2>Add New Product (Admin Tool)</h2>
                    <form id="insertForm" action="api/insertProduct.php" method="POST" enctype="multipart/form-data">
                        <input type="text" name="ProdName" placeholder="Product Name" required><br><br>
                        <input type="text" name="ProdShop" placeholder="Shop Name" required><br><br>
                        <input type="text" name="ProdDesc" placeholder="Description"><br><br>
                        <input type="number" name="ProdPrice" placeholder="Price" step="0.01" min="0" required><br><br>
                        <input type="text" name="ProdKeyword" placeholder="Keywords"><br><br>
                        <input type="text" name="ProdPokemon" placeholder="Pokemon Card"><br><br>
                        <input type="text" name="ProdLoc" placeholder="Location"><br><br>
                        <input type="file" name="ProdPic" accept="image/*" required><br><br>

                        <button type="submit">Submit</button>
                    </form>
                </div>';
        }?>
        <!--To add product-->
        
    </section>
    <article>
        <h2>Community Notice</h2>
        <div id="all-article-display" class="flex-container"></div>
    </article>
    <footer>
        <div class="footer-links">
            <a href="#">Terms of Use</a>
            <a href="#">Privacy Policy</a>
        </div>
        <br>
        <p>Copyright &copy; Chicken Shop 2025</p>
    </footer>

    <script src="js/getAllProduct.js?v=<?= time() ?>"></script>
    <script src="js/getAllArticle.js?v=<?= time() ?>"></script>
    <script src="js/insertProduct.js?v=<?= time() ?>"></script>
</body>
</html>