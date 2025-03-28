/**
 * Animations.js
 * Contains all animation functionality for the Car Rental System
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize animations
    initScrollAnimations();
    initButtonAnimations();
    initHoverEffects();
    initCarAnimations();
    initNotificationAnimations();
});

/**
 * Scroll Animations
 * Adds animations to elements when they enter the viewport
 */
function initScrollAnimations() {
    const animatedElements = document.querySelectorAll('.animate-on-scroll');
    
    // Observer options
    const options = {
        root: null, // viewport
        rootMargin: '0px',
        threshold: 0.2 // 20% of the element must be visible
    };
    
    // Callback function for the observer
    const callback = (entries, observer) => {
        entries.forEach(entry => {
            // Add animation class when element enters viewport
            if (entry.isIntersecting) {
                let element = entry.target;
                
                // Get animation type from data attribute
                let animationType = element.dataset.animation || 'fadeIn';
                element.classList.add(`animate__${animationType}`);
                
                // Optional: Stop observing element after animating
                observer.unobserve(element);
            }
        });
    };
    
    // Create observer
    const observer = new IntersectionObserver(callback, options);
    
    // Observe all elements with animate-on-scroll class
    animatedElements.forEach(element => {
        observer.observe(element);
    });
}

/**
 * Button Animations
 * Adds animation effects to buttons
 */
function initButtonAnimations() {
    const buttons = document.querySelectorAll('.btn-animate');
    
    buttons.forEach(button => {
        // Add ripple effect on click
        button.addEventListener('click', function(e) {
            let x = e.clientX - e.target.getBoundingClientRect().left;
            let y = e.clientY - e.target.getBoundingClientRect().top;
            
            let ripple = document.createElement('span');
            ripple.classList.add('ripple-effect');
            ripple.style.left = `${x}px`;
            ripple.style.top = `${y}px`;
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600); // Remove after animation completes
        });
    });
}

/**
 * Hover Effects
 * Adds hover animations to elements
 */
function initHoverEffects() {
    // Scale effect on car cards
    const carCards = document.querySelectorAll('.car-card');
    
    carCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px)';
            this.style.boxShadow = '0 15px 30px rgba(0,0,0,0.1)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 5px 15px rgba(0,0,0,0.05)';
        });
    });
    
    // Glow effect on feature icons
    const featureIcons = document.querySelectorAll('.feature-icon');
    
    featureIcons.forEach(icon => {
        icon.addEventListener('mouseenter', function() {
            this.classList.add('pulse-animation');
        });
        
        icon.addEventListener('mouseleave', function() {
            this.classList.remove('pulse-animation');
        });
    });
}

/**
 * Car Animations
 * Special animations for car-related elements
 */
function initCarAnimations() {
    // Animate car image in hero section
    const heroCarImage = document.querySelector('.hero-car-image');
    
    if (heroCarImage) {
        // Floating animation
        setInterval(() => {
            heroCarImage.style.transform = 'translateY(-10px)';
            
            setTimeout(() => {
                heroCarImage.style.transform = 'translateY(0)';
            }, 1000);
        }, 2000);
    }
    
    // Car driving animation
    const carAnimation = document.querySelector('.car-animation');
    
    if (carAnimation) {
        // Start the car driving animation
        carAnimation.style.animation = 'driveCar 10s linear infinite';
    }
    
    // Car selection effect
    const carSelection = document.querySelectorAll('.car-select-option');
    
    carSelection.forEach(option => {
        option.addEventListener('click', function() {
            // Remove active class from all options
            carSelection.forEach(opt => opt.classList.remove('active'));
            
            // Add active class to selected option with animation
            this.classList.add('active');
            this.classList.add('animate__pulse');
            
            // Remove animation class after animation completes
            setTimeout(() => {
                this.classList.remove('animate__pulse');
            }, 800);
        });
    });
}

/**
 * Notification Animations
 * Animations for alerts and notifications
 */
function initNotificationAnimations() {
    // Animate alerts
    const alerts = document.querySelectorAll('.alert');
    
    alerts.forEach(alert => {
        // Add fade in animation
        alert.classList.add('animate__fadeIn');
        
        // Auto dismiss alerts with data-auto-dismiss attribute
        if (alert.dataset.autoDismiss) {
            const dismissTime = parseInt(alert.dataset.autoDismiss) || 5000;
            
            setTimeout(() => {
                // Add fade out animation
                alert.classList.remove('animate__fadeIn');
                alert.classList.add('animate__fadeOut');
                
                // Remove alert after animation completes
                setTimeout(() => {
                    alert.remove();
                }, 500);
            }, dismissTime);
        }
        
        // Close button functionality
        const closeBtn = alert.querySelector('.close');
        
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                alert.classList.remove('animate__fadeIn');
                alert.classList.add('animate__fadeOut');
                
                setTimeout(() => {
                    alert.remove();
                }, 500);
            });
        }
    });
    
    // Toast notifications
    const toasts = document.querySelectorAll('.toast');
    
    toasts.forEach(toast => {
        // Add slide in animation
        toast.classList.add('animate__slideInUp');
        
        // Auto dismiss toasts
        const dismissTime = parseInt(toast.dataset.autoDismiss) || 3000;
        
        setTimeout(() => {
            // Add slide out animation
            toast.classList.remove('animate__slideInUp');
            toast.classList.add('animate__slideOutDown');
            
            // Remove toast after animation completes
            setTimeout(() => {
                toast.remove();
            }, 500);
        }, dismissTime);
    });
}

/**
 * Utility function to add animation to element
 * @param {HTMLElement} element - Element to animate
 * @param {string} animation - Animation name (fadeIn, fadeOut, etc.)
 * @param {boolean} removeAfter - Whether to remove the animation class after completion
 */
function animateElement(element, animation, removeAfter = true) {
    // Add animation class
    element.classList.add(`animate__${animation}`);
    
    // Remove class after animation completes if specified
    if (removeAfter) {
        const animationDuration = getComputedStyle(element).animationDuration;
        const durationInMs = parseFloat(animationDuration) * 1000;
        
        setTimeout(() => {
            element.classList.remove(`animate__${animation}`);
        }, durationInMs);
    }
}

/**
 * Show loading spinner with animation
 * @param {HTMLElement} container - Container element for the spinner
 * @param {string} message - Optional loading message
 */
function showLoadingSpinner(container, message = "Loading...") {
    // Create spinner element
    const spinner = document.createElement('div');
    spinner.classList.add('loading-spinner-container');
    
    spinner.innerHTML = `
        <div class="loading-spinner"></div>
        <p class="loading-message">${message}</p>
    `;
    
    // Add spinner to container
    container.appendChild(spinner);
    
    // Animate spinner appearance
    animateElement(spinner, 'fadeIn');
    
    return spinner;
}

/**
 * Hide loading spinner with animation
 * @param {HTMLElement} spinner - Spinner element to hide
 */
function hideLoadingSpinner(spinner) {
    // Animate spinner disappearance
    animateElement(spinner, 'fadeOut');
    
    // Remove spinner after animation completes
    setTimeout(() => {
        spinner.remove();
    }, 500);
}

/**
 * Page transition animation
 * @param {string} url - URL to navigate to
 */
function pageTransition(url) {
    // Create overlay element
    const overlay = document.createElement('div');
    overlay.classList.add('page-transition-overlay');
    
    // Add to body
    document.body.appendChild(overlay);
    
    // Animate overlay
    overlay.style.opacity = '1';
    
    // Navigate to new page after animation completes
    setTimeout(() => {
        window.location.href = url;
    }, 500);
}

/**
 * Scroll to element with animation
 * @param {HTMLElement} element - Element to scroll to
 * @param {number} duration - Animation duration in milliseconds
 * @param {number} offset - Offset from the top in pixels
 */
function scrollToElement(element, duration = 500, offset = 0) {
    const targetPosition = element.getBoundingClientRect().top + window.pageYOffset - offset;
    const startPosition = window.pageYOffset;
    const distance = targetPosition - startPosition;
    let startTime = null;
    
    function animation(currentTime) {
        if (startTime === null) startTime = currentTime;
        const timeElapsed = currentTime - startTime;
        const run = ease(timeElapsed, startPosition, distance, duration);
        window.scrollTo(0, run);
        if (timeElapsed < duration) requestAnimationFrame(animation);
    }
    
    function ease(t, b, c, d) {
        t /= d / 2;
        if (t < 1) return c / 2 * t * t + b;
        t--;
        return -c / 2 * (t * (t - 2) - 1) + b;
    }
    
    requestAnimationFrame(animation);
}