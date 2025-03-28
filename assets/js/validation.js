/**
 * Validation.js
 * Contains form validation functionality for the Car Rental System
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize validations
    initLoginValidation();
    initRegisterValidation();
    initBookingValidation();
    initContactFormValidation();
    initProfileValidation();
    initCarFormValidation();
});

/**
 * Utility function to display validation error
 * @param {HTMLElement} input - Input element
 * @param {string} message - Error message
 */
function showValidationError(input, message) {
    // Find parent form group
    const formGroup = input.closest('.form-group') || input.closest('.form-floating');
    
    // Remove any existing error
    removeValidationError(input);
    
    // Add error class to input
    input.classList.add('is-invalid');
    
    // Create error message element
    const errorElement = document.createElement('div');
    errorElement.classList.add('invalid-feedback');
    errorElement.textContent = message;
    
    // Add after input
    input.after(errorElement);
}

/**
 * Utility function to remove validation error
 * @param {HTMLElement} input - Input element
 */
function removeValidationError(input) {
    // Remove error class
    input.classList.remove('is-invalid');
    
    // Find and remove any existing error message
    const formGroup = input.closest('.form-group') || input.closest('.form-floating');
    const errorElement = formGroup.querySelector('.invalid-feedback');
    
    if (errorElement) {
        errorElement.remove();
    }
}

/**
 * Utility function to add success state
 * @param {HTMLElement} input - Input element
 */
function addValidationSuccess(input) {
    input.classList.add('is-valid');
}

/**
 * Validate email format
 * @param {string} email - Email to validate
 * @returns {boolean} - Whether email is valid
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Validate phone number format
 * @param {string} phone - Phone number to validate
 * @returns {boolean} - Whether phone is valid
 */
function isValidPhone(phone) {
    const phoneRegex = /^[0-9]{10,11}$/;
    return phoneRegex.test(phone);
}

/**
 * Validate password strength
 * @param {string} password - Password to validate
 * @returns {boolean} - Whether password is strong enough
 */
function isStrongPassword(password) {
    // At least 6 characters, containing at least one number
    const passwordRegex = /^(?=.*[0-9]).{6,}$/;
    return passwordRegex.test(password);
}

/**
 * Validate date format and check if it's a future date
 * @param {string} dateString - Date string to validate
 * @returns {boolean} - Whether date is valid and in the future
 */
function isValidFutureDate(dateString) {
    const selectedDate = new Date(dateString);
    const today = new Date();
    today.setHours(0, 0, 0, 0); // Reset time to beginning of day
    
    // Check if date is valid and is in the future
    return !isNaN(selectedDate) && selectedDate >= today;
}

/**
 * Check if return date is after pickup date
 * @param {string} pickupDateString - Pickup date string
 * @param {string} returnDateString - Return date string
 * @returns {boolean} - Whether return date is after pickup date
 */
function isValidDateRange(pickupDateString, returnDateString) {
    const pickupDate = new Date(pickupDateString);
    const returnDate = new Date(returnDateString);
    
    // Check if dates are valid and return date is after pickup date
    return !isNaN(pickupDate) && !isNaN(returnDate) && returnDate > pickupDate;
}

/**
 * Login Form Validation
 */
function initLoginValidation() {
    const loginForm = document.getElementById('login-form');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(event) {
            let isValid = true;
            
            // Get form inputs
            const username = document.getElementById('username');
            const password = document.getElementById('password');
            
            // Validate username
            if (username.value.trim() === '') {
                showValidationError(username, 'Vui lòng nhập tên đăng nhập');
                isValid = false;
            } else {
                removeValidationError(username);
                addValidationSuccess(username);
            }
            
            // Validate password
            if (password.value.trim() === '') {
                showValidationError(password, 'Vui lòng nhập mật khẩu');
                isValid = false;
            } else {
                removeValidationError(password);
                addValidationSuccess(password);
            }
            
            // Prevent form submission if validation fails
            if (!isValid) {
                event.preventDefault();
            }
        });
    }
}

/**
 * Register Form Validation
 */
function initRegisterValidation() {
    const registerForm = document.getElementById('register-form');
    
    if (registerForm) {
        registerForm.addEventListener('submit', function(event) {
            let isValid = true;
            
            // Get form inputs
            const username = document.getElementById('username');
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');
            const fullName = document.getElementById('full_name');
            const phone = document.getElementById('phone');
            const agreeTerms = document.getElementById('agree-terms');
            
            // Validate username
            if (username.value.trim() === '') {
                showValidationError(username, 'Vui lòng nhập tên đăng nhập');
                isValid = false;
            } else if (username.value.trim().length < 3) {
                showValidationError(username, 'Tên đăng nhập phải có ít nhất 3 ký tự');
                isValid = false;
            } else {
                removeValidationError(username);
                addValidationSuccess(username);
            }
            
            // Validate email
            if (email.value.trim() === '') {
                showValidationError(email, 'Vui lòng nhập email');
                isValid = false;
            } else if (!isValidEmail(email.value.trim())) {
                showValidationError(email, 'Email không hợp lệ');
                isValid = false;
            } else {
                removeValidationError(email);
                addValidationSuccess(email);
            }
            
            // Validate password
            if (password.value === '') {
                showValidationError(password, 'Vui lòng nhập mật khẩu');
                isValid = false;
            } else if (!isStrongPassword(password.value)) {
                showValidationError(password, 'Mật khẩu phải có ít nhất 6 ký tự và chứa ít nhất 1 số');
                isValid = false;
            } else {
                removeValidationError(password);
                addValidationSuccess(password);
            }
            
            // Validate confirm password
            if (confirmPassword.value === '') {
                showValidationError(confirmPassword, 'Vui lòng xác nhận mật khẩu');
                isValid = false;
            } else if (password.value !== confirmPassword.value) {
                showValidationError(confirmPassword, 'Mật khẩu xác nhận không khớp');
                isValid = false;
            } else {
                removeValidationError(confirmPassword);
                addValidationSuccess(confirmPassword);
            }
            
            // Validate full name
            if (fullName.value.trim() === '') {
                showValidationError(fullName, 'Vui lòng nhập họ tên');
                isValid = false;
            } else {
                removeValidationError(fullName);
                addValidationSuccess(fullName);
            }
            
            // Validate phone
            if (phone.value.trim() === '') {
                showValidationError(phone, 'Vui lòng nhập số điện thoại');
                isValid = false;
            } else if (!isValidPhone(phone.value.trim())) {
                showValidationError(phone, 'Số điện thoại không hợp lệ (cần 10-11 số)');
                isValid = false;
            } else {
                removeValidationError(phone);
                addValidationSuccess(phone);
            }
            
            // Validate terms agreement
            if (agreeTerms && !agreeTerms.checked) {
                showValidationError(agreeTerms, 'Bạn phải đồng ý với điều khoản dịch vụ');
                isValid = false;
            } else if (agreeTerms) {
                removeValidationError(agreeTerms);
            }
            
            // Prevent form submission if validation fails
            if (!isValid) {
                event.preventDefault();
            }
        });
    }
}

/**
 * Booking Form Validation
 */
function initBookingValidation() {
    const bookingForm = document.getElementById('booking-form');
    
    if (bookingForm) {
        // Calculate total price on date change
        const pickupDate = document.getElementById('pickup_date');
        const returnDate = document.getElementById('return_date');
        const pricePerDay = document.getElementById('price_per_day');
        const totalPrice = document.getElementById('total_price');
        const totalPriceDisplay = document.getElementById('total_price_display');
        
        function calculateTotalPrice() {
            if (pickupDate.value && returnDate.value && pricePerDay) {
                if (isValidDateRange(pickupDate.value, returnDate.value)) {
                    const start = new Date(pickupDate.value);
                    const end = new Date(returnDate.value);
                    const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
                    
                    const price = parseFloat(pricePerDay.value);
                    const total = days * price;
                    
                    if (totalPrice) totalPrice.value = total;
                    if (totalPriceDisplay) totalPriceDisplay.textContent = total.toLocaleString('vi-VN') + ' VND';
                }
            }
        }
        
        if (pickupDate) {
            pickupDate.addEventListener('change', calculateTotalPrice);
        }
        
        if (returnDate) {
            returnDate.addEventListener('change', calculateTotalPrice);
        }
        
        // Form submission validation
        bookingForm.addEventListener('submit', function(event) {
            let isValid = true;
            
            // Get form inputs
            const pickupDate = document.getElementById('pickup_date');
            const returnDate = document.getElementById('return_date');
            const pickupLocation = document.getElementById('pickup_location');
            const returnLocation = document.getElementById('return_location');
            
            // Validate pickup date
            if (!pickupDate.value) {
                showValidationError(pickupDate, 'Vui lòng chọn ngày nhận xe');
                isValid = false;
            } else if (!isValidFutureDate(pickupDate.value)) {
                showValidationError(pickupDate, 'Ngày nhận xe phải là ngày trong tương lai');
                isValid = false;
            } else {
                removeValidationError(pickupDate);
                addValidationSuccess(pickupDate);
            }
            
            // Validate return date
            if (!returnDate.value) {
                showValidationError(returnDate, 'Vui lòng chọn ngày trả xe');
                isValid = false;
            } else if (!isValidDateRange(pickupDate.value, returnDate.value)) {
                showValidationError(returnDate, 'Ngày trả xe phải sau ngày nhận xe');
                isValid = false;
            } else {
                removeValidationError(returnDate);
                addValidationSuccess(returnDate);
            }
            
            // Validate pickup location
            if (pickupLocation.value.trim() === '') {
                showValidationError(pickupLocation, 'Vui lòng nhập địa điểm nhận xe');
                isValid = false;
            } else {
                removeValidationError(pickupLocation);
                addValidationSuccess(pickupLocation);
            }
            
            // Validate return location
            if (returnLocation.value.trim() === '') {
                showValidationError(returnLocation, 'Vui lòng nhập địa điểm trả xe');
                isValid = false;
            } else {
                removeValidationError(returnLocation);
                addValidationSuccess(returnLocation);
            }
            
            // Prevent form submission if validation fails
            if (!isValid) {
                event.preventDefault();
            }
        });
    }
}

/**
 * Contact Form Validation
 */
function initContactFormValidation() {
    const contactForm = document.getElementById('contact-form');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(event) {
            let isValid = true;
            
            // Get form inputs
            const name = document.getElementById('name');
            const email = document.getElementById('email');
            const subject = document.getElementById('subject');
            const message = document.getElementById('message');
            
            // Validate name
            if (name.value.trim() === '') {
                showValidationError(name, 'Vui lòng nhập họ tên');
                isValid = false;
            } else {
                removeValidationError(name);
                addValidationSuccess(name);
            }
            
            // Validate email
            if (email.value.trim() === '') {
                showValidationError(email, 'Vui lòng nhập email');
                isValid = false;
            } else if (!isValidEmail(email.value.trim())) {
                showValidationError(email, 'Email không hợp lệ');
                isValid = false;
            } else {
                removeValidationError(email);
                addValidationSuccess(email);
            }
            
            // Validate subject
            if (subject.value.trim() === '') {
                showValidationError(subject, 'Vui lòng nhập tiêu đề');
                isValid = false;
            } else {
                removeValidationError(subject);
                addValidationSuccess(subject);
            }
            
            // Validate message
            if (message.value.trim() === '') {
                showValidationError(message, 'Vui lòng nhập nội dung');
                isValid = false;
            } else if (message.value.trim().length < 10) {
                showValidationError(message, 'Nội dung phải có ít nhất 10 ký tự');
                isValid = false;
            } else {
                removeValidationError(message);
                addValidationSuccess(message);
            }
            
            // Prevent form submission if validation fails
            if (!isValid) {
                event.preventDefault();
            }
        });
    }
}

/**
 * Profile Form Validation
 */
function initProfileValidation() {
    const profileForm = document.getElementById('profile-form');
    const passwordForm = document.getElementById('password-form');
    
    if (profileForm) {
        profileForm.addEventListener('submit', function(event) {
            let isValid = true;
            
            // Get form inputs
            const email = document.getElementById('email');
            const fullName = document.getElementById('full_name');
            const phone = document.getElementById('phone');
            
            // Validate email
            if (email.value.trim() === '') {
                showValidationError(email, 'Vui lòng nhập email');
                isValid = false;
            } else if (!isValidEmail(email.value.trim())) {
                showValidationError(email, 'Email không hợp lệ');
                isValid = false;
            } else {
                removeValidationError(email);
                addValidationSuccess(email);
            }
            
            // Validate full name
            if (fullName.value.trim() === '') {
                showValidationError(fullName, 'Vui lòng nhập họ tên');
                isValid = false;
            } else {
                removeValidationError(fullName);
                addValidationSuccess(fullName);
            }
            
            // Validate phone
            if (phone.value.trim() === '') {
                showValidationError(phone, 'Vui lòng nhập số điện thoại');
                isValid = false;
            } else if (!isValidPhone(phone.value.trim())) {
                showValidationError(phone, 'Số điện thoại không hợp lệ (cần 10-11 số)');
                isValid = false;
            } else {
                removeValidationError(phone);
                addValidationSuccess(phone);
            }
            
            // Prevent form submission if validation fails
            if (!isValid) {
                event.preventDefault();
            }
        });
    }
    
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(event) {
            let isValid = true;
            
            // Get form inputs
            const currentPassword = document.getElementById('current_password');
            const newPassword = document.getElementById('new_password');
            const confirmPassword = document.getElementById('confirm_password');
            
            // Validate current password
            if (currentPassword.value === '') {
                showValidationError(currentPassword, 'Vui lòng nhập mật khẩu hiện tại');
                isValid = false;
            } else {
                removeValidationError(currentPassword);
                addValidationSuccess(currentPassword);
            }
            
            // Validate new password
            if (newPassword.value === '') {
                showValidationError(newPassword, 'Vui lòng nhập mật khẩu mới');
                isValid = false;
            } else if (!isStrongPassword(newPassword.value)) {
                showValidationError(newPassword, 'Mật khẩu phải có ít nhất 6 ký tự và chứa ít nhất 1 số');
                isValid = false;
            } else {
                removeValidationError(newPassword);
                addValidationSuccess(newPassword);
            }
            
            // Validate confirm password
            if (confirmPassword.value === '') {
                showValidationError(confirmPassword, 'Vui lòng xác nhận mật khẩu mới');
                isValid = false;
            } else if (newPassword.value !== confirmPassword.value) {
                showValidationError(confirmPassword, 'Mật khẩu xác nhận không khớp');
                isValid = false;
            } else {
                removeValidationError(confirmPassword);
                addValidationSuccess(confirmPassword);
            }
            
            // Prevent form submission if validation fails
            if (!isValid) {
                event.preventDefault();
            }
        });
    }
}

/**
 * Car Form Validation (for admin)
 */
function initCarFormValidation() {
    const carForm = document.getElementById('car-form');
    
    if (carForm) {
        carForm.addEventListener('submit', function(event) {
            let isValid = true;
            
            // Get form inputs
            const brand = document.getElementById('brand');
            const model = document.getElementById('model');
            const year = document.getElementById('year');
            const licensePlate = document.getElementById('license_plate');
            const price = document.getElementById('price_per_day');
            
            // Validate brand
            if (brand.value.trim() === '') {
                showValidationError(brand, 'Vui lòng nhập hãng xe');
                isValid = false;
            } else {
                removeValidationError(brand);
                addValidationSuccess(brand);
            }
            
            // Validate model
            if (model.value.trim() === '') {
                showValidationError(model, 'Vui lòng nhập model xe');
                isValid = false;
            } else {
                removeValidationError(model);
                addValidationSuccess(model);
            }
            
            // Validate year
            if (year.value.trim() === '') {
                showValidationError(year, 'Vui lòng nhập năm sản xuất');
                isValid = false;
            } else if (isNaN(year.value) || parseInt(year.value) < 1900 || parseInt(year.value) > new Date().getFullYear()) {
                showValidationError(year, 'Năm sản xuất không hợp lệ');
                isValid = false;
            } else {
                removeValidationError(year);
                addValidationSuccess(year);
            }
            
            // Validate license plate
            if (licensePlate.value.trim() === '') {
                showValidationError(licensePlate, 'Vui lòng nhập biển số xe');
                isValid = false;
            } else {
                removeValidationError(licensePlate);
                addValidationSuccess(licensePlate);
            }
            
            // Validate price
            if (price.value.trim() === '') {
                showValidationError(price, 'Vui lòng nhập giá thuê xe');
                isValid = false;
            } else if (isNaN(price.value) || parseFloat(price.value) <= 0) {
                showValidationError(price, 'Giá thuê xe phải là số dương');
                isValid = false;
            } else {
                removeValidationError(price);
                addValidationSuccess(price);
            }
            
            // Prevent form submission if validation fails
            if (!isValid) {
                event.preventDefault();
            }
        });
    }
}