<?php
// eliminar_usuario.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require 'conexion.php';

if (isset($_GET['id_usuario'])) {
    $id_usuario = $_GET['id_usuario'];
    
    var_dump($id_usuario); // Verifica si se recibe correctamente el valor

    // Comenzamos una transacción
    $pdo->beginTransaction();
    
    try {
        // Eliminar al usuario de la tabla 'usuarios'
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id_usuario = :id_usuario");
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->execute();
        
        // Eliminar la relación del usuario con las clases (si es alumno)
        $stmt = $pdo->prepare("DELETE FROM alumnos_clases WHERE id_alumno = :id_usuario");
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->execute();
        
        // Confirmar la transacción
        $pdo->commit();
        
        // Redirigir a la página de gestión de usuarios sin parámetros
        echo "<script>alert('Usuario eliminado correctamente.'); window.location.href = 'gestion_usuario.php';</script>";
        exit();
    } catch (Exception $e) {
        // En caso de error, revertir la transacción
        $pdo->rollBack();
        echo "Error al eliminar el usuario: " . $e->getMessage();
        exit();
    }
} else {
    echo "No se ha especificado un usuario a eliminar.";
}
?>