<?php
class GetPokemon {
    private $products = [];

    public function __construct() {
        $apiUrl = 'http://localhost/chickenMarket/api/product'; // Change if needed
        $response = @file_get_contents($apiUrl);

        if ($response !== false) {
            $this->products = json_decode($response, true) ?? [];
        }
    }

    public function getPokemonCard($productId) {
        if ($productId === null) {
            echo "No product ID provided.";
            return;
        }

        // Find the product by ProductID
        $product = null;
        foreach ($this->products as $p) {
            if (isset($p['ProductID']) && $p['ProductID'] == $productId) {
                $product = $p;
                break;
            }
        }

        if (!$product) {
            echo "Product not found.";
            return;
        }

        $name = htmlspecialchars($product['Pokemon'] ?? 'No Pokemon');
        if ($name === 'No Pokemon') {
            echo "No Pokémon assigned to this product.";
            return;
        }

        // Fetch Pokémon data
        $pokemonResponse = @file_get_contents("https://pokeapi.co/api/v2/pokemon/" . strtolower($name));

        if ($pokemonResponse !== false) {
            $data = json_decode($pokemonResponse, true);
            $imageUrl = $data['sprites']['front_default'] ?? '';

            if ($imageUrl) {
                echo "
                    <img src='$imageUrl' alt='$name' class='picture'>
                    <h3>From this purchase, you'll get a $name card from Chicken Market X Pokemon Collab!!</h3>
                ";
            } else {
                echo "Image not available for this Pokémon.";
            }
        } else {
            echo "Pokémon not found!";
        }
    }
}

// Create the object so you can use it immediately
$getData = new GetPokemon();
?>
