let cartItems = [];
const userId = 1; 
getOrder(userId);

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

function updateCartItem(productId, newQuantity) {
    if (newQuantity < 1) return;

    fetch('api/updatecart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            UserID: userId,
            ProductID: productId,
            action: 'update',
            quantity: newQuantity
        })
    })
    .then(response => response.json())
    .then(result => {
        if (!result.success) {
            console.error("Failed to update quantity");
            getCart(userId); 
        }
    })
    .catch(error => {
        console.error('Error updating quantity:', error);
        getCart(userId); 
    });
}

function removeCartItem(productId) {
    fetch('api/updatecart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            UserID: userId,
            ProductID: productId,
            action: 'remove'
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            getCart(userId);
        } else {
            console.error("Failed to remove item");
        }
    })
    .catch(error => {
        console.error('Error removing item:', error);
    });
}

document.addEventListener("DOMContentLoaded", function() {
    getCart(userId);

    document.getElementById('checkout-button').addEventListener('click', function() {
        const address = document.getElementById('address').value;
        
        let missingFields = [];

        if (!address) {
            missingFields.push("Address");
        }
        if (cartItems.length === 0) {
            missingFields.push("Cart Items");
        }

        if (missingFields.length > 0) {
            alert("Please fill in the following before checking out: " + missingFields.join(", "));
            return;
        }
    
        fetch('api/ordering.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                UserID: userId,
                address: address,
                cartItems: cartItems
            })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert("Order placed successfully!");
                getOrder(userId);
                cartItems = []; // Empty the global cart after success
                document.getElementById('cart-display').innerHTML = '';
            } else {
                console.error(result.error);
                alert("There was an issue placing your order: " + result.error);
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            alert("There was a network error.");
        });
    });
});

function getCart(userId) {
    fetch('api/cart')
        .then(response => response.json())
        .then(fetchedItems => {
            cartItems = fetchedItems.filter(cartItem => Number(cartItem.UserID) === Number(userId));

            const cartHTMLPromises = cartItems.map(cartItem => {
                return new Promise(resolve => {
                    getProductPicture(cartItem.ProductID, function(productImage) {
                        const cartItemHTML = `
                            <div class="cart-item">
                                <div class='picture'>${productImage}</div>
                                <div class='detail'>
                                    <p><b>${cartItem.Name}</b></p>
                                    <p>${cartItem.ShopName}</p>
                                    <div class="quantity-control">
                                        <label>Quantity: 
                                            <input type="number" class="quantity-input" value="${cartItem.Quantity}" min="1" onchange="updateCartItem(${cartItem.ProductID}, this.value)">
                                        </label>
                                    </div>
                                    <button class="remove-btn" onclick="removeCartItem(${cartItem.ProductID})">Remove</button>
                                </div>
                            </div>
                        `;
                        resolve(cartItemHTML);
                    });
                });
            });

            Promise.all(cartHTMLPromises).then(allHTML => {
                document.getElementById("cart-display").innerHTML = allHTML.join('');
            });
        })
        .catch(error => {
            console.error("Error fetching cart items:", error);
        });
}

function getOrder(userId) {
    fetch('api/order')
        .then(response => response.json())
        .then(fetchedItems => { 
            const orderItems = fetchedItems.filter(orderItem => 
                Number(orderItem.UserID) === Number(userId))
                .sort((a, b) => b.OrderID - a.OrderID); // newest first

            const orderHTML = orderItems.map(orderItem => {
                const orderElementId = `order-items-${orderItem.OrderID}`;
                // Call getItems immediately for each order
                getItems(orderItem.OrderID, orderElementId);
                let status = "pending";
                if(orderItem.status == 1){
                    status = "complete";
                }
                
                return `
                    <div class="order-item">
                        <p><b>Order ID:</b> ${orderItem.OrderID}</p>
                        <p><b>Status:</b> ${status}</p>
                        <p>Date Ordered: ${orderItem.dateOrdered}</p>
                        <p>Date Completed: ${orderItem.dateComplete || 'N/A'}</p>
                        <p>Address: ${orderItem.address}</p>
                        <div><b>Items:</b></div>
                        <div id="${orderElementId}" class="order-items">
                            <p>Loading items...</p>
                        </div>
                    </div>
                `;
            }).join('');

            document.getElementById("orders-display").innerHTML = orderHTML;
        })
        .catch(error => {
            console.error("Error fetching order items:", error);
            document.getElementById("orders-display").innerHTML = 
                "<p>Error loading orders</p>";
        });
}

function getItems(orderID, targetElementId) {
    fetch('api/orderitem')  // Note: removed the orderID parameter from URL
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(allOrderItems => {
            // Filter items by orderID on the client side
            const orderedItems = allOrderItems.filter(item => 
                Number(item.OrderID) === Number(orderID)
            );

            if (orderedItems.length === 0) {
                document.getElementById(targetElementId).innerHTML = 
                    "<p>No items found for this order</p>";
                return;
            }

            const orderedItemHTML = orderedItems.map(item => {
                return `<p>${item.Name || 'Unknown'} ${item.Quantity || 0}x</p>`;
            }).join('');

            document.getElementById(targetElementId).innerHTML = orderedItemHTML;
        })
        .catch(error => {
            console.error('Error fetching order items:', error);
            document.getElementById(targetElementId).innerHTML = 
                `<p>Error loading items: ${error.message}</p>`;
        });
}