<?php
session_start();
require_once 'config/database.php';

// Get existing departments
$stmt = $pdo->query("SELECT * FROM filieres ORDER BY nom_filiere");
$filieres = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Filière</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Ajouter une Filière</h1>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <form action="traitement_filiere.php" method="POST">
            <div class="form-group mb-3">
                <label for="nom_filiere">Nom de la filière:</label>
                <input type="text" id="nom_filiere" name="nom_filiere" required class="form-control">
            </div>

            <div class="form-group mb-3">
                <label for="description">Description:</label>
                <textarea id="description" name="description" class="form-control" rows="4"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Ajouter la filière</button>
            <a href="liste_etudiant.php" class="btn btn-secondary">Retour à la liste</a>
        </form>

        <h2 class="mt-5">Filières existantes</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($filieres as $filiere): ?>
                <tr>
                    <td><?php echo htmlspecialchars($filiere['nom_filiere']); ?></td>
                    <td><?php echo htmlspecialchars($filiere['description']); ?></td>
                    <td>
                        <a href="modifier_filiere.php?id=<?php echo $filiere['id_filiere']; ?>" 
                           class="btn btn-warning btn-sm">Modifier</a>
                        <a href="supprimer_filiere.php?id=<?php echo $filiere['id_filiere']; ?>" 
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette filière ?')">
                            Supprimer
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 