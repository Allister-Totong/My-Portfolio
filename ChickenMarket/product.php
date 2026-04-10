<?php
    include "php/route.php";
    const baseUrl = '<?= $baseUrl ?>';
    $productID = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : null;
    $userid = 1; //temporary set to have user of website as user 1 a.k.a. admin.
    $currentUserRole = 1; //Temporarily used to define the current user's role. If role is 1, it is admin, else would be other role.
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?= $baseUrl ?>/" />
    <title>Product Details | Chicken Market</title>
    <link rel="stylesheet" href="css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="css/utility-style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="css/product.css?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>
    <nav class="navbar">
        <div class="nav-container">
            <a base href="cart"class="left-navbar" tabindex="2"><i class="fa-solid fa-bars"></i></a>
            <a base href="cart" class="left-navbar" tabindex="2">cart</a>
            <a base href="index" tabindex="1"><img class="logo" src="images/Logo.png" alt="Logo"></a>
            <a base href="search" class="search-icon" tabindex="3"><i class="fa-solid fa-magnifying-glass"></i></a>
            <a base href="login"class="right-navbar" tabindex="4"><i class="fa-solid fa-arrow-right-from-bracket"></i></a>
            <a base href="login" class="right-navbar" tabindex="4">Sign In</a>
        </div>
    </nav>
    <header>
        <img src="images/Hero.jpeg" alt="Home Picture" class="hero-image">
    </header>

    <!--product data-->
    <section>
        <!--Weather API are included in product-display from getProduct.js-->
        <div id="product-display" class="container"></div>
        
        <!--admin editing tool-->
        <!--Admin tools only admin can use, identified with the role designation 1.
        To prevent other user role from accessing, check if it is 1. If it is, have the admin
        tools printed. if else, this will be skipped and inaccessible, thus preventing injection
        from unauthorized users.-->
        <?php
        if ($currentUserRole === 1) {
            echo '<div id="edit-product"></div>';
        }?>
        
        
        
        <!--put in cart-->
        <div class="decorbox">
            <form action="api/intocart.php" method="POST">
                <input type="hidden" name="userid" value="<?= $userid ?>"> 
                <input type="hidden" name="productid" value="<?= $productID ?>">
                
                Quantity: <input type="number" name="quantity" value="1" min="1" required>
                <button type="submit">Add to Cart</button>
            </form>
        </div>
        
        <!--call serverside pokemon API-->
        <div class="container">
            <?php
            include "api/getPokemon.php";
            $productId = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : null;
            $getData->getPokemonCard($productId);
            ?>
        </div>
    </section>

    <!--recommendations-->
    <section>
        <h2>Recomended Products</h2>
        <div class="flex-container">
            <div id="product-recommendations" class="container"></div>
        </div>
    </section>

    <!--reviews-->
    <section class="reviews-section">
        <h2>Customer Reviews</h2>
        <div id="all-review-display">
            <!-- Reviews will load here dynamically -->
        </div>

        <div class="review">
            <form action="api/saveComment.php" method="POST">
                <input type="hidden" id="productID" name="productID" value="<?= $productID ?>">
                <input type="hidden" id="userid" name="userid" value="<?= $userid ?>">
                
                <div>
                    <label for="rating">Your Rating:</label>
                    <select id="rating" name="rating" class="form-control">
                        <option value="">Select rating</option>
                        <?php for ($i = 1; $i <= 5; $i += 0.5): ?>
                            <option value="<?= $i ?>"><?= $i ?> ★</option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div>
                    <label for="comment">Your Review:</label>
                    <textarea id="comment" name="comment" class="form-control" placeholder="Write your review..."></textarea>
                </div>
                
                <button type="submit" class="submit-btn">Submit Review</button>
            </form>
        </div>
    </section>

    <footer>
        <div class="footer-links">
            <a href="">Terms of Use</a>
            <a href="">Privacy Policy</a>
        </div>
        <br>
        <p>Copyright &copy; Chicken Shop 2025</p>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.2/papaparse.min.js"></script>
    <script src="js/getProduct.js?v=<?= time() ?>"></script>
    <script src="js/getComment.js?v=<?= time() ?>"></script><!--the time are added due to caching issue-->

</body>
</html>