<?php
require_once(__DIR__.'/../config.php'); // Connexion à la base de données

$errorMessage = "";
$successMessage = "";

// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=localhost;dbname=projetweb", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Vérifier si un ID est passé dans l'URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // Récupérer les informations du produit à modifier
        $stmt = $pdo->prepare("SELECT * FROM produits WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $produit = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $errorMessage = "Erreur lors de la récupération du produit.";
    }
}

// Mettre à jour le produit si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier'])) {
    $nom = $_POST['nom'] ?? '';
    $prix = $_POST['prix'] ?? 0;
    $quantite = $_POST['quantite'] ?? 0;
    $categorie = $_POST['categorie'] ?? '';

    if (empty($nom) || $prix <= 0 || $quantite <= 0 || empty($categorie)) {
        $errorMessage = "Tous les champs sont requis et doivent être valides.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE produits SET nom = :nom, prix = :prix, quantite = :quantite, categorie = :categorie WHERE id = :id");
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':prix', $prix);
            $stmt->bindParam(':quantite', $quantite);
            $stmt->bindParam(':categorie', $categorie);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $successMessage = "Produit mis à jour avec succès!";
        } catch (PDOException $e) {
            $errorMessage = "Erreur lors de la mise à jour du produit.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le Produit</title>
    <style>
        /* Votre style CSS ici */
    </style>
</head>
<body>

    <h1>Modifier le Produit</h1>

    <!-- Afficher les messages -->
    <?php if ($errorMessage): ?>
        <div class="error"><?= htmlspecialchars($errorMessage) ?></div>
    <?php endif; ?>
    <?php if ($successMessage): ?>
        <div class="success"><?= htmlspecialchars($successMessage) ?></div>
    <?php endif; ?>

    <!-- Formulaire de modification -->
    <?php if ($produit): ?>
        <form method="POST" action="updateprod.php?id=<?= $produit['id'] ?>">
            <input type="text" name="nom" value="<?= htmlspecialchars($produit['nom']) ?>" >
            <input type="number" name="prix" value="<?= htmlspecialchars($produit['prix']) ?>" step="0.01" >
            <input type="number" name="quantite" value="<?= htmlspecialchars($produit['quantite']) ?>" >
            <input type="text" name="categorie" value="<?= htmlspecialchars($produit['categorie']) ?>" >
            <button type="submit" name="modifier">Modifier</button>
        </form>
    <?php else: ?>
        <p>Produit non trouvé.</p>
    <?php endif; ?>

</body>
</html>
