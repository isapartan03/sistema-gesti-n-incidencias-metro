 
  document.getElementById('btnGuardar').addEventListener('click', function(e) {
  
    const form = document.getElementById('fallaForm');
    if(form.checkValidity()) {
    
      document.getElementById('loadingOverlay').style.display = 'flex';
      
      
      this.disabled = true;
      this.classList.add('btn-disabled');
      this.innerHTML = 'Procesando...';
      
      
      setTimeout(() => {
        form.submit();
      }, 100);
    }
  });