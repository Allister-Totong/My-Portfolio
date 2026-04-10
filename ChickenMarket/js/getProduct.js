const apiKey = '4c28ed703c2a0315afe98064302001d0';

function getProductPicture(productID, callback) {
    fetch(`api/productpicture/?id=${productID}`)
        .then(response => response.json())
        .then(data => {
            const result = `<img src="${data.imagePath}" alt="${data.alt}" class="${data.class}">`;
            callback(result);
        });
}

function getAverageRating(productID, callback) {
    return fetch('api/comment')
    .then(response => response.json())
    .then(comments => {
        const matchingComments = comments.filter(p => Number(p.ProductID) === Number(productID));
        if (matchingComments.length === 0) {
            const rating = "No Review";
            callback(rating);
            return;
        }
        const totalRating = matchingComments.reduce((sum, comment) => sum + Number(comment.Rating), 0);
        const averageRating = totalRating / matchingComments.length;
        const roundedRating = Number(averageRating.toFixed(1));

        callback(roundedRating);
    })
}

function getWeather(city, callback) {
    fetch(`https://api.openweathermap.org/data/2.5/weather?q=${city}&appid=${apiKey}&units=metric`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`City not found (${response.status})`);
            }
            return response.json();
        })
        .then(data => {
            const description = data.weather[0].description;
            const temp = data.main.temp;
            const html = `<p><b>Weather:</b> ${description}, <b>Temp:</b> ${temp}°C</p>`;
            callback(html);
        })
        .catch(error => {
            callback(`<p>Error getting weather: ${error.message}</p>`);
        });
}

function getProduct(productID) {
    fetch('api/product')
        .then(response => response.json())
        .then(products => {
            const product = products.find(p => Number(p.ProductID) === Number(productID));
            if (!product) {
                document.getElementById("product-display").innerHTML = "<p>Product not found.</p>";
                return;
            }

            getProductPicture(product.ProductID, function (productImage) {
                getAverageRating(product.ProductID, function (averageRating) {
                    getWeather(product.Location || 'Jakarta', function (weatherHTML) {
                        let productHTML = `
                            <div class='picture'>
                                ${productImage}
                            </div>
                            <div class='detail'>
                                <h1>${product.Name}</h1>
                                <h3>Shop: ${product.ShopName}</h3>
                                <p><b>Price: $${product.Price}</b></p>
                                <p><b>Rating: ${averageRating}</b> <i class="fa-solid fa-star"></i></p>
                                <p>Keywords: ${product.Keyword}</p>
                                <p>${product.Description}</p>
                                <h3>What weather is your seller having now?</h3>
                                <h3>${product.Location}:</h3>
                                ${weatherHTML}
                            </div>
                        `;
                        document.getElementById("product-display").innerHTML = productHTML;

                        let editFormHTML = `
                            <div class="decorbox">
                                <h2>Edit Product (Admint Tools)</h2>
                                <form id="edit-product-form">
                                    <label>Name:</label><br>
                                    <input type="text" name="Name" value="${product.Name}"><br><br>

                                    <label>Shop Name:</label><br>
                                    <input type="text" name="ShopName" value="${product.ShopName}"><br><br>

                                    <label>Price:</label><br>
                                    <input type="number" name="Price" value="${product.Price}"><br><br>

                                    <label>Keyword:</label><br>
                                    <input type="text" name="Keyword" value="${product.Keyword}"><br><br>

                                    <label>Description:</label><br>
                                    <textarea name="Description" rows="5">${product.Description}</textarea><br><br>

                                    <label>Location:</label><br>
                                    <input type="text" name="Location" value="${product.Location || ''}"><br><br>

                                    <button type="submit" class="save-btn">Save Changes</button>
                                    <button type="button" id="delete-product-btn" class="delete-btn">Delete Product</button>
                                </form>
                            </div>
                        `;
                        document.getElementById("edit-product").innerHTML = editFormHTML;

                        document.getElementById("edit-product-form")?.addEventListener("submit", function (e) {
                            e.preventDefault();
                            
                            const formData = new FormData(this);
                            const productData = {};
                            formData.forEach((value, key) => {
                                productData[key] = value;
                            });
                        
                            // Add ProductID manually
                            productData.ProductID = productID;
                        
                            fetch('api/editProduct.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify(productData)
                            })
                            .then(response => response.json())
                            .then(result => {
                                if (result.success) {
                                    alert('Product updated successfully!');
                                    getProduct(productID);
                                } else {
                                    alert('Error updating product.');
                                }
                            })
                            .catch(error => {
                                alert('Error updating product: ' + error.message);
                            });
                        });

                        document.getElementById("delete-product-btn")?.addEventListener("click", function() {
                            if (confirm("Are you sure you want to delete this product? This action cannot be undone.")) {
                                fetch('api/deleteProduct.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({ ProductID: productID })
                                })
                                .then(response => response.json())
                                .then(result => {
                                    if (result.success) {
                                        alert('Product deleted successfully!');
                                        window.location.href = '';
                                    } else {
                                        alert('Error deleting product: ' + (result.error || 'Unknown error'));
                                    }
                                })
                                .catch(error => {
                                    alert('Error deleting product: ' + error.message);
                                });
                            }
                        });

                        getRecommendedProducts(product.ProductID, product.Keyword, function(recommendationsHTML) {
                            document.getElementById("product-recommendations").innerHTML = recommendationsHTML;
                        });
                    });
                });
            });
        })
}

function getRecommendedProducts(currentProductID, keyword, callback) {
    fetch('api/product')
        .then(response => response.json())
        .then(products => {
            // Filter products with same keyword, exclude current product, and limit to 3
            const recommended = products.filter(p => 
                p.Keyword === keyword && 
                Number(p.ProductID) !== Number(currentProductID)
            ).slice(0, 3);
            
            if (recommended.length === 0) {
                callback('<p>No recommendations available</p>');
                return;
            }

            // Create an array of promises for getting each product's picture
            const recommendationPromises = recommended.map(product => {
                return new Promise(resolve => {
                    getProductPicture(product.ProductID, pictureHTML => {
                        resolve({
                            ...product,
                            pictureHTML
                        });
                    });
                });
            });

            // Wait for all pictures to load
            Promise.all(recommendationPromises)
                .then(recommendedWithPictures => {
                    let html = '<div class="recommendations"><h3>Recommended Products</h3><div class="recommended-products">';
                    
                    recommendedWithPictures.forEach(product => {
                        html += `
                            <div class="recommended-product">
                                <a href="product.php?id=${product.ProductID}">
                                    ${product.pictureHTML}
                                    <h4>${product.Name}</h4>
                                    <p>$${product.Price}</p>
                                </a>
                            </div>
                        `;
                    });
                    
                    html += '</div></div>';
                    callback(html);
                });
        });
}

document.addEventListener("DOMContentLoaded", function () {
    const urlParams = new URLSearchParams(window.location.search);
    const productID = urlParams.get('id');
    if (productID) {
        getProduct(productID);
        document.getElementById("submitComment")?.addEventListener("click", function () {
            setTimeout(() => getProduct(productID), 1000);
        });
    }
});
