function getProductPicture(productID, callback) {
    fetch(`api/productpicture/?id=${productID}`)
        .then(response => response.json())
        .then(data => {
            const result = `<img src="${data.imagePath}" alt="${data.alt}" class="${data.class}">`;
            callback(result);
        })
        .catch(error => {
            callback(`<img src="default.jpg" alt="No image available">`);
        });
}

function getAverageRating(productID, callback) {
    fetch('api/comment')
        .then(response => response.json())
        .then(comments => {
            const matchingComments = comments.filter(p => Number(p.ProductID) === Number(productID));
            if (matchingComments.length === 0) {
                callback("No Review");
                return;
            }
            const totalRating = matchingComments.reduce((sum, comment) => sum + Number(comment.Rating), 0);
            const averageRating = totalRating / matchingComments.length;
            callback(Number(averageRating.toFixed(1)));
        })
        .catch(error => {
            callback("No Review");
        });
}

function getAllProducts() {
    fetch('api/product')
        .then(response => response.json())
        .then(fetchedProducts => {
            products = fetchedProducts;

            const productHTMLPromises = products.map(product => {
                return new Promise(resolve => {
                    getProductPicture(product.ProductID, function (productImage) {
                        getAverageRating(product.ProductID, function (averageRating) {
                            const productHTML = `
                                <a href='product/?id=${product.ProductID}' class='thumbnail-link'>
                                    <div class='product'>
                                        <div class='picture'>${productImage}</div>
                                        <div class='detail'>
                                            <h1>${product.Name}</h1>
                                            <h3>Shop: ${product.ShopName}</h3>
                                            <p><b>Price: $${product.Price}</b></p>
                                            <p><b>Rating: ${averageRating}</b> <i class="fa-solid fa-star"></i></p>
                                            <p>Keywords: ${product.Keyword}</p>
                                            <p>${product.Description}</p>
                                        </div>
                                    </div>
                                </a>
                            `;
                            resolve(productHTML);
                        });
                    });
                });
            });

            Promise.all(productHTMLPromises).then(allHTML => {
                document.getElementById("all-product-display").innerHTML = allHTML.join('');
            });
        })
        .catch(error => {
            console.error("Error fetching products:", error);
        });
}

document.getElementById('insertForm').addEventListener('submit', async (e) => {
    e.preventDefault();  // Prevent default form submission

    const form = document.getElementById('insertForm');
    const formData = new FormData(form);

    // Debugging: Log the FormData to see if the form is being correctly prepared
    console.log("FormData:", formData);

    console.log("Sending request to the PHP file...");
    const response = await fetch('api/insertProduct', {
        method: 'POST',
        body: formData
    });

    // Debugging: Check if the request is successful and log the response
    console.log("Response received:", response);

    const result = await response.json();

    // Debugging: Log the JSON result from PHP
    console.log("Result from PHP:", result);
    getAllProducts()
});

