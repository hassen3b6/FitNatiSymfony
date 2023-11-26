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
      
    
        $latestImc = $repo->findLatestImc();
    
        
        if ($latestImc !== null) {
           
            $besoinsCaloriques = $this->calculateCaloricNeeds($latestImc, $request->request->all());
    
           
            $calorique = new Calorique();
            $calorique->setBesoinsCaloriques($besoinsCaloriques);
    
           
            $form = $this->createForm(CaloriqueType::class, $calorique);
    
    
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                
                $calorique->setImcs($latestImc);
    
      
                $calorique->setActivite($request->request->get('calorique')['activite']);
                $calorique->setObjectif($request->request->get('calorique')['objectif']);
    
            
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($calorique);
                $entityManager->flush();
    
                return $this->redirectToRoute('app_calorique');
            }
    
   
            return $this->render('calorique/Calorique.html.twig', [
                'form' => $form->createView(),
            ]);
        }
    
 
        return new Response('Erreur: Aucun enregistrement Imc trouvé pour l\'utilisateur.');
    }
    

    private function calculateCaloricNeeds(Imc $imc, array $formData): float
    {
 
        $poids = $imc->getPoids();
        $taille = $imc->getTaille();
        $sexe = $imc->getSexe();
        $age = $imc->getAge();

     
        $besoinsCaloriques = ($sexe === 'homme') ?
            88.362 + (13.397 * $poids) + (4.799 * $taille) - (5.677 * $age) :
            447.593 + (9.247 * $poids) + (3.098 * $taille) - (4.330 * $age);

  
        $activite = $formData['activite'] ?? 'sedentary';
        $objectif = $formData['objectif'] ?? 'maintain';

    
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

        $besoinsCaloriques = $besoinsCaloriques * $facteursActivite[$activite] * $facteursObjectif[$objectif];

        return abs($besoinsCaloriques);
    }
}
