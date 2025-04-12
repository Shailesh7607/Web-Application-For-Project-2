const profile = document.querySelector('.header .flex .profile');
const navbar = document.querySelector('.header .flex .navbar');
const userBtn = document.querySelector('#user-btn');
const subImages = document.querySelectorAll('.update-product .image-container');
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
// Changing the image
mainImage?.addEventListener('click',() => {
   console.log("Main image clicked\ncurrent image source:",mainImage.getAttribute('src'));
});
   //Admin account deletion section
   document.querySelector('#admin-delete-form')?.addEventListener('submit', (e) => { e.preventDefault();
      const username = document.querySelector('#username').value;
      if (username === 'admin') { 
         document.querySelector('#message').style.display = 'block';
         e.target.reset();
         document.querySelector('#delete-btn').disabled = true;
      } else alert("Username not found.");
   });