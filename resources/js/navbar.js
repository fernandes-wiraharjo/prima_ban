document.getElementById('logoutBtn').addEventListener('click', function (event) {
  event.preventDefault();

  // Redirect to the logout route URL
  window.location.href = this.getAttribute('href');
});
