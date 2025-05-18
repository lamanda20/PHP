<?php session_start();?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ENSA - QR Scanner</title>
    <!-- Gardons les mêmes scripts que dans la version originale pour assurer la compatibilité -->
    <script type="text/javascript" src="js/adapter.min.js"></script>
    <script type="text/javascript" src="js/vue.min.js"></script>
    <script type="text/javascript" src="js/instascan.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --success-color: #2ecc71;
            --danger-color: #e74c3c;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }

        .navbar {
            background-color: var(--secondary-color);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-weight: 700;
            color: white !important;
            font-size: 1.5rem;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .card-header {
            background-color: var(--secondary-color);
            color: white;
            font-weight: 600;
            padding: 15px 20px;
            border-bottom: none;
        }

        #preview {
            border-radius: 10px;
            overflow: hidden;
            width: 100%;
            height: auto;
        }

        .table {
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-radius: 10px;
            overflow: hidden;
        }

        .table thead {
            background-color: var(--secondary-color);
            color: white;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
        }

        .btn-primary:hover {
            background-color: #2980b9;
        }

        .alert {
            border-radius: 8px;
            border: none;
            padding: 15px;
            margin-top: 20px;
        }

        .alert-success {
            background-color: var(--success-color);
            color: white;
        }

        .alert-danger {
            background-color: var(--danger-color);
            color: white;
        }

        .camera-container {
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="#">
            <i class="fas fa-university mr-2"></i>ENSA
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="#">
                        <i class="fas fa-home mr-1"></i>Accueil
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
                        <i class="fas fa-file-alt mr-1"></i>Pages
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#">Page 1</a>
                        <a class="dropdown-item" href="#">Page 2</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">Page 3</a>
                    </div>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-user-plus mr-1"></i>S'inscrire
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-sign-in-alt mr-1"></i>Se connecter
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <?php
    if(isset($_SESSION['error'])){
        echo "
            <div class='alert alert-danger'>
                <i class='fas fa-exclamation-circle mr-2'></i>
                <strong>Erreur!</strong> ".$_SESSION['error']."
            </div>
            ";
        unset($_SESSION['error']);
    }

    if(isset($_SESSION['success'])){
        echo "
            <div class='alert alert-success'>
                <i class='fas fa-check-circle mr-2'></i>
                <strong>Succès!</strong> ".$_SESSION['success']."
            </div>
            ";
        unset($_SESSION['success']);
    }
    ?>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-camera mr-2"></i>Scanner QR Code
                </div>
                <div class="card-body p-0">
                    <div class="camera-container">
                        <video id="preview" width="100%"></video>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-qrcode mr-2"></i>Résultat du Scan
                </div>
                <div class="card-body">
                    <form action="insert1.php" method="post" class="form-horizontal">
                        <div class="form-group">
                            <label><i class="fas fa-keyboard mr-1"></i>SCAN QR CODE</label>
                            <input type="text" name="text" id="text" readonly placeholder="Scannez un QR code" class="form-control">
                        </div>
                    </form>

                    <div class="table-responsive mt-4">
                        <table class="table table-bordered">
                            <thead class="bg-dark text-white">
                            <tr>
                                <th><i class="fas fa-hashtag mr-1"></i>ID</th>
                                <th><i class="fas fa-id-card mr-1"></i>ID Étudiant</th>
                                <th><i class="fas fa-clock mr-1"></i>Heure d'arrivée</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $server = "localhost";
                            $username = "root";
                            $password = "";
                            $dbname = "qrcodedb";

                            $conn = new mysqli($server, $username, $password, $dbname);

                            if ($conn->connect_error) {
                                die("Connection failed: " . $conn->connect_error);
                            }

                            $sql = "SELECT id, student_id, time_in FROM table_attendance WHERE DATE(time_in) = CURDATE()";
                            $query = $conn->query($sql);

                            if($query->num_rows > 0) {
                                while($row = $query->fetch_assoc()) {
                                    ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo $row['student_id']; ?></td>
                                        <td><?php echo $row['time_in']; ?></td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                ?>
                                <tr>
                                    <td colspan="3" class="text-center">Aucune présence enregistrée aujourd'hui</td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/js/bootstrap.min.js"></script>
<script>
    // Utilisons exactement le même code JavaScript que dans la version originale
    let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });

    Instascan.Camera.getCameras().then(function (cameras) {
        if (cameras.length > 0) {
            scanner.start(cameras[0]);
        } else {
            alert('No cameras found');
        }
    }).catch(function(e) {
        console.error(e);
    });

    scanner.addListener('scan', function(c) {
        document.getElementById('text').value = c;
        document.forms[0].submit();
    });
</script>
</body>
</html>