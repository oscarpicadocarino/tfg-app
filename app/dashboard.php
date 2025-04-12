<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$tipo = $_SESSION['tipo_usuario'] ?? 'desconocido';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #eef2f7;
    }
    .dashboard-container {
      margin-top: 100px;
    }
    .card {
      border-radius: 1rem;
      box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>
  <div class="container dashboard-container d-flex justify-content-center align-items-center">
    <div class="card p-5 col-md-6 text-center">
      <h2>Bienvenido al panel</h2>
      <p class="lead">Has iniciado sesión como <strong><?= ucfirst($tipo) ?></strong>.</p>
      <a href="logout.php" class="btn btn-outline-danger mt-3">Cerrar sesión</a>
    </div>
  </div>
</body>
</html>
