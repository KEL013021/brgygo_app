$(document).ready(function () {

  // 🔔 Notifications button
  $('#notifBtn').click(function () {
    $('.badge-number').fadeOut();
    alert('Open notifications panel...');
  });

  // 🔍 Live service search
  document.getElementById("serviceSearch").addEventListener("keyup", function () {
    const search = this.value.toLowerCase();
    const rows = document.querySelectorAll("#servicesTableBody tr");

    rows.forEach(row => {
      const rowText = row.innerText.toLowerCase();
      row.style.display = rowText.includes(search) ? "" : "none";
    });
  });

  // ✅ Modal show if status in URL
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get('status') === 'success') {
    new bootstrap.Modal(document.getElementById('successModal')).show();
  }
  if (urlParams.get('status') === 'exists') {
    new bootstrap.Modal(document.getElementById('existsModal')).show();
  }
  // Remove query from URL (for clean reload)
  if (urlParams.get('status')) {
    const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
    window.history.pushState({ path: newUrl }, "", newUrl);
  }
});
