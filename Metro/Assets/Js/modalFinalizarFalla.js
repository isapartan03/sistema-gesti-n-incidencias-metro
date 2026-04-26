document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalConfirmDelete');
    const btnConfirmar = document.getElementById('btnConfirmDelete');
    const btnCancelar = document.getElementById('btnCancelDelete');
    let urlFinalizar = '';
    
    // Agregar evento a todos los botones de finalizar
    document.querySelectorAll('a.btn-danger[href*="controlador_delete_falla"]').forEach(boton => {
        boton.addEventListener('click', function(e) {
            e.preventDefault(); 
            urlFinalizar = this.href; 
           modal.style.display = 'flex'; 
        });
    });
    
    // Confirmar finalización
    btnConfirmar.addEventListener('click', function() {
        if (urlFinalizar) {
            window.location.href = urlFinalizar; // Redirigir a la URL de finalización
        }
    });
    
    // Cancelar finalización
    btnCancelar.addEventListener('click', function() {
        modal.style.display = 'none';
        urlFinalizar = ''; 
    });
    
    // Cerrar modal al hacer clic fuera del contenido
    window.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
            urlFinalizar = '';
        }
    });
    
   
});