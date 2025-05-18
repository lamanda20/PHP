// main.js - Gestion des interactions avec JavaScript

// Fonction pour vérifier le code d'activation par AJAX
function verifyCode() {
    const email = document.getElementById("email").value;
    const code = document.getElementById("code").value;
    const message = document.getElementById("message");

    // Vérification par AJAX
    fetch('verify_code.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `email=${encodeURIComponent(email)}&code=${encodeURIComponent(code)}`
    })
        .then(response => response.text())
        .then(data => {
            message.innerText = data;
            if (data.includes("Activation réussie")) {
                message.style.color = "green";
                setTimeout(() => {
                    window.location.href = "login.php";
                }, 2000);
            } else {
                message.style.color = "red";
            }
        });
}

// Fonction pour téléverser un fichier par AJAX (dashboard.php)
function uploadFile() {
    const form = document.getElementById("uploadForm");
    const formData = new FormData(form);
    const message = document.getElementById("message");

    fetch('upload_file.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.text())
        .then(data => {
            message.innerText = data;
            message.style.color = data.includes("téléversé avec succès") ? "green" : "red";
            if (data.includes("téléversé avec succès")) {
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            }
        });
}

// Fonction pour confirmer les actions administratives
function confirmAction(message) {
    return confirm(message);
}

// Gestion de la suppression de fichiers par AJAX (administrateur)
function deleteFile(fileId) {
    if (confirm("Êtes-vous sûr de vouloir supprimer ce fichier ?")) {
        fetch('admin_delete_file.php?id=' + fileId, {
            method: 'GET'
        })
            .then(response => response.text())
            .then(data => {
                alert(data);
                window.location.reload();
            });
    }
}

// Gestion de l'activation / désactivation d'un étudiant par AJAX (administrateur)
function toggleActivation(studentId) {
    fetch('admin_toggle_activation.php?id=' + studentId, {
        method: 'GET'
    })
        .then(response => response.text())
        .then(data => {
            alert(data);
            window.location.reload();
        });
}
