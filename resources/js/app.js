// resources/js/app.js

import './bootstrap';

// Global functions and utilities
window.confirmDelete = function(message) {
    return confirm(message || 'Are you sure you want to delete this item?');
};

window.formatCurrency = function(amount) {
    return '₱' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
};

// Auto-calculate totals in forms
document.addEventListener('DOMContentLoaded', function() {
    // Sales form auto-calculation
    const quantityInput = document.querySelector('input[name="quantity"]');
    const priceInput = document.querySelector('input[name="price_per_kg"]');
    const amountInput = document.querySelector('input[name="amount"]');
    
    if (quantityInput && priceInput && amountInput) {
        const calculateTotal = () => {
            const quantity = parseFloat(quantityInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;
            amountInput.value = (quantity * price).toFixed(2);
        };
        
        quantityInput.addEventListener('input', calculateTotal);
        priceInput.addEventListener('input', calculateTotal);
    }
    
    // Confirm before form submission for delete actions
    const deleteForms = document.querySelectorAll('form[data-confirm]');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm(this.dataset.confirm)) {
                e.preventDefault();
            }
        });
    });
});

let touchStartX = 0;
let touchEndX = 0;

document.addEventListener('touchstart', e => {
    touchStartX = e.changedTouches[0].screenX;
});

document.addEventListener('touchend', e => {
    touchEndX = e.changedTouches[0].screenX;
    handleSwipe();
});

function handleSwipe() {
    if (window.innerWidth <= 768) {
        const sidebar = document.getElementById('sidebar');
        if (touchEndX < touchStartX - 50) {
            sidebar.classList.remove('mobile-open'); // Swipe left
        }
        if (touchEndX > touchStartX + 50 && touchStartX < 50) {
            sidebar.classList.add('mobile-open'); // Swipe right from edge
        }
    }
}