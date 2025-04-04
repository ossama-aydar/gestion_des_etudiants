<?php
require_once 'config/database.php';

$message = '';

// Get filieres for dropdown
$filieres = $pdo->query('SELECT id_filiere, nom_filiere FROM filieres')->fetchAll();


if(isset($_POST["valider"])) {
    if(!empty($_POST["nom"]) && !empty($_POST["prenoms"]) && !empty($_POST["email"]) && !empty($_POST["password"])) {
        // Check if email already exists
        $check = $pdo->prepare('SELECT COUNT(*) FROM etudiants WHERE email = ?');
        $check->execute([$_POST["email"]]);
        
        if($check->fetchColumn() > 0) {
            $message = '<div class="alert alert-danger">Cet email existe déjà!</div>';
        } else {
            $sql = 'INSERT INTO etudiants(nom, prenoms, sexe, email, password, contact, quartier, presentation, id_filiere) 
                    VALUES (:nom, :prenoms, :sexe, :email, :password, :contact, :quartier, :presentation, :id_filiere)';
            $stmt = $pdo->prepare($sql);
            
            try {
                $stmt->execute([
                    'nom' => htmlspecialchars($_POST["nom"]),
                    'prenoms' => htmlspecialchars($_POST["prenoms"]),
                    'sexe' => $_POST["sexe"],
                    'email' => htmlspecialchars($_POST["email"]),
                    'password' => password_hash($_POST["password"], PASSWORD_DEFAULT), // Using secure password hashing
                    'contact' => htmlspecialchars($_POST["contact"]),
                    'quartier' => htmlspecialchars($_POST["quartier"]),
                    'presentation' => htmlspecialchars($_POST["presentation"]),
                    'id_filiere' => $_POST["id_filiere"]
                ]);
                $message = '<div class="alert alert-success">Étudiant ajouté avec succès!</div>';
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
    <title>Ajouter un étudiant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5 mb-5">
        <h2>Ajouter un nouvel étudiant</h2>
        <?php echo $message; ?>
        
        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Nom</label>
                <input type="text" name="nom" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Prénoms</label>
                <input type="text" name="prenoms" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Sexe</label>
                <select name="sexe" class="form-control" required>
                    <option value="M">Masculin</option>
                    <option value="F">Féminin</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Mot de passe</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Contact</label>
                <input type="text" name="contact" class="form-control">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Quartier</label>
                <input type="text" name="quartier" class="form-control">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Présentation</label>
                <textarea name="presentation" class="form-control"></textarea>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Filière</label>
                <select name="id_filiere" class="form-control" required>
                    <?php foreach($filieres as $filiere): ?>
                        <option value="<?php echo $filiere['id_filiere']; ?>">
                            <?php echo htmlspecialchars($filiere['nom_filiere']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit" name="valider" class="btn btn-primary">Ajouter</button>
            <a href="liste_etudiant.php" class="btn btn-secondary">Retour à la liste</a>
        </form>
    </div>
</body>
</html> 