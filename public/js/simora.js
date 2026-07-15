document.addEventListener('DOMContentLoaded', function () {
  const toggle = document.querySelector('.mobile-nav-toggle');
  const sidebar = document.querySelector('.simora-sidebar');

  if (toggle && sidebar) {
    toggle.addEventListener('click', function () {
      document.body.classList.toggle('sidebar-open');
    });
  }

  document.querySelectorAll('.simora-sidebar .menu-item').forEach(function (item) {
    item.addEventListener('click', function () {
      document.body.classList.remove('sidebar-open');
    });
  });

  window.addEventListener('resize', function () {
    if (window.innerWidth > 720) {
      document.body.classList.remove('sidebar-open');
    }
  });
});
