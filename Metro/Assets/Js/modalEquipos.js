let idAEliminar = null;

document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('modalConfirmDelete');

    document.querySelectorAll('.btn-show-modal').forEach(btn => {
        btn.addEventListener('click', function () {
            idAEliminar = this.getAttribute('data-id');
            modal.style.display = 'flex';
        });
    });
//si le dan confirmar
    document.getElementById('btnConfirmDelete').addEventListener('click', function () {
        if (idAEliminar) {
            window.location.href = '../Controller/EquipoC.php?action=delete&id=' + encodeURIComponent(idAEliminar);
        }
    });
// si le dan cancelar
    document.getElementById('btnCancelDelete').addEventListener('click', function () {
        modal.style.display = 'none';
        idAEliminar = null;
    });

    window.addEventListener('click', function (e) {
        if (e.target === modal) {
            modal.style.display = 'none';
            idAEliminar = null;
        }
    });
});
let idCambioEstado = null;
let accionCambioEstado = null;

document.querySelectorAll('.btn-cambiar-estado').forEach(btn => {
    btn.addEventListener('click', function () {
        idCambioEstado = this.getAttribute('data-id');
        accionCambioEstado = this.getAttribute('data-action');

        const mensaje = accionCambioEstado === 'desincorporar'
            ? '¿Estás seguro de desincorporar este equipo?'
            : '¿Estás seguro de incorporar este equipo?';

        document.getElementById('mensajeCambioEstado').textContent = mensaje;
        document.getElementById('modalCambioEstado').style.display = 'flex';
    });
});

document.getElementById('btnConfirmarCambioEstado').addEventListener('click', function () {
    if (idCambioEstado && accionCambioEstado) {
        window.location.href = `../Controller/EquipoC.php?action=${accionCambioEstado}&id=${encodeURIComponent(idCambioEstado)}`;
    }
});

document.getElementById('btnCancelarCambioEstado').addEventListener('click', function () {
    document.getElementById('modalCambioEstado').style.display = 'none';
    idCambioEstado = null;
    accionCambioEstado = null;
});

window.addEventListener('click', function (e) {
    const modal = document.getElementById('modalCambioEstado');
    if (e.target === modal) {
        modal.style.display = 'none';
        idCambioEstado = null;
        accionCambioEstado = null;
    }
});