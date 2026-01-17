    //section
    // Dropdown Toggle
const dropdownButtonUO = document.getElementById('userOptionsToggle');
const dropdownButtonNotification = document.getElementById('notificationsToggle');

const dropdownMenuUO = document.querySelector('.user-options-menu');
const dropdownMenuNotification = document.querySelector('.notification-menu');

// dropdown detalles usuario
dropdownButtonUO.addEventListener('click', (e) => {
    e.stopPropagation(); // Prevent event bubbling
    dropdownMenuUO.classList.toggle('show'); // Show/hide dropdown menu
    const isExpanded = dropdownButtonUO.getAttribute('aria-expanded') === 'true';
    dropdownButtonUO.setAttribute('aria-expanded', !isExpanded);
});

// Dropdown notificaciones
dropdownButtonNotification.addEventListener('click', (e) => {
    e.stopPropagation(); // Prevent event bubbling
    dropdownMenuNotification.classList.toggle('show'); // Show/hide dropdown menu
    const isExpanded = dropdownButtonNotification.getAttribute('aria-expanded') === 'true';
    dropdownButtonNotification.setAttribute('aria-expanded', !isExpanded);
});

// Close the dropdown menu when clicking outside
document.addEventListener('click', (e) => {
    if (!dropdownMenu.contains(e.target) && !dropdownButton.contains(e.target)) {
        dropdownMenuUO.classList.remove('show');
        dropdownMenuNotification.classList.remove('show');
        dropdownButtonUO.setAttribute('aria-expanded', 'false');
        dropdownButtonNotification.setAttribute('aria-expanded', 'false');
    }
});

