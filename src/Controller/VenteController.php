<?php

namespace App\Controller;
use App\Entity\Produit;
use App\Entity\Vente;
use App\Entity\DemandeVente;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VenteController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/vente', name: 'app_vente')]
    public function index( ProduitRepository $produitRepository): Response

    {
        $produits =  $produitRepository->findAll();
        return $this->render('vente/vente.html.twig', [
            'produits' =>  $produits,
        ]);
    }

    #[Route('/vente/new', name: 'app_vente_processvente')]
    public function processVente(Request $request)
    {
        // Récupérer l'utilisateur connecté
        $utilisateur = $this->getUser();


        // Récupérer les données du formulaire
        $client = $request->request->get('client');
        $numero = $request->request->get('numero');
        $produits = $request->request->get('produits');
        $quantites = $request->request->get('quantites');

        // Créer une nouvelle vente
        $vente = new Vente();
        $vente->setClient((string)$client);
        $vente->setTelephone((string)$numero);
        $vente->setUtilisateur($utilisateur); // Enregistrer l'utilisateur

        // Calculer le montant total
        $montantTotal = 0;
        if (!empty($produits) && !empty($quantites)) {
        foreach ($produits as $index => $produitId) {
            $quantite = $quantites[$index];

            // Calculer le montant pour chaque produit en fonction de la quantité
            // Ici, je suppose que vous avez une méthode pour obtenir le prix du produit en fonction de son ID
            $prixProduit = $this->getDoctrine()->getRepository(Produit::class)->find($produitId)->getPrix();
            $montantProduit = $prixProduit * $quantite;

            $montantTotal += $montantProduit;
        }
        }
        $vente->setMontantTotal($montantTotal);

        // Enregistrer la vente en base de données
        $this->entityManager->persist($vente);
        $this->entityManager->flush();

        // Traiter les produits sélectionnés et leurs quantités
        if (!empty($produits) && !empty($quantites)) {
        foreach ($produits as $index => $produitId) {
            $quantite = $quantites[$index];

            // Créer une nouvelle demande de vente
            $demandeVente = new DemandeVente();
            $demandeVente->setProduit($produitId);
            $demandeVente->setQuantite($quantite);
            $demandeVente->setVente($vente);

            // Enregistrer la demande de vente en base de données
            $this->entityManager->persist($demandeVente);
        }
        }
        $this->entityManager->flush();


        // Rediriger l'utilisateur vers une autre page après l'enregistrement
        return $this->redirectToRoute('app_vente');
    }
}
