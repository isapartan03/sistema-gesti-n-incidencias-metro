document.addEventListener('DOMContentLoaded', function() {
  const submitBtn     = document.getElementById('submitBtn');
  const customConfirm = document.getElementById('customConfirm');
  const confirmCancel = document.getElementById('confirmCancel');
  const confirmSubmit = document.getElementById('confirmSubmit');
  const reporteForm   = document.getElementById('reporteForm');

  if (submitBtn && reporteForm) {
    submitBtn.addEventListener('click', function(e) {
      e.preventDefault();

      if (!reporteForm.checkValidity()) {
        reporteForm.reportValidity();
        return;
      }

      customConfirm.style.display = 'flex';
    });
  }

  if (confirmCancel) {
    confirmCancel.addEventListener('click', () => customConfirm.style.display = 'none');
  }

  if (confirmSubmit && reporteForm) {
    confirmSubmit.addEventListener('click', () => reporteForm.submit());
  }

  window.addEventListener('click', function(event) {
    if (event.target === customConfirm) {
      customConfirm.style.display = 'none';
    }
  });
});
