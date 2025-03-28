/**
 * Main.js
 * Contains main functionality for the Car Rental System
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize components
    initNavbar();
    initSearchForm();
    initDatepickers();
    initCarFilters();
    initImageGallery();
    initBookingCalculator();
    initAdminSidebar();
    initTooltips();
    initNotifications();
});

/**
 * Initialize Navbar
 */
function initNavbar() {
    // Mobile menu toggle
    const menuToggle = document.querySelector('.navbar-toggler');
    const mobileMenu = document.querySelector('.navbar-collapse');
    
    if (menuToggle && mobileMenu) {
        menuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('show');
        });
    }
    
    // Add active class to current page link
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.nav-link');
    
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        
        if (href === currentPath || (href !== '/' && currentPath.startsWith(href))) {
            link.classList.add('active');
        }
    });
    
    // Navbar scroll behavior
    const navbar = document.querySelector('.main-header');
    
    if (navbar) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        });
    }
}

/**
 * Initialize Search Form
 */
function initSearchForm() {
    const searchForm = document.getElementById('search-form');
    
    if (searchForm) {
        // Add event listener for form submission
        searchForm.addEventListener('submit', function(event) {
            event.preventDefault();
            
            // Get form values
            const pickupDate = document.getElementById('search_pickup_date').value;
            const returnDate = document.getElementById('search_return_date').value;
            const carType = document.getElementById('search_car_type').value;
            
            // Redirect to cars page with search parameters
            window.location.href = `/cars.php?pickup_date=${pickupDate}&return_date=${returnDate}&car_type=${carType}`;
        });
    }
}

/**
 * Initialize Datepickers
 */
function initDatepickers() {
    // Initialize all date inputs
    const dateInputs = document.querySelectorAll('input[type="date"]');
    
    dateInputs.forEach(input => {
        // Set min date to today for pickup dates
        if (input.id.includes('pickup') || input.id.includes('start')) {
            const today = new Date().toISOString().split('T')[0];
            input.setAttribute('min', today);
        }
        
        // Update min date for return date based on pickup date
        if (input.id.includes('pickup') || input.id.includes('start')) {
            input.addEventListener('change', function() {
                const returnInput = document.getElementById(input.id.replace('pickup', 'return').replace('start', 'end'));
                
                if (returnInput) {
                    // Set min date for return to be pickup date
                    returnInput.setAttribute('min', input.value);
                    
                    // If current return date is before new pickup date, update it
                    if (returnInput.value && returnInput.value < input.value) {
                        returnInput.value = input.value;
                    }
                }
            });
        }
    });
}

/**
 * Initialize Car Filters
 */
function initCarFilters() {
    const filterForm = document.getElementById('filter-form');
    
    if (filterForm) {
        // Get filter elements
        const priceRange = document.getElementById('price-range');
        const priceValue = document.getElementById('price-value');
        const brandFilters = document.querySelectorAll('input[name="brand[]"]');
        const typeFilters = document.querySelectorAll('input[name="type[]"]');
        const sortSelect = document.getElementById('sort-by');
        
        // Update price range display
        if (priceRange && priceValue) {
            priceRange.addEventListener('input', function() {
                priceValue.textContent = priceRange.value.toLocaleString('vi-VN') + ' VND';
            });
        }
        
        // Apply filters when changed
        const filterInputs = filterForm.querySelectorAll('input, select');
        
        filterInputs.forEach(input => {
            input.addEventListener('change', function() {
                applyFilters();
            });
        });
        
        // Apply filters function
        function applyFilters() {
            const cars = document.querySelectorAll('.car-card');
            
            cars.forEach(car => {
                let showCar = true;
                
                // Filter by price
                if (priceRange) {
                    const carPrice = parseFloat(car.dataset.price || 0);
                    if (carPrice > parseFloat(priceRange.value)) {
                        showCar = false;
                    }
                }
                
                // Filter by brand
                if (brandFilters.length > 0) {
                    const selectedBrands = Array.from(brandFilters)
                        .filter(input => input.checked)
                        .map(input => input.value);
                    
                    if (selectedBrands.length > 0) {
                        const carBrand = car.dataset.brand;
                        if (!selectedBrands.includes(carBrand)) {
                            showCar = false;
                        }
                    }
                }
                
                // Filter by type
                if (typeFilters.length > 0) {
                    const selectedTypes = Array.from(typeFilters)
                        .filter(input => input.checked)
                        .map(input => input.value);
                    
                    if (selectedTypes.length > 0) {
                        const carType = car.dataset.type;
                        if (!selectedTypes.includes(carType)) {
                            showCar = false;
                        }
                    }
                }
                
                // Apply visibility
                if (showCar) {
                    car.style.display = '';
                } else {
                    car.style.display = 'none';
                }
            });
            
            // Sort cars
            if (sortSelect) {
                const sortBy = sortSelect.value;
                const carContainer = document.querySelector('.car-list');
                
                if (carContainer) {
                    const carList = Array.from(cars);
                    
                    carList.sort((a, b) => {
                        if (sortBy === 'price-asc') {
                            return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                        } else if (sortBy === 'price-desc') {
                            return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                        } else if (sortBy === 'name-asc') {
                            return a.dataset.name.localeCompare(b.dataset.name);
                        } else if (sortBy === 'name-desc') {
                            return b.dataset.name.localeCompare(a.dataset.name);
                        }
                        return 0;
                    });
                    
                    // Reappend sorted cars
                    carList.forEach(car => {
                        carContainer.appendChild(car);
                    });
                }
            }
        }
    }
}

/**
 * Initialize Image Gallery for car details
 */
function initImageGallery() {
    const mainImage = document.querySelector('.car-detail-image img');
    const thumbnails = document.querySelectorAll('.car-thumbnail');
    
    if (mainImage && thumbnails.length > 0) {
        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                // Update main image src
                mainImage.src = this.dataset.src;
                
                // Remove active class from all thumbnails
                thumbnails.forEach(t => t.classList.remove('active'));
                
                // Add active class to clicked thumbnail
                this.classList.add('active');
            });
        });
    }
}

/**
 * Initialize Booking Calculator
 */
function initBookingCalculator() {
    const pickupDate = document.getElementById('pickup_date');
    const returnDate = document.getElementById('return_date');
    const pricePerDay = document.getElementById('price_per_day');
    const totalPriceDisplay = document.getElementById('total_price_display');
    const totalPriceInput = document.getElementById('total_price');
    
    if (pickupDate && returnDate && pricePerDay && totalPriceDisplay) {
        function calculateTotal() {
            if (pickupDate.value && returnDate.value) {
                const start = new Date(pickupDate.value);
                const end = new Date(returnDate.value);
                
                // Calculate number of days (including partial days)
                const diffTime = end - start;
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                
                if (diffDays > 0) {
                    // Calculate total price
                    const price = parseFloat(pricePerDay.value);
                    const total = diffDays * price;
                    
                    // Update display and hidden input
                    totalPriceDisplay.textContent = total.toLocaleString('vi-VN') + ' VND';
                    if (totalPriceInput) totalPriceInput.value = total;
                    
                    // Show rental details
                    const daysDisplay = document.getElementById('rental_days');
                    if (daysDisplay) daysDisplay.textContent = diffDays;
                }
            }
        }
        
        // Calculate on date change
        pickupDate.addEventListener('change', calculateTotal);
        returnDate.addEventListener('change', calculateTotal);
        
        // Initial calculation
        calculateTotal();
    }
}

/**
 * Initialize Admin Sidebar
 */
function initAdminSidebar() {
    const sidebarToggle = document.querySelector('.admin-sidebar-toggle');
    const sidebar = document.querySelector('.admin-sidebar');
    const content = document.querySelector('.admin-content');
    
    if (sidebarToggle && sidebar && content) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('admin-sidebar-collapsed');
            content.classList.toggle('admin-content-expanded');
        });
    }
    
    // Dropdown toggles
    const dropdownToggles = document.querySelectorAll('.admin-dropdown-toggle');
    
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            this.classList.toggle('collapsed');
            
            // Get target dropdown menu
            const target = document.querySelector(this.getAttribute('data-bs-target'));
            
            if (target) {
                target.classList.toggle('show');
            }
        });
    });
}

/**
 * Initialize Bootstrap Tooltips
 */
function initTooltips() {
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    
    if (tooltips.length > 0 && typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        tooltips.forEach(tooltip => {
            new bootstrap.Tooltip(tooltip);
        });
    }
}

/**
 * Initialize Notifications
 */
function initNotifications() {
    // Auto dismiss alerts
    const alerts = document.querySelectorAll('.alert[data-auto-dismiss]');
    
    alerts.forEach(alert => {
        const dismissTime = parseInt(alert.dataset.autoDismiss) || 5000;
        
        setTimeout(() => {
            // Fade out
            alert.style.opacity = '0';
            
            // Remove after transition
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, dismissTime);
    });
    
    // Close button functionality
    const closeButtons = document.querySelectorAll('.alert .close');
    
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const alert = this.closest('.alert');
            
            alert.style.opacity = '0';
            
            setTimeout(() => {
                alert.remove();
            }, 300);
        });
    });
}

/**
 * Display a notification
 * @param {string} message - Notification message
 * @param {string} type - Notification type (success, danger, warning, info)
 * @param {boolean} autoDismiss - Whether to auto dismiss
 */
function showNotification(message, type = 'info', autoDismiss = true) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show`;
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    // Add auto dismiss
    if (autoDismiss) {
        notification.dataset.autoDismiss = '5000';
    }
    
    // Add to notification container
    const container = document.querySelector('.notification-container');
    
    if (container) {
        container.appendChild(notification);
    } else {
        // Create container if it doesn't exist
        const newContainer = document.createElement('div');
        newContainer.className = 'notification-container';
        newContainer.style.position = 'fixed';
        newContainer.style.top = '20px';
        newContainer.style.right = '20px';
        newContainer.style.zIndex = '1050';
        
        newContainer.appendChild(notification);
        document.body.appendChild(newContainer);
    }
    
    // Initialize notification
    initNotifications();
}

/**
 * Confirm action with dialog
 * @param {string} message - Confirmation message
 * @param {Function} callback - Callback function if confirmed
 */
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}