<?php
    include "php/route.php"
    /*• Searching with live filtering
• Searching by at least 3 Fields (For example, Date Added, Name, Ra$ng)
• NEW: If a search query doesn't return exact results, suggest the closest matching
products/ar+cles/users/posts based on name similarity*/
?>


<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?= $baseUrl ?>/" />
    <title>Chicken Market</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/utility-style.css">
    <link rel="stylesheet" href="css/search.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.2/papaparse.min.js"></script>
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
    <!--Receiving and keeping input value when submitting the input. It will go to search/?query=__-->
<!--Wrap this in a form with method GET. use it to get the item from csv-->
    <input class="input" type="text" id="searchInput" placeholder="Search...">
    <button onclick="filterItems()" class="search-btn"><i class="fas fa-search"></i></button>
    
<!--Use GET parameter -->
    
    <!--Display the last 3 searches-->
    <div class="recent-searches" id="recent-searches"></div>
    
    <!--This will get the result of the input after search, reading the search/?query=__ to get the data with the keywords that contains the queried word
    It will use getSearch function from getData.php.-->
    <div id="results" class="result"></div>
<!--Use php for loop to get the data based on the parameter-->
    
    <script src="js/search.js" defer></script>
</body>
</html>