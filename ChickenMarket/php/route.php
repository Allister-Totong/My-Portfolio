<?php
$baseUrl = (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] === 'chickenmarket.test') 
    ? 'http://chickenmarket.test' 
    : 'http://localhost/chickenMarket';
//I was working in laragon because for some reason my xampp don't auto update consistently. I add this to make the url directory compatible in both
?>
