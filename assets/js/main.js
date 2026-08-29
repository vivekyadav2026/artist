document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Mobile Navigation Menu Toggle
    const mobileToggle = document.querySelector('.mobile-nav-toggle');
    const mainNav = document.querySelector('.main-nav');
    
    if (mobileToggle && mainNav) {
        mobileToggle.addEventListener('click', function() {
            mainNav.classList.toggle('active');
            
            // Toggle hamburger icon between ☰ and ✕
            const isExpanded = mainNav.classList.contains('active');
            mobileToggle.innerHTML = isExpanded ? '&#10006;' : '&#9776;';
        });
        
        // Close menu when clicking a link
        const links = mainNav.querySelectorAll('a');
        links.forEach(link => {
            link.addEventListener('click', function() {
                mainNav.classList.remove('active');
                mobileToggle.innerHTML = '&#9776;';
            });
        });
    }

    // 2. Active Link Highlighting on Scroll
    const sections = document.querySelectorAll('section[id]');
    const navItems = document.querySelectorAll('.main-nav li');
    
    function highlightNav() {
        let scrollY = window.pageYOffset;
        
        sections.forEach(current => {
            const sectionHeight = current.offsetHeight;
            const sectionTop = current.offsetTop - 120; // Offset for sticky navbar
            const sectionId = current.getAttribute('id');
            
            if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
                navItems.forEach(item => {
                    item.classList.remove('active');
                    const link = item.querySelector('a');
                    if (link && link.getAttribute('href') === '#' + sectionId) {
                        item.classList.add('active');
                    }
                });
            }
        });
    }
    window.addEventListener('scroll', highlightNav);

    // 3. Dynamic Gallery Category Filter
    const filterButtons = document.querySelectorAll('.filter-btn');
    const galleryItems = document.querySelectorAll('.gallery-item');
    
    if (filterButtons.length > 0 && galleryItems.length > 0) {
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons and add to this one
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                const filterValue = this.getAttribute('data-filter');
                
                galleryItems.forEach(item => {
                    const itemCategory = item.getAttribute('data-category');
                    
                    if (filterValue === 'all' || filterValue === itemCategory.toLowerCase()) {
                        item.style.display = 'block';
                        // Add fade-in animation
                        item.style.opacity = '0';
                        setTimeout(() => {
                            item.style.opacity = '1';
                            item.style.transition = 'opacity 0.3s ease';
                        }, 50);
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    }

    // 4. AJAX Enquiry Form Submissions
    const forms = document.querySelectorAll('.enquiry-form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = form.querySelector('button[type="submit"]');
            const messageBox = form.querySelector('.form-message');
            
            // Basic UI loading feedback
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Sending... <span class="spinner"></span>';
            
            // Client-side validations
            const phoneField = form.querySelector('input[name="phone"]');
            const phoneVal = phoneField ? phoneField.value.trim() : '';
            const phonePattern = /^[0-9+() -]{10,18}$/;
            
            if (phoneField && !phonePattern.test(phoneVal)) {
                showFormMessage(messageBox, 'Please enter a valid 10-digit phone number.', 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
                return;
            }
            
            const formData = new FormData(form);
            
            // Submit data using Fetch API
            fetch('submit-enquiry.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    showFormMessage(messageBox, data.message, 'success');
                    form.reset();
                } else {
                    showFormMessage(messageBox, data.message || 'Something went wrong. Please try again.', 'error');
                }
            })
            .catch(error => {
                console.error('Error submitting form:', error);
                showFormMessage(messageBox, 'Unable to submit enquiry. Please check your internet connection and try again.', 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            });
        });
    });
    
    function showFormMessage(container, message, type) {
        if (!container) return;
        
        container.innerHTML = message;
        container.className = 'form-message ' + type;
        container.style.display = 'block';
        
        // Auto-scroll message into view if offscreen
        const rect = container.getBoundingClientRect();
        if (rect.top < 0 || rect.bottom > window.innerHeight) {
            container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }
});
