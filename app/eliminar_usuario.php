<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require 'conexion.php';

if (isset($_GET['id_usuario'])) {
    $id_usuario = $_GET['id_usuario'];
    
    // Comenzamos una transacción
    $pdo->beginTransaction();
    
    try {
        // 1. Eliminar la relación del usuario con las clases (si es alumno)
        $stmt = $pdo->prepare("DELETE FROM alumnos_clases WHERE id_alumno = :id_usuario");
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->execute();

        // 2. Si es profesor, poner en NULL el id_profesor en la tabla clases
        $stmt = $pdo->prepare("UPDATE clases SET id_profesor = NULL WHERE id_profesor = :id_usuario");
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->execute();
        
        // 3. Eliminar al usuario de la tabla 'usuarios'
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id_usuario = :id_usuario");
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->execute();
        
        // Confirmar la transacción
        $pdo->commit();
        
        echo "<script>alert('Usuario eliminado correctamente.'); window.location.href = 'gestion_usuario.php';</script>";
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error al eliminar el usuario: " . $e->getMessage();
        exit();
    }
} else {
    echo "No se ha especificado un usuario a eliminar.";
}
?>
