document.addEventListener('DOMContentLoaded', function() {
    // Toggle sidebar on mobile
    const sidebarToggle = document.createElement('button');
    sidebarToggle.className = 'sidebar-toggle btn btn-primary d-md-none';
    sidebarToggle.innerHTML = '<i class="fas fa-bars"></i>';
    sidebarToggle.style.position = 'fixed';
    sidebarToggle.style.bottom = '20px';
    sidebarToggle.style.right = '20px';
    sidebarToggle.style.zIndex = '1000';
    sidebarToggle.style.width = '50px';
    sidebarToggle.style.height = '50px';
    sidebarToggle.style.borderRadius = '50%';
    sidebarToggle.style.display = 'flex';
    sidebarToggle.style.alignItems = 'center';
    sidebarToggle.style.justifyContent = 'center';
    sidebarToggle.style.boxShadow = '0 2px 10px rgba(0,0,0,0.2)';
    
    document.body.appendChild(sidebarToggle);
    
    sidebarToggle.addEventListener('click', function() {
        document.querySelector('.sidebar').classList.toggle('show');
    });
    
    // Active menu item highlight
    const currentPage = window.location.pathname.split('/').pop();
    const menuItems = document.querySelectorAll('.sidebar-menu li a');
    
    menuItems.forEach(item => {
        item.classList.remove('active');
        if (item.getAttribute('href') === currentPage) {
            item.classList.add('active');
        }
    });
    
    // Add animation to cards
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'all 0.5s ease ' + (index * 0.1) + 's';
        
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100);
    });
});