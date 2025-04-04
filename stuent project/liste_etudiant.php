<?php
require_once 'config/database.php';

// Récupérer la liste des étudiants avec leurs filières
$sql = 'SELECT e.*, f.nom_filiere 
        FROM etudiants e 
        LEFT JOIN filieres f ON e.id_filiere = f.id_filiere';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$etudiants = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Liste des étudiants</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Liste des étudiants</h2>
        <a href="ajout_etudiant.php" class="btn btn-primary mb-3">Ajouter un étudiant</a>
        
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénoms</th>
                    <th>Email</th>
                    <th>Filière</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($etudiants as $etudiant): ?>
                    <tr>
                        <td><?php echo $etudiant['id']; ?></td>
                        <td><?php echo htmlspecialchars($etudiant['nom']); ?></td>
                        <td><?php echo htmlspecialchars($etudiant['prenoms']); ?></td>
                        <td><?php echo htmlspecialchars($etudiant['email']); ?></td>
                        <td><?php echo htmlspecialchars($etudiant['nom_filiere']); ?></td>
                        <td>
                            <a href="details_etudiant.php?id=<?php echo $etudiant['id']; ?>" 
                               class="btn btn-info btn-sm">Voir plus</a>
                            <a href="modifier_etudiant.php?id=<?php echo $etudiant['id']; ?>" 
                               class="btn btn-warning btn-sm">Modifier</a>
                            <a href="supprimer_etudiant.php?id=<?php echo $etudiant['id']; ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet étudiant?')">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html> 