document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        const fileInput = form.querySelector('input[type="file"]');
        if (fileInput) {
            const file = fileInput.files[0];
            if (file) {
                const validTypes = fileInput.accept.split(',').map(type => type.trim().replace('.', ''));
                const fileType = file.name.split('.').pop().toLowerCase();
                const maxSize = fileInput.name === 'photo' ? 2 * 1024 * 1024 : 5 * 1024 * 1024;

                if (!validTypes.includes(fileType)) {
                    alert('Format de fichier non autorisé (' + fileInput.accept + ').');
                    e.preventDefault();
                } else if (file.size > maxSize) {
                    alert('Le fichier est trop volumineux (max ' + (maxSize / 1024 / 1024) + ' Mo).');
                    e.preventDefault();
                }
            }
        }

        if (form.action.includes('upload_justificatif.php')) {
            const dateAbsence = form.querySelector('input[name="date_absence"]').value;
            if (!dateAbsence || new Date(dateAbsence) > new Date()) {
                alert('Veuillez entrer une date d\'absence valide (non future).');
                e.preventDefault();
            }
        }
    });
});