const profile = document.querySelector('.header .flex .profile');
const navbar = document.querySelector('.header .flex .navbar');
const userBtn = document.querySelector('#user-btn');
const menuBtn = document.querySelector('#menu-btn');
const subImages = document.querySelectorAll('.update-product .image-container .sub-images img');
const mainImage = document.querySelector('.update-product .image-container .main-image img');

//profile visibility toggle
userBtn?.addEventListener('click', () => {
   profile?.classList.toggle('active');
   navbar?.classList.remove('active');
});
// navbar visibiltiy toggle
menuBtn?.addEventListener('click', () => {
   navbar?.classList.toggle('active');
   profile?.classList.remove('active');
});

// Hiding profile and navbar on scroll
window.addEventListener('scroll', () => {
   profile?.classList.remove('active');
   navbar?.classList.remove('active');
});

// Changing the main image when sub-image is clicked
subImages.forEach(img => {
   img.addEventListener('click', () => {
      const src = img.getAttribute('src');
      if (mainImage) mainImage.src = src;
   });
});