<?php
// login.php
session_start();

// Archivo de conexión separado (mejor no incluir directamente index.php con HTML)
$host = 'db';
$usuario = 'usuario';
$contrasena = 'password';
$nombre_bd = 'tfg_app_db';

$mysqli = new mysqli($host, $usuario, $contrasena, $nombre_bd);
if ($mysqli->connect_error) {
    die("Conexión fallida: " . $mysqli->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = $_POST['correo'];
    $contraseña = $_POST['contraseña'];

    $query = "SELECT * FROM usuarios WHERE correo = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if ($user['contraseña'] == $contraseña) {
            $_SESSION['user_id'] = $user['id_usuario'];
            $_SESSION['tipo_usuario'] = $user['tipo_usuario'];
            switch ($user['tipo_usuario']) {
              case 'admin':
                  header("Location: inicio_admin.php");
                  break;
              case 'profesor':
                  header("Location: inicio_profesor.php");
                  break;
              case 'alumno':
                  header("Location: inicio_alumno.php");
                  break;
              default:
                  // // Puedes redirigir a una página de error o cerrar sesión
                  // header("Location: error.php");
                  break;
          }
          exit();
        } else {
            $error = "Contraseña incorrecta";
        }
    } else {
        $error = "Correo electrónico no encontrado";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar sesión</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f0f2f5;
    }
    .login-container {
      margin-top: 100px;
    }
    .card {
      border-radius: 1rem;
      box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }
    .form-control {
      border-radius: 0.5rem;
    }
    .btn {
      border-radius: 0.5rem;
    }
  </style>
</head>
<body>
  <div class="container login-container d-flex justify-content-center align-items-center">
    <div class="card p-5 col-md-6 col-lg-4">
      <h3 class="text-center mb-4">Iniciar sesión</h3>
      
      <?php if (isset($error)): ?>
        <div class="alert alert-danger text-center" role="alert">
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="login.php">
        <div class="mb-3">
          <label for="correo" class="form-label">Correo electrónico</label>
          <input type="email" class="form-control" id="correo" name="correo" required>
        </div>
        <div class="mb-4">
          <label for="contraseña" class="form-label">Contraseña</label>
          <input type="password" class="form-control" id="contraseña" name="contraseña" required>
        </div>
        <div class="d-grid">
          <button type="submit" class="btn btn-primary">Iniciar sesión</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
