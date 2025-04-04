<?php
session_start();
require_once 'config/database.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "ID de l'étudiant non spécifié";
    header('Location: liste_etudiant.php');
    exit();
}

$id = intval($_GET['id']);

try {
    // Check if student exists
    $checkStmt = $pdo->prepare("SELECT photo FROM etudiants WHERE id = ?");
    $checkStmt->execute([$id]);
    $student = $checkStmt->fetch();

    if (!$student) {
        $_SESSION['error'] = "Étudiant non trouvé";
        header('Location: liste_etudiant.php');
        exit();
    }

    // Delete student's photo if it exists
    if (!empty($student['photo']) && file_exists('uploads/' . $student['photo'])) {
        unlink('uploads/' . $student['photo']);
    }

    // Delete the student from database
    $deleteStmt = $pdo->prepare("DELETE FROM etudiants WHERE id = ?");
    $deleteStmt->execute([$id]);

    $_SESSION['success'] = "L'étudiant a été supprimé avec succès";

} catch (PDOException $e) {
    $_SESSION['error'] = "Erreur lors de la suppression : " . $e->getMessage();
}

header('Location: liste_etudiant.php');
exit();
?> 