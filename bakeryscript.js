let cartItems = [];

// Add to Cart Function
function addToCart(item, quantity, price) {
    const totalPrice = quantity * price;
    const existingItemIndex = cartItems.findIndex(cartItem => cartItem.item === item);

    if (existingItemIndex > -1) {
        // If item already exists, update quantity and total price
        cartItems[existingItemIndex].quantity += quantity;
        cartItems[existingItemIndex].totalPrice += totalPrice;
    } else {
        // Add new item to the cart
        cartItems.push({ item, quantity, totalPrice });
    }
    updateCartDisplay();
}

// Update Cart Item Quantity
function updateCartItem(index, quantity) {
    const cartItem = cartItems[index];
    const pricePerItem = cartItem.totalPrice / cartItem.quantity;
    cartItem.quantity = quantity;
    cartItem.totalPrice = quantity * pricePerItem;
    updateCartDisplay();
}

// Remove Item from Cart
function removeCartItem(index) {
    cartItems.splice(index, 1);
    updateCartDisplay();
}

// Update Cart Display in HTML
function updateCartDisplay() {
    const cartList = document.getElementById('cart-items');
    const cartTotal = document.getElementById('cart-total');

    cartList.innerHTML = ''; // Clear cart
    let total = 0;

    cartItems.forEach((item, index) => {
        const listItem = document.createElement('li');

        // Quantity Input
        const quantityInput = document.createElement('input');
        quantityInput.type = 'number';
        quantityInput.min = 1;
        quantityInput.value = item.quantity;
        quantityInput.onchange = () => updateCartItem(index, parseInt(quantityInput.value));

        // Remove Button
        const removeButton = document.createElement('button');
        removeButton.textContent = 'Remove';
        removeButton.onclick = () => removeCartItem(index);

        listItem.textContent = `${item.item} - Quantity: `;
        listItem.appendChild(quantityInput);
        listItem.appendChild(removeButton);
        listItem.appendChild(document.createTextNode(` - Total: LKR ${item.totalPrice}`));

        cartList.appendChild(listItem);
        total += item.totalPrice;
    });

    cartTotal.textContent = `Total: LKR ${total}`;
}

// Save Cart and Navigate to Order Page
function saveCart() {
    if (cartItems.length === 0) {
        alert("Cart is empty!");
        return;
    }

    // Save cart items to localStorage
    localStorage.setItem('cartItems', JSON.stringify(cartItems));

    // Send cart items to server to save in database
    fetch('bakerysave_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(cartItems)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Cart saved successfully!');
            window.location.href = 'bakeryorder.html'; // Navigate to the order page
        } else {
            alert('Failed to save cart items to the database.');
        }
    })
    .catch(error => console.error('Error:', error));
}

// Display Cart Details on Order Page
function displayCartOnOrderPage() {
    const cartList = document.getElementById('order-cart-items');
    const cartTotal = document.getElementById('order-cart-total');
    const savedCartItems = JSON.parse(localStorage.getItem('cartItems')) || [];

    let total = 0;
    savedCartItems.forEach(item => {
        const listItem = document.createElement('li');
        listItem.textContent = `${item.item} - Quantity: ${item.quantity} - Total: LKR ${item.totalPrice}`;
        cartList.appendChild(listItem);
        total += item.totalPrice;
    });

    cartTotal.textContent = `Total: LKR ${total}`;
}

// Process Order
function processOrder() {
    const name = document.getElementById('name').value;
    const address = document.getElementById('address').value;
    const phone = document.getElementById('phone').value;

    if (!name || !address || !phone) {
        alert("Please fill in all fields.");
        return;
    }

    const cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];

    fetch('bakeryorder.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name, address, phone, cartItems })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Order placed successfully!');
            localStorage.removeItem('cartItems'); // Clear cart
            window.location.href = 'bakeryform.html'; // Navigate back to homepage
        } else {
            alert('Failed to place order.');
        }
    })
    .catch(error => console.error('Error:', error));
}
