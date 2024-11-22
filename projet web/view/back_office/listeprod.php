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

// Supprimer un produit
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM produits WHERE id = :id");
        $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
        $stmt->execute();
        $successMessage = "Produit supprimé avec succès!";
    } catch (PDOException $e) {
        $errorMessage = "Erreur lors de la suppression du produit.";
    }
}

// Ajouter un produit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $nom = $_POST['nom'] ?? '';
    $prix = $_POST['prix'] ?? 0;
    $quantite = $_POST['quantite'] ?? 0;
    $categorie = $_POST['categorie'] ?? '';

    if (empty($nom) || $prix <= 0 || $quantite <= 0 || empty($categorie)) {
        $errorMessage = "Tous les champs sont requis et doivent être valides.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO produits (nom, prix, quantite, categorie) VALUES (:nom, :prix, :quantite, :categorie)");
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':prix', $prix);
            $stmt->bindParam(':quantite', $quantite);
            $stmt->bindParam(':categorie', $categorie);
            $stmt->execute();
            $successMessage = "Produit ajouté avec succès!";
        } catch (PDOException $e) {
            $errorMessage = "Erreur lors de l'ajout du produit.";
        }
    }
}

// Récupérer tous les produits
try {
    $sql = "SELECT * FROM produits";
    $stmt = $pdo->query($sql);
    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errorMessage = "Erreur lors de la récupération des produits.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Produits</title>
    <style>
        /* Style pour la page */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .actions a {
            text-decoration: none;
            padding: 5px 10px;
            margin: 0 5px;
            border-radius: 5px;
        }
        .actions a.edit {
            background-color: #4CAF50;
            color: white;
        }
        .actions a.delete {
            background-color: #f44336;
            color: white;
        }
        .no-data {
            text-align: center;
            color: #555;
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
        form {
            margin-top: 20px;
        }
        form input, form button {
            padding: 10px;
            margin-bottom: 10px;
            width: 100%;
            max-width: 400px;
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
    </style>
</head>
<body>

    <h1>Gestion des Produits</h1>

    <!-- Afficher les messages -->
    <?php if ($errorMessage): ?>
        <div class="error"><?= htmlspecialchars($errorMessage) ?></div>
    <?php endif; ?>
    <?php if ($successMessage): ?>
        <div class="success"><?= htmlspecialchars($successMessage) ?></div>
    <?php endif; ?>

    <!-- Liste des produits -->
    <h2>Liste des Produits</h2>
    <?php if (empty($produits)): ?>
        <div class="no-data">Aucun produit trouvé.</div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prix</th>
                    <th>Quantité</th>
                    <th>Catégorie</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produits as $produit): ?>
                    <tr>
                        <td><?= htmlspecialchars($produit['id']) ?></td>
                        <td><?= htmlspecialchars($produit['nom']) ?></td>
                        <td><?= htmlspecialchars($produit['prix']) ?> €</td>
                        <td><?= htmlspecialchars($produit['quantite']) ?></td>
                        <td><?= htmlspecialchars($produit['categorie']) ?></td>
                        <td class="actions">
                            <a class="edit" href="updateprod.php?id=<?= $produit['id'] ?>">Modifier</a>
                            <a class="delete" href="?action=delete&id=<?= $produit['id'] ?>" onclick="return confirm('Supprimer ce produit?');">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- Formulaire d'ajout -->
    <h2>Ajouter un Nouveau Produit</h2>
    <form method="POST" action="listeprod.php">
        <input type="text" name="nom" placeholder="Nom du produit" >
        <input type="number" name="prix" placeholder="Prix (€)" step="0.01" >
        <input type="number" name="quantite" placeholder="Quantité" >
        <input type="text" name="categorie" placeholder="Catégorie" >
        <button type="submit" name="ajouter">Ajouter</button>
    </form>

</body>
</html>
