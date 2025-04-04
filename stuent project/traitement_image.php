<?php
require_once 'config/database.php';

function uploadImage($file, $id_etudiant) {
    $target_dir = "uploads/";
    $message = '';
    
    // Create uploads directory if it doesn't exist
    if(!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    // Get file extension
    $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    
    // Generate unique filename
    $filename = uniqid() . '.' . $imageFileType;
    $target_file = $target_dir . $filename;
    
    // Check if image file is actual image
    if(!getimagesize($file["tmp_name"])) {
        return ["success" => false, "message" => "Le fichier n'est pas une image."];
    }
    
    // Check file size (limit to 5MB)
    if($file["size"] > 5000000) {
        return ["success" => false, "message" => "Le fichier est trop volumineux."];
    }
    
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        return ["success" => false, "message" => "Seuls les fichiers JPG, JPEG, PNG & GIF sont autorisés."];
    }
    
    // Delete old photo if exists
    $stmt = $db->prepare("SELECT photo FROM etudiants WHERE id = ?");
    $stmt->execute([$id_etudiant]);
    $old_photo = $stmt->fetchColumn();
    
    if($old_photo && file_exists($target_dir . $old_photo)) {
        unlink($target_dir . $old_photo);
    }
    
    // Upload new file
    if(move_uploaded_file($file["tmp_name"], $target_file)) {
        // Update database
        $stmt = $db->prepare("UPDATE etudiants SET photo = ? WHERE id = ?");
        if($stmt->execute([$filename, $id_etudiant])) {
            return ["success" => true, "message" => "La photo a été uploadée avec succès.", "filename" => $filename];
        } else {
            unlink($target_file); // Delete uploaded file if database update fails
            return ["success" => false, "message" => "Erreur lors de la mise à jour de la base de données."];
        }
    } else {
        return ["success" => false, "message" => "Erreur lors de l'upload du fichier."];
    }
}

// Usage example (if called directly)
if(isset($_FILES['photo']) && isset($_POST['id_etudiant'])) {
    $result = uploadImage($_FILES['photo'], $_POST['id_etudiant']);
    echo json_encode($result);
    exit();
}
?> 