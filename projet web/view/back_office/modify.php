<?php
// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=localhost;dbname=projetweb", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Initialiser les messages
$errorMessage = "";
$successMessage = "";

// Récupérer l'ID du produit à modifier
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Récupérer les données actuelles du produit
    try {
        $stmt = $pdo->prepare("SELECT * FROM produits WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $produit = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$produit) {
            die("Produit non trouvé.");
        }
    } catch (PDOException $e) {
        die("Erreur lors de la récupération des données : " . $e->getMessage());
    }
} else {
    die("ID de produit invalide.");
}

// Mettre à jour les données du produit si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
            header("Location: listeprod.php"); // Redirection après mise à jour
            exit();
        } catch (PDOException $e) {
            $errorMessage = "Erreur lors de la mise à jour des données.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Produit</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .form-container {
            max-width: 400px;
            margin: auto;
        }
        form input, form button {
            padding: 10px;
            margin-bottom: 10px;
            width: 100%;
        }
        form button {
            background-color: #5cb85c;
            color: white;
            border: none;
            cursor: pointer;
        }
        form button:hover {
            background-color: #4cae4c;
        }
        .error, .success {
            text-align: center;
            padding: 10px;
            margin-bottom: 20px;
            color: white;
        }
        .error {
            background-color: #f44336;
        }
        .success {
            background-color: #4CAF50;
        }
    </style>
</head>
<body>
    <h1>Modifier Produit</h1>

    <!-- Messages d'erreur ou de succès -->
    <?php if ($errorMessage): ?>
        <div class="error"><?= htmlspecialchars($errorMessage) ?></div>
    <?php endif; ?>
    <?php if ($successMessage): ?>
        <div class="success"><?= htmlspecialchars($successMessage) ?></div>
    <?php endif; ?>

    <!-- Formulaire de modification -->
    <div class="form-container">
        <form action="" method="POST">
            <input type="text" name="nom" value="<?= htmlspecialchars($produit['nom']) ?>" placeholder="Nom du produit" required>
            <input type="number" name="prix" value="<?= htmlspecialchars($produit['prix']) ?>" placeholder="Prix (€)" step="0.01" required>
            <input type="number" name="quantite" value="<?= htmlspecialchars($produit['quantite']) ?>" placeholder="Quantité" required>
            <input type="text" name="categorie" value="<?= htmlspecialchars($produit['categorie']) ?>" placeholder="Catégorie" required>
            <button type="submit">Enregistrer</button>
        </form>
    </div>
</body>
</html>
