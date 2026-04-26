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
            window.location.href = '../Controller/TecCont.php?action=delete&carnet=' + encodeURIComponent(idAEliminar);
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