// Minimal JS for form handling and accessibility
document.addEventListener('DOMContentLoaded', function(){
  const form = document.getElementById('loginForm');
  form.addEventListener('submit', function(e){
    e.preventDefault();
    // simple visual feedback
    const btn = form.querySelector('.btn-submit');
    btn.textContent = 'Logging...';
    btn.disabled = true;
    setTimeout(()=>{
      btn.textContent = 'Login';
      btn.disabled = false;
      alert('Form submitted (demo)');
    },900);
  });
});
