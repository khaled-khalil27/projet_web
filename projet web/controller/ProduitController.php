<?php
require_once 'ProduitModel.php';

class ProduitController
{
    private $model;

    public function __construct()
    {
        $this->model = new ProduitModel();
    }

    // Afficher tous les produits
    public function afficherProduits()
    {
        return $this->model->getProduits();
    }

    // Ajouter un produit
    public function ajouterProduit($nom, $prix, $quantite, $categorie)
    {
        $this->model->ajouterProduit($nom, $prix, $quantite, $categorie);
    }

    // Supprimer un produit
    public function supprimerProduit($id)
    {
        $this->model->supprimerProduit($id);
    }

    // Modifier un produit
    public function modifierProduit($id, $nom, $prix, $quantite, $categorie)
    {
        $this->model->modifierProduit($id, $nom, $prix, $quantite, $categorie);
    }

    // Afficher un produit spécifique
    public function afficherProduit($id)
    {
        return $this->model->getProduitById($id);
    }
}
?>
