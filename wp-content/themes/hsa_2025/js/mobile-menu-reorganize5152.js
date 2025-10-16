/**
 * Mobile Menu Reorganization
 * Moves the top menu after the main menu on mobile devices
 * and restores it to original position on larger screens
 */

document.addEventListener('DOMContentLoaded', function() {
    // WordPress generates menus with specific naming patterns
    // Top menu: wp_nav_menu with theme_location 'hsa-top-menu' creates #menu-hsa-top-menu or similar
    // Let's search for the actual menu elements dynamically
    
    const topNavContainer = document.querySelector('.top_navigation'); // Original container
    const mainNavContainer = document.querySelector('.main_navigation'); // Target container
    const mainMenu = document.querySelector('#main-menu'); // Main menu reference point
    
    // Find the top menu ul within the top navigation
    let topMenu = null;
    if (topNavContainer) {
        topMenu = topNavContainer.querySelector('ul'); // First ul in top navigation
    }
    
    // If we can't find it by container, try common WordPress menu ID patterns
    if (!topMenu) {
        const possibleIds = [
            '#menu-hsa-top-menu',
            '#menu-top-menu', 
            'ul[id*="top-menu"]',
            'ul[id*="hsa-top"]'
        ];
        
        for (let selector of possibleIds) {
            topMenu = document.querySelector(selector);
            if (topMenu) break;
        }
    }
    
    // Store original parent for restoration
    let originalParent = topNavContainer;
    let isMenuMoved = false;
    
    function reorganizeMenu() {
        const isMobile = window.innerWidth <= 768; // Adjust breakpoint as needed
        
        if (isMobile && !isMenuMoved && topMenu && mainMenu && mainNavContainer) {
            // Move top menu after main menu on mobile
            mainNavContainer.insertBefore(topMenu, mainMenu.nextSibling);
            isMenuMoved = true;
            console.log('Top menu moved to mobile position');
        } else if (!isMobile && isMenuMoved && topMenu && originalParent) {
            // Restore top menu to original position on desktop
            originalParent.appendChild(topMenu);
            isMenuMoved = false;
            console.log('Top menu restored to original position');
        }
    }
    
    // Run on initial load (with small delay to ensure DOM is fully ready)
    setTimeout(reorganizeMenu, 100);
    
    // Run on window resize with debouncing
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(reorganizeMenu, 150);
    });
});
