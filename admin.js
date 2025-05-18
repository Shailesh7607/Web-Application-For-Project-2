const profile = document.querySelector('.header .flex .profile');
const navbar = document.querySelector('.header .flex .navbar');
const userBtn = document.querySelector('#user-btn');
const menuBtn = document.querySelector('#menu-btn');
const mainImage = document.querySelector('.update-product .image-container .main-image img');
const adminDeleteForm = document.querySelector('#admin-delete-form');

// Profile and navbar visibility toggle
userBtn?.addEventListener('click', () => {
    profile?.classList.toggle('active');
    navbar?.classList.remove('active');
});

menuBtn?.addEventListener('click', () => {
    navbar?.classList.toggle('active');
    profile?.classList.remove('active');
});

// Hide profile and navbar on scroll
window.addEventListener('scroll', () => {
    profile?.classList.remove('active');
    navbar?.classList.remove('active');
});

// Admin account deletion
adminDeleteForm?.addEventListener('submit', (e) => {
    e.preventDefault();
    const usernameInput = adminDeleteForm.querySelector('input[name="username"]');
    const username = usernameInput?.value.trim();
    const message = document.querySelector('#message');
    const deleteBtn = document.querySelector('#delete-btn');

    if (username === 'admin') {
        if (message) message.style.display = 'block';
        adminDeleteForm.reset();
        if (deleteBtn) deleteBtn.disabled = true;
    } else {
        alert('Username not found.');
    }
});

// Image click handler for update_product.php
mainImage?.addEventListener('click', () => {
    console.log('Main image clicked, current source:', mainImage.getAttribute('src'));
   
});