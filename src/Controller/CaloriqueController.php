<?php

namespace App\Controller;

use App\Entity\Calorique;
use App\Entity\Imc;
use App\Form\CaloriqueType;
use App\Repository\ImcRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CaloriqueController extends AbstractController
{
    #[Route('/calorique/calculate', name: 'calculate_calorique', methods: ['GET', 'POST'])]
    public function calculateCalorique(Request $request ,ImcRepository $repo): Response
    {
      
    
        // Récupérer la dernière entité Imc pour l'utilisateur
        $latestImc = $repo->findLatestImc();
    
        // Si l'utilisateur a au moins un enregistrement Imc
        if ($latestImc !== null) {
            // Effectuer le calcul des besoins caloriques
            $besoinsCaloriques = $this->calculateCaloricNeeds($latestImc, $request->request->all());
    
            // Créer une nouvelle entité Calorique
            $calorique = new Calorique();
            $calorique->setBesoinsCaloriques($besoinsCaloriques);
    
            // Utiliser le formulaire CaloriqueType pour créer le formulaire
            $form = $this->createForm(CaloriqueType::class, $calorique);
    
            // Gérer la soumission du formulaire
            $form->handleRequest($request);
    
            // Vérifier si le formulaire est soumis et valide
            if ($form->isSubmitted() && $form->isValid()) {
                // Associer l'entité Imc à l'entité Calorique
                $calorique->setImcs($latestImc);
    
                // Ajouter les champs d'activité et d'objectif à l'entité Calorique
                $calorique->setActivite($request->request->get('calorique')['activite']);
                $calorique->setObjectif($request->request->get('calorique')['objectif']);
    
                // Persister et flush l'entité Calorique
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($calorique);
                $entityManager->flush();
    
                return $this->redirectToRoute('app_calorique');
            }
    
            // Afficher le formulaire
            return $this->render('calorique/Calorique.html.twig', [
                'form' => $form->createView(),
            ]);
        }
    
        // Gérer le cas où il n'y a pas d'enregistrement Imc pour l'utilisateur.
        // Vous pourriez rediriger vers une page où l'utilisateur peut saisir ses données Imc d'abord
        // ou afficher un message d'erreur.
        // ...
    
        // Déplacer la déclaration du retour ici
        return new Response('Erreur: Aucun enregistrement Imc trouvé pour l\'utilisateur.');
    }
    

    private function calculateCaloricNeeds(Imc $imc, array $formData): float
    {
        // Extraire les données de l'entité Imc
        $poids = $imc->getPoids();
        $taille = $imc->getTaille();
        $sexe = $imc->getSexe();
        $age = $imc->getAge();

        // Calculer le taux métabolique de base (BMR)
        $besoinsCaloriques = ($sexe === 'homme') ?
            88.362 + (13.397 * $poids) + (4.799 * $taille) - (5.677 * $age) :
            447.593 + (9.247 * $poids) + (3.098 * $taille) - (4.330 * $age);

        // Extraire les données du formulaire
        $activite = $formData['activite'] ?? 'sedentary';
        $objectif = $formData['objectif'] ?? 'maintain';

        // Définir les facteurs
        $facteursActivite = [
            'sedentary' => 1.0,
            'lightlyActive' => 1.2,
            'moderatelyActive' => 1.5,
            'veryActive' => 1.8,
        ];

        $facteursObjectif = [
            'loseWeight' => 1.1,
            'maintain' => 1.0,
            'buildMuscle' => 1.4,
        ];

        // Calculer les besoins caloriques en fonction des facteurs
        $besoinsCaloriques = $besoinsCaloriques * $facteursActivite[$activite] * $facteursObjectif[$objectif];

        return abs($besoinsCaloriques);
    }
}
