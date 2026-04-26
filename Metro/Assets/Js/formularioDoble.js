function mostrarParte(parte) {
    // Validar la parte 1 antes de continuar
    if(parte === 2) {
        const inputs = document.querySelectorAll('#parte1 input[required]');
        const notificacion = document.getElementById("alerta");

        let valido = true;
        
        inputs.forEach(input => {
            if(!input.value.trim()) {
                valido = false;
            } else {
            }
        });
        
        if(!valido) {
           	notificacion.style.color = 'red';
            notificacion.innerText = "Rellene los campos";
            notificacion.style.display = "block";
             setTimeout(() => {
        notificacion.style.display = "none";
    }, 3000);
            return;
        }
      
    }

    
    // Mostrar la parte seleccionada y ocultar la otra
    document.getElementById('parte1').style.display = parte === 1 ? 'block' : 'none';
    document.getElementById('parte2').style.display = parte === 2 ? 'block' : 'none';
    
    // Desplazar al inicio del formulario
    window.scrollTo(0, document.querySelector('.form').offsetTop);

}