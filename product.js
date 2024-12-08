// Product Page

let currentColorIndex = 0;
const colors = [
    "/Assets/fabrics/gingham-blue.jpg", // Blue
    "/Assets/fabrics/gingham-pink.jpg", // Pink
    "/Assets/fabrics/gingham-purple.jpg", // Purple
    "/Assets/fabrics/gingham-orange.jpg", // Orange
    "/Assets/fabrics/gingham-yellow.jpg" // Yellow
];

// Function to change the product image based on the selected color
function changeColor(index) {
    currentColorIndex = index;
    const productImage = document.querySelector(".product-image img");
    productImage.src = colors[index];
}

// Functions for previous and next buttons
function prevColor() {
    currentColorIndex = (currentColorIndex - 1 + colors.length) % colors.length;
    changeColor(currentColorIndex);
}

function nextColor() {
    currentColorIndex = (currentColorIndex + 1) % colors.length;
    changeColor(currentColorIndex);
}

// Initial display of the first color
window.onload = function() {
    changeColor(currentColorIndex);
}

// Add event listener for keydown events
document.addEventListener("keydown", function(event) {
    if (event.key === "ArrowLeft") {
        prevColor(); // Go to previous color
    } else if (event.key === "ArrowRight") {
        nextColor(); // Go to next color
    }
});



// Cart Page

// Update total price based on quantity and unit price
function updateTotalPrice(row) {
    const quantityInput = row.querySelector('.quantity-select');
    const unitPrice = parseFloat(row.querySelector('.unit-price').textContent.replace('₱', '').replace(',', ''));
    const totalPriceCell = row.querySelector('.item-total-price');

    const quantity = parseInt(quantityInput.value);
    const totalPrice = (unitPrice * quantity).toFixed(2);
    totalPriceCell.textContent = `₱${totalPrice}`;
}

// Update subtotal and total
function updateCartTotals() {
    const totalPrices = document.querySelectorAll('.item-total-price');
    let subtotal = 0;

    totalPrices.forEach(price => {
        const priceText = price.textContent.replace('₱', '').replace(',', '');
        subtotal += parseFloat(priceText);
    });

    const shipping = 40.00; // Fixed shipping cost
    const tax = 4.00; // Fixed tax cost
    const total = subtotal + shipping + tax;

    document.querySelector('.subtotal').textContent = `₱${subtotal.toFixed(2)}`;
    document.querySelector('.total-price').textContent = `₱${total.toFixed(2)}`;
}

// Select all checkboxes
document.getElementById('select-all').addEventListener('change', function () {
    const checkboxes = document.querySelectorAll('.item-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Quantity change event
document.querySelectorAll('.quantity-select').forEach(input => {
    input.addEventListener('change', function () {
        const row = this.closest('tr');
        updateTotalPrice(row);
        updateCartTotals();
    });
});

// Remove selected items
document.getElementById('remove-selected').addEventListener('click', function () {
    const checkboxes = document.querySelectorAll('.item-checkbox:checked');
    checkboxes.forEach(checkbox => {
        checkbox.closest('tr').remove();
    });
    updateCartTotals(); // Update totals after removal
});

// Remove individual item
document.querySelectorAll('.remove-item').forEach(button => {
    button.addEventListener('click', function () {
        const row = this.closest('tr');
        row.remove();
        updateCartTotals(); // Update totals after removal
    });
});

// Initial calculation of totals
document.querySelectorAll('.quantity-select').forEach(input => {
    const row = input.closest('tr');
    updateTotalPrice(row);
});
updateCartTotals();