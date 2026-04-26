// Función para validar la contraseña
        function validarContrasena() {
            const contrasena = document.getElementById('pass').value;
            const errorElement = document.getElementById('errorContrasena');
            
           
            const longitudValida = contrasena.length >= 8;
            const tieneMayuscula = /[A-Z]/.test(contrasena);
            const tieneMinuscula = /[a-z]/.test(contrasena);
            const tieneNumero = /[0-9]/.test(contrasena);
            const tieneEspecial = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(contrasena);

            
            
            actualizarRequisito('longitud', longitudValida);
            actualizarRequisito('mayuscula', tieneMayuscula);
            actualizarRequisito('minuscula', tieneMinuscula);
            actualizarRequisito('numero', tieneNumero);
            actualizarRequisito('especial', tieneEspecial);
          
            
           
            const valida = longitudValida && tieneMayuscula && tieneMinuscula && tieneNumero && tieneEspecial;
            
            if (!valida && contrasena.length > 0) {
                errorElement.textContent = "La contraseña no cumple con todos los requisitos.";
                errorElement.style.display = "block";
            } else {
                errorElement.style.display = "none";
            }
            
            return valida;
        }
        
      
        function validarConfirmacion() {
            const contrasena = document.getElementById('pass').value;
            const confirmacion = document.getElementById('confirmarPass').value;
            const errorElement = document.getElementById('errorConfirmacion');
            
            if (confirmacion.length > 0 && contrasena !== confirmacion) {
                errorElement.textContent = "Las contraseñas no coinciden.";
                errorElement.style.display = "block";
                return false;
            } else {
                errorElement.style.display = "none";
                return true;
            }
        }
        
        
        function actualizarRequisito(elementId, condicion) {
            const element = document.getElementById(elementId);
            if (condicion) {
                element.classList.add('cumplido');
            } else {
                element.classList.remove('cumplido');
            }
        }
        
       
        document.getElementById('formRecuperacion').addEventListener('submit', function(event) {
            const contrasenaValida = validarContrasena();
            const confirmacionValida = validarConfirmacion();
            
            if (!contrasenaValida || !confirmacionValida) {
                event.preventDefault();
                if (!contrasenaValida) {
                    document.getElementById('errorContrasena').textContent = "La contraseña no cumple con todos los requisitos.";
                    document.getElementById('errorContrasena').style.display = "block";
                }
                if (!confirmacionValida) {
                    document.getElementById('errorConfirmacion').textContent = "Las contraseñas no coinciden.";
                    document.getElementById('errorConfirmacion').style.display = "block";
                }
            }
        });
        
        
        document.querySelector('.borrar').addEventListener('click', function() {
            document.querySelectorAll('.requisito').forEach(el => {
                el.classList.remove('cumplido');
            });
            document.getElementById('errorContrasena').style.display = 'none';
            document.getElementById('errorConfirmacion').style.display = 'none';
        });