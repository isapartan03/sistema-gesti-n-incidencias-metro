
const urlParams = new URLSearchParams(window.location.search);
const error = urlParams.get('e');
const intentos = urlParams.get('i') || 0; // Obtiene el número de intentos o 0 si no existe


//funcion para detectar el tipo de error
function mostrarNotificacion(tipo, intentos) {
    let cantidad;
    const notificacion = document.getElementById("notificacion");
    cantidad= "Intentos: "+intentos;
    if (intentos==0) {cantidad='';}

    // Mensajes de las notificaciones
    let color, mensaje;
    switch (tipo) {
        case "0":
            color = "#F44336"; 
            mensaje = "❌ Error: Credenciales Incorrectas. " + cantidad;
            break;
        case "1":
            color = "#F44336"; 
            mensaje = "❌ Error: Algo salió mal.";
            break;
        case "2":
            color = "#F44336"; 
            mensaje = "❌ Error: Usuario Inexistente";
            break;
         case "3":
            color = "#F44336"; 
            mensaje = "❌ Error: Respuestas Invalidas";
            break;
        case "4":
            color = "#F44336"; 
            mensaje = "❌ Error: Usuario Bloqueado";
            break;
        default:
            color = "#F44336"; 
            mensaje = "❌ Error: Algo salió mal.";
    }

    notificacion.style.backgroundColor = color;
    notificacion.innerText = mensaje;
    notificacion.style.display = "block";

    setTimeout(() => {
        notificacion.style.display = "none";
    }, 5000); 
}

// Si existe un error en la URL, mostrar la notificación con el tipo correspondiente y si no no muetra nada 
if (error) {
    mostrarNotificacion(error, intentos);
}