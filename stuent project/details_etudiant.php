<?php
require_once 'config/database.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: liste_etudiant.php');
    exit();
}

$id = intval($_GET['id']);

// Récupérer les détails de l'étudiant
$sql = 'SELECT e.*, f.nom_filiere 
        FROM etudiants e 
        LEFT JOIN filieres f ON e.id_filiere = f.id_filiere 
        WHERE e.id = ?';
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$etudiant = $stmt->fetch();

if (!$etudiant) {
    header('Location: liste_etudiant.php');
    exit();
}

$message = '';

// Handle photo upload
if(isset($_POST['upload']) && isset($_FILES['photo'])) {
    $target_dir = "uploads/";
    if(!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $target_file = $target_dir . basename($_FILES["photo"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    
    // Check if image file is a actual image or fake image
    if(getimagesize($_FILES["photo"]["tmp_name"]) !== false) {
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            // Update database with photo filename
            $sql = "UPDATE etudiants SET photo = :photo WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'photo' => basename($_FILES["photo"]["name"]),
                'id' => $id
            ]);
            $message = '<div class="alert alert-success">La photo a été uploadée avec succès.</div>';
            // Refresh student data
            header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $id);
            exit();
        } else {
            $message = '<div class="alert alert-danger">Désolé, une erreur s\'est produite lors de l\'upload.</div>';
        }
    } else {
        $message = '<div class="alert alert-danger">Le fichier n\'est pas une image.</div>';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Détails de l'étudiant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Détails de l'étudiant</h2>
        <?php echo $message; ?>
        
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($etudiant['nom'] . ' ' . $etudiant['prenoms']); ?></h5>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($etudiant['email']); ?></p>
                        <p><strong>Sexe:</strong> <?php echo $etudiant['sexe'] === 'M' ? 'Masculin' : 'Féminin'; ?></p>
                        <p><strong>Contact:</strong> <?php echo htmlspecialchars($etudiant['contact']); ?></p>
                        <p><strong>Quartier:</strong> <?php echo htmlspecialchars($etudiant['quartier']); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Filière:</strong> <?php echo htmlspecialchars($etudiant['nom_filiere']); ?></p>
                        <?php if(!empty($etudiant['photo'])): ?>
                            <p><strong>Photo:</strong></p>
                            <img src="uploads/<?php echo htmlspecialchars($etudiant['photo']); ?>" 
                                 class="img-thumbnail" 
                                 alt="Photo de l'étudiant"
                                 style="max-width: 200px;">
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mb-3">
                    <h6>Présentation:</h6>
                    <p><?php echo nl2br(htmlspecialchars($etudiant['presentation'])); ?></p>
                </div>

                <div class="mt-3">
                    <a href="modifier_etudiant.php?id=<?php echo $etudiant['id']; ?>" 
                       class="btn btn-warning">Modifier</a>
                    <a href="liste_etudiant.php" class="btn btn-secondary">Retour à la liste</a>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <h3>Ajouter une photo</h3>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Sélectionner une photo</label>
                    <input type="file" name="photo" class="form-control" required>
                </div>
                <button type="submit" name="upload" class="btn btn-primary">Upload</button>
            </form>
        </div>
    </div>
</body>
</html> 