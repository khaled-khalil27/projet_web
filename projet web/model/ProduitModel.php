<?php
require_once 'Config.php';

class ProduitModel
{
    private $db;

    public function __construct()
    {
        $this->db = Config::getConnexion(); // Utilisation de Config pour la connexion
    }

    // Récupérer tous les produits
    public function getProduits()
    {
        $sql = "SELECT * FROM produits ORDER BY date_ajout DESC";
        $query = $this->db->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    // Ajouter un produit
    public function ajouterProduit($nom, $prix, $quantite, $categorie)
    {
        $sql = "INSERT INTO produits (nom, prix, quantite, categorie) VALUES (:nom, :prix, :quantite, :categorie)";
        $query = $this->db->prepare($sql);
        $query->execute([
            ':nom' => $nom,
            ':prix' => $prix,
            ':quantite' => $quantite,
            ':categorie' => $categorie,
        ]);
    }

    // Supprimer un produit
    public function supprimerProduit($id)
    {
        $sql = "DELETE FROM produits WHERE id = :id";
        $query = $this->db->prepare($sql);
        $query->execute([':id' => $id]);
    }

    // Modifier un produit
    public function modifierProduit($id, $nom, $prix, $quantite, $categorie)
    {
        $sql = "UPDATE produits SET nom = :nom, prix = :prix, quantite = :quantite, categorie = :categorie WHERE id = :id";
        $query = $this->db->prepare($sql);
        $query->execute([
            ':id' => $id,
            ':nom' => $nom,
            ':prix' => $prix,
            ':quantite' => $quantite,
            ':categorie' => $categorie,
        ]);
    }

    // Récupérer un produit par son ID
    public function getProduitById($id)
    {
        $sql = "SELECT * FROM produits WHERE id = :id";
        $query = $this->db->prepare($sql);
        $query->execute([':id' => $id]);
        return $query->fetch();
    }
}
?>
