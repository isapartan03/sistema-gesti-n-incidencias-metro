document.addEventListener('DOMContentLoaded', function () {
    let idAccion = null;

    const modals = {
        delete: document.getElementById('modalDelete'),
        reset: document.getElementById('modalReset'),
        susp: document.getElementById('modalSusp'),
        habi: document.getElementById('modalHabi')
    };

    // Mostrar modales
    document.querySelectorAll('.btn-show-delete').forEach(btn => {
        btn.addEventListener('click', () => {
            idAccion = btn.dataset.id;
            modals.delete.style.display = 'flex';
        });
    });

    document.querySelectorAll('.btn-show-reset').forEach(btn => {
        btn.addEventListener('click', () => {
            idAccion = btn.dataset.id;
            modals.reset.style.display = 'flex';
        });
    });

    document.querySelectorAll('.btn-show-susp').forEach(btn => {
        btn.addEventListener('click', () => {
            idAccion = btn.dataset.id;
            modals.susp.style.display = 'flex';
        });
    });

    document.querySelectorAll('.btn-show-hab').forEach(btn => {
        btn.addEventListener('click', () => {
            idAccion = btn.dataset.id;
            modals.habi.style.display = 'flex';
        });
    });

    // Confirmar acciones
    document.getElementById('confirmDelete').addEventListener('click', () => {
        window.location.href = 'index.php?c=usuario&a=elimi&id=' + encodeURIComponent(idAccion);
    });

    document.getElementById('confirmReset').addEventListener('click', () => {
        window.location.href = 'index.php?c=usuario&a=reset&id=' + encodeURIComponent(idAccion);
    });

    document.getElementById('confirmSusp').addEventListener('click', () => {
        window.location.href = 'index.php?c=usuario&a=susp&id=' + encodeURIComponent(idAccion);
    });

    document.getElementById('confirmHabi').addEventListener('click', () => {
        window.location.href = 'index.php?c=usuario&a=habi&id=' + encodeURIComponent(idAccion);
    });

    // Cerrar modal
    document.querySelectorAll('.cancelModal').forEach(btn => {
        btn.addEventListener('click', () => {
            for (let key in modals) modals[key].style.display = 'none';
            idAccion = null;
        });
    });

    // Cerrar al hacer clic fuera del modal
    window.addEventListener('click', e => {
        for (let key in modals) {
            if (e.target === modals[key]) {
                modals[key].style.display = 'none';
                idAccion = null;
            }
        }
    });
});