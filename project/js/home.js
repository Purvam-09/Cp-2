document.addEventListener('DOMContentLoaded', function() {
    // Testimonial slider
    const testimonials = document.querySelectorAll('.testimonial');
    const dots = document.querySelectorAll('.dot');
    const prevBtn = document.querySelector('.testimonial-btn.prev');
    const nextBtn = document.querySelector('.testimonial-btn.next');
    
    if (testimonials.length && dots.length && prevBtn && nextBtn) {
        let currentIndex = 0;
        
        // Function to show a specific testimonial
        const showTestimonial = (index) => {
            // Hide all testimonials
            testimonials.forEach(testimonial => {
                testimonial.classList.remove('active');
            });
            
            // Remove active class from all dots
            dots.forEach(dot => {
                dot.classList.remove('active');
            });
            
            // Show the selected testimonial and activate the corresponding dot
            testimonials[index].classList.add('active');
            dots[index].classList.add('active');
        };
        
        // Event listeners for next/prev buttons
        nextBtn.addEventListener('click', () => {
            currentIndex = (currentIndex + 1) % testimonials.length;
            showTestimonial(currentIndex);
        });
        
        prevBtn.addEventListener('click', () => {
            currentIndex = (currentIndex - 1 + testimonials.length) % testimonials.length;
            showTestimonial(currentIndex);
        });
        
        // Event listeners for dots
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                currentIndex = index;
                showTestimonial(currentIndex);
            });
        });
        
        // Auto slide every 5 seconds
        setInterval(() => {
            currentIndex = (currentIndex + 1) % testimonials.length;
            showTestimonial(currentIndex);
        }, 5000);
    }
});