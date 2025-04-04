<?php
require_once 'config/database.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: liste_etudiant.php');
    exit();
}

$id = intval($_GET['id']);

// Récupérer les filières
$filieres = $pdo->query('SELECT id_filiere, nom_filiere FROM filieres')->fetchAll();

// Récupérer les données de l'étudiant
$stmt = $pdo->prepare('SELECT * FROM etudiants WHERE id = ?');
$stmt->execute([$id]);
$etudiant = $stmt->fetch();

if (!$etudiant) {
    header('Location: liste_etudiant.php');
    exit();
}

$message = '';

if (isset($_POST['modifier'])) {
    if (!empty($_POST['nom']) && !empty($_POST['prenoms']) && !empty($_POST['email'])) {
        // Vérifier si l'email existe déjà pour un autre étudiant
        $check = $pdo->prepare('SELECT COUNT(*) FROM etudiants WHERE email = ? AND id != ?');
        $check->execute([$_POST['email'], $id]);
        
        if ($check->fetchColumn() > 0) {
            $message = '<div class="alert alert-danger">Cet email existe déjà!</div>';
        } else {
            $sql = 'UPDATE etudiants SET 
                    nom = :nom,
                    prenoms = :prenoms,
                    sexe = :sexe,
                    email = :email,
                    contact = :contact,
                    quartier = :quartier,
                    presentation = :presentation,
                    id_filiere = :id_filiere
                    WHERE id = :id';
            
            try {
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    'nom' => htmlspecialchars($_POST['nom']),
                    'prenoms' => htmlspecialchars($_POST['prenoms']),
                    'sexe' => $_POST['sexe'],
                    'email' => htmlspecialchars($_POST['email']),
                    'contact' => htmlspecialchars($_POST['contact']),
                    'quartier' => htmlspecialchars($_POST['quartier']),
                    'presentation' => htmlspecialchars($_POST['presentation']),
                    'id_filiere' => $_POST['id_filiere'],
                    'id' => $id
                ]);
                
                $message = '<div class="alert alert-success">Les modifications ont été enregistrées avec succès!</div>';
                
                // Mettre à jour les données affichées
                $stmt = $pdo->prepare('SELECT * FROM etudiants WHERE id = ?');
                $stmt->execute([$id]);
                $etudiant = $stmt->fetch();
                
            } catch(PDOException $e) {
                $message = '<div class="alert alert-danger">Erreur: ' . $e->getMessage() . '</div>';
            }
        }
    } else {
        $message = '<div class="alert alert-danger">Veuillez remplir tous les champs obligatoires!</div>';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Modifier un étudiant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Modifier l'étudiant</h2>
        <?php echo $message; ?>
        
        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Nom</label>
                <input type="text" name="nom" class="form-control" required 
                       value="<?php echo htmlspecialchars($etudiant['nom']); ?>">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Prénoms</label>
                <input type="text" name="prenoms" class="form-control" required 
                       value="<?php echo htmlspecialchars($etudiant['prenoms']); ?>">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Sexe</label>
                <select name="sexe" class="form-control" required>
                    <option value="M" <?php echo $etudiant['sexe'] === 'M' ? 'selected' : ''; ?>>Masculin</option>
                    <option value="F" <?php echo $etudiant['sexe'] === 'F' ? 'selected' : ''; ?>>Féminin</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required 
                       value="<?php echo htmlspecialchars($etudiant['email']); ?>">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Contact</label>
                <input type="text" name="contact" class="form-control" 
                       value="<?php echo htmlspecialchars($etudiant['contact']); ?>">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Quartier</label>
                <input type="text" name="quartier" class="form-control" 
                       value="<?php echo htmlspecialchars($etudiant['quartier']); ?>">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Présentation</label>
                <textarea name="presentation" class="form-control"><?php echo htmlspecialchars($etudiant['presentation']); ?></textarea>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Filière</label>
                <select name="id_filiere" class="form-control" required>
                    <?php foreach($filieres as $filiere): ?>
                        <option value="<?php echo $filiere['id_filiere']; ?>" 
                                <?php echo $etudiant['id_filiere'] == $filiere['id_filiere'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($filiere['nom_filiere']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit" name="modifier" class="btn btn-primary">Enregistrer les modifications</button>
            <a href="liste_etudiant.php" class="btn btn-secondary">Retour à la liste</a>
        </form>
    </div>
</body>
</html> 