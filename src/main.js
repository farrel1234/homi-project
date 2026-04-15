import './style.css'

// Premium Scroll Animation System
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('animate-active');
            // Unobserve after animation is triggered
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

document.addEventListener('DOMContentLoaded', () => {
    // Add scroll-trigger class to elements that should animate
    const animatables = document.querySelectorAll('.animate-on-scroll');
    animatables.forEach((el, index) => {
        // Add a small delay for staggered effect if they are in a grid
        if (el.closest('.grid')) {
            el.style.transitionDelay = `${(index % 4) * 100}ms`;
        }
        observer.observe(el);
    });
    
    console.log('HOMI Landing Page: Premium Animations Initialized');
});
