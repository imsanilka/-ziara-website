// Custom JavaScript for ZIARA e-commerce website

// DOM Content Loaded Event
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Add fade-in animation to cards
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        setTimeout(() => {
            card.classList.add('fade-in');
        }, index * 100);
    });

    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Cart functionality
    initializeCart();
    
    // Search functionality
    initializeSearch();
    
    // Product quantity controls
    initializeQuantityControls();
});

// Cart Functions
function initializeCart() {
    // Update cart count in navbar
    updateCartCount();
    
    // Add to cart buttons
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            const productName = this.dataset.productName;
            const productPrice = this.dataset.productPrice;
            const quantity = document.querySelector('#quantity') ? document.querySelector('#quantity').value : 1;
            
            addToCart(productId, productName, productPrice, quantity);
        });
    });
}

function addToCart(productId, productName, productPrice, quantity) {
    // This would typically make an AJAX call to add item to cart
    // For now, we'll show a success message
    showAlert('Product added to cart!', 'success');
    updateCartCount();
}

function updateCartCount() {
    // This would typically fetch cart count from server
    // For demo purposes, we'll use a placeholder
    const cartCountElement = document.querySelector('.cart-count');
    if (cartCountElement) {
        // cartCountElement.textContent = getCartItemCount();
    }
}

// Search Functions
function initializeSearch() {
    const searchInput = document.querySelector('#search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            filterProducts(searchTerm);
        });
    }
}

function filterProducts(searchTerm) {
    const productCards = document.querySelectorAll('.product-card');
    productCards.forEach(card => {
        const productName = card.querySelector('.card-title').textContent.toLowerCase();
        const productDescription = card.querySelector('.card-text').textContent.toLowerCase();
        
        if (productName.includes(searchTerm) || productDescription.includes(searchTerm)) {
            card.style.display = 'block';
            card.classList.add('slide-up');
        } else {
            card.style.display = 'none';
        }
    });
}

// Quantity Controls
function initializeQuantityControls() {
    const quantityInputs = document.querySelectorAll('.quantity-input');
    quantityInputs.forEach(input => {
        const minusBtn = input.parentElement.querySelector('.quantity-minus');
        const plusBtn = input.parentElement.querySelector('.quantity-plus');
        
        if (minusBtn) {
            minusBtn.addEventListener('click', function() {
                let currentValue = parseInt(input.value);
                if (currentValue > 1) {
                    input.value = currentValue - 1;
                    updateItemTotal(input);
                }
            });
        }
        
        if (plusBtn) {
            plusBtn.addEventListener('click', function() {
                let currentValue = parseInt(input.value);
                const maxValue = parseInt(input.getAttribute('max')) || 999;
                if (currentValue < maxValue) {
                    input.value = currentValue + 1;
                    updateItemTotal(input);
                }
            });
        }
        
        input.addEventListener('change', function() {
            updateItemTotal(this);
        });
    });
}

function updateItemTotal(input) {
    const row = input.closest('tr');
    if (row) {
        const price = parseFloat(row.querySelector('.item-price').dataset.price);
        const quantity = parseInt(input.value);
        const total = price * quantity;
        
        row.querySelector('.item-total').textContent = '$' + total.toFixed(2);
        updateCartTotal();
    }
}

function updateCartTotal() {
    const itemTotals = document.querySelectorAll('.item-total');
    let cartTotal = 0;
    
    itemTotals.forEach(total => {
        const amount = parseFloat(total.textContent.replace('$', ''));
        cartTotal += amount;
    });
    
    const cartTotalElement = document.querySelector('.cart-total');
    if (cartTotalElement) {
        cartTotalElement.textContent = '$' + cartTotal.toFixed(2);
    }
}

// Utility Functions
function showAlert(message, type = 'info') {
    const alertContainer = document.querySelector('.alert-container') || document.body;
    const alertElement = document.createElement('div');
    alertElement.className = `alert alert-${type} alert-dismissible fade show`;
    alertElement.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    alertContainer.insertBefore(alertElement, alertContainer.firstChild);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        if (alertElement.parentNode) {
            alertElement.remove();
        }
    }, 5000);
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function validatePhone(phone) {
    const phoneRegex = /^\(\d{3}\) \d{3}-\d{4}$/;
    return phoneRegex.test(phone);
}

// Form Helpers
function resetForm(formId) {
    const form = document.getElementById(formId);
    if (form) {
        form.reset();
        form.classList.remove('was-validated');
    }
}

function serializeForm(form) {
    const formData = new FormData(form);
    const data = {};
    for (let [key, value] of formData.entries()) {
        data[key] = value;
    }
    return data;
}

// Loading States
function showLoading(element) {
    const originalContent = element.innerHTML;
    element.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';
    element.disabled = true;
    element.dataset.originalContent = originalContent;
}

function hideLoading(element) {
    if (element.dataset.originalContent) {
        element.innerHTML = element.dataset.originalContent;
        element.disabled = false;
        delete element.dataset.originalContent;
    }
}

// Image Lazy Loading
function initializeLazyLoading() {
    const images = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
}

// Smooth Scrolling
function smoothScrollTo(targetId) {
    const target = document.getElementById(targetId);
    if (target) {
        target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}

// Local Storage Helpers
function saveToLocalStorage(key, data) {
    try {
        localStorage.setItem(key, JSON.stringify(data));
    } catch (error) {
        console.error('Error saving to localStorage:', error);
    }
}

function getFromLocalStorage(key) {
    try {
        const data = localStorage.getItem(key);
        return data ? JSON.parse(data) : null;
    } catch (error) {
        console.error('Error reading from localStorage:', error);
        return null;
    }
}

// Export functions for use in other scripts
window.ZiaraUtils = {
    showAlert,
    formatCurrency,
    validateEmail,
    validatePhone,
    resetForm,
    serializeForm,
    showLoading,
    hideLoading,
    smoothScrollTo,
    saveToLocalStorage,
    getFromLocalStorage
};

