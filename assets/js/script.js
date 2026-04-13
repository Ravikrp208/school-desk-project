// assets/js/script.js

document.addEventListener('DOMContentLoaded', () => {
    // Mobile Menu Toggle logic
    const menuBtn = document.querySelector('.menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const navLinks = document.querySelector('.nav-links');
    const menuIcon = menuBtn ? menuBtn.querySelector('i') : null;
    
    if(menuBtn) {
        menuBtn.addEventListener('click', () => {
            const isDrawerOpen = mobileMenu ? mobileMenu.classList.contains('translate-x-0') : false;
            
            // Toggle Drawer (My Premium Version)
            if (mobileMenu) {
                if (isDrawerOpen) {
                    mobileMenu.classList.remove('translate-x-0');
                    mobileMenu.classList.add('translate-x-full');
                } else {
                    mobileMenu.classList.add('translate-x-0');
                    mobileMenu.classList.remove('translate-x-full');
                }
            }

            // Toggle Dropdown (User's Shown Version)
            if (navLinks) {
                navLinks.classList.toggle('mobile-menu-active');
                navLinks.classList.toggle('hidden');
            }

            // Toggle Icon
            if (menuIcon) {
                const isNowOpen = navLinks ? navLinks.classList.contains('mobile-menu-active') : !isDrawerOpen;
                if (isNowOpen) {
                    menuIcon.classList.remove('fa-bars');
                    menuIcon.classList.add('fa-xmark');
                    document.body.style.overflow = 'hidden'; 
                } else {
                    menuIcon.classList.remove('fa-xmark');
                    menuIcon.classList.add('fa-bars');
                    document.body.style.overflow = '';
                }
            }
        });
    }
});
