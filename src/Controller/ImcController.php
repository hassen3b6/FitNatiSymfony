<?php

namespace App\Controller;
// ImcController.php

// ImcController.php

use App\Entity\Imc;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;


use App\Form\ImcType;
use App\Repository\ImcRepository;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class ImcController extends AbstractController
{
    #[Route('/imc', name: 'app_imc')]
    public function index(): Response
    {
        return $this->render('imc/index.html.twig', [
            'controller_name' => 'ImcController',
        ]);
    }

    #[Route('/Fiche', name: 'ficheDB')]
    public function listFiche(ImcRepository $imcRepository): Response
    {
        return $this->render('imc/listFiche.html.twig', [
            'imc' => $imcRepository->findAll(),
        ]);
    }

  // ... (votre code existant)

#[Route('/addFiche', name: 'add_Fiche')]
public function addImc(ManagerRegistry $manager, Request $request): Response
{
    $em = $manager->getManager();

    $imc = new Imc();

    $form = $this->createForm(ImcType::class, $imc);
    $form->handleRequest($request);

    $imcResult = null;

    if ($form->isSubmitted() && $form->isValid()) {
        // Calculate IMC
        $imc->setIMC($this->calculerImc($imc->getPoids(), $imc->getTaille()));

        // Determine IMC category
        $imc->setCategorieIMC($this->determinerCategorieImc($imc->getIMC()));

        // Calculate and set ideal weight based on sex
        $poidsIdeal = $this->calculerPoidsIdeal($imc->getTaille(), $imc->getSexe());
        $imc->setPoidsIdeal($poidsIdeal);

        $em->persist($imc);
        $em->flush();

        // Get the results for display
        $imcResult = [
            'imc' => $imc->getIMC(),
            'categorie' => $imc->getCategorieIMC(),
            'poidsIdeal' => $imc->getPoidsIdeal(),
        ];
    }

    return $this->render('imc/ImcFiche.html.twig', ['form' => $form->createView(), 'imcResult' => $imcResult]);
}

    #[Route('/Fiche/edit/{id}', name: 'Fiche_edit')]
    public function editFiche(Request $request, ManagerRegistry $manager, $id, ImcRepository $imcRepository): Response
    {
        $em = $manager->getManager();

        $imc = $imcRepository->find($id);

        if (!$imc) {
            throw $this->createNotFoundException('Fiche non trouvée');
        }

        $form = $this->createForm(ImcType::class, $imc);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Calculate IMC
            $imc->setIMC($this->calculerImc($imc->getPoids(), $imc->getTaille()));

            // Determine IMC category
            $imc->setCategorieIMC($this->determinerCategorieImc($imc->getIMC()));

              // Calculate and set ideal weight based on sex
              $poidsIdeal = $this->calculerPoidsIdeal($imc->getTaille(), $imc->getSexe());
              $imc->setPoidsIdeal($poidsIdeal); 


            $em->flush();


            return $this->redirectToRoute('ficheDB');
        }

        return $this->render('imc/editFiche.html.twig', [
            'imc' => $imc,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/Fiche/delete/{id}', name: 'Fiche_delete')]
    public function deleteFiche($id, ManagerRegistry $manager, ImcRepository $imcRepository): Response
    {
        $em = $manager->getManager();
        $imc = $imcRepository->find($id);

        if (!$imc) {
            throw $this->createNotFoundException('Fiche non trouvée');
        }

        $em->remove($imc);
        $em->flush();

        return $this->redirectToRoute('ficheDB');
    }

    #[Route('/calculateImc/{id}', name: 'calculate_imc')]
    public function calculateImc($id, ImcRepository $imcRepository): JsonResponse
    {
        // Retrieve the IMC entity from the repository
        $imc = $imcRepository->find($id);
    
        // Check if the entity with the given ID exists
        if (!$imc) {
            throw $this->createNotFoundException('Fiche non trouvée');
        }
    
        // Retrieve the weight, height, and sex from the entity
        $poids = $imc->getPoids();
        $taille = $imc->getTaille();
        $sexe = $imc->getSexe();
    
        // Calculate IMC
        $IMC = $this->calculerImc($poids, $taille);
    
        // Determine IMC category
        $categorieIMC = $this->determinerCategorieImc($IMC);
    
        // Calculate ideal weight based on sex
        $poidsIdeal = $this->calculerPoidsIdeal($taille, $sexe);
    
        // Update the IMC, category, and ideal weight properties in the $imc entity
        $imc->setIMC($IMC);
        $imc->setCategorieIMC($categorieIMC);
        $imc->setPoidsIdeal($poidsIdeal);
    
        // Persist changes to the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($imc);
        $entityManager->flush();
    
        // Return IMC, category, and ideal weight in JSON format
        return new JsonResponse(['imc' => $IMC, 'categorie' => $categorieIMC, 'poidsIdeal' => $poidsIdeal]);
    }
    

    // ... (your existing code)

    // Auxiliary method for IMC calculation
    private function calculerImc($poids, $taille): float
    {
        // Vérifier que la taille est en mètres
        if ($taille <= 0) {
            // Gérer le cas où la taille est invalide
            return 0.0;
        }
        
        // Convertir la taille en mètres si elle est fournie en centimètres
        if ($taille > 3) {
            $taille = $taille / 100; // Conversion de centimètres en mètres
        }
    
        // Calculer l'IMC en utilisant la formule : poids / (taille * taille)
        $imc = $poids / ($taille * $taille);
        
        return round($imc,2);
    }
    

    // Auxiliary method to determine IMC category
    private function determinerCategorieImc($IMC): string
    {
        // Define ranges for IMC categories (adjust as needed)
        if ($IMC < 18.5) {
            return 'Sous-poids';
        } elseif ($IMC >= 18.5 && $IMC < 24.9) {
            return 'Poids normal';
        } elseif ($IMC >= 25.0 && $IMC < 29.9) {
            return 'Surpoids';
        } else {
            return 'Obésité';
        }

        
    }// Auxiliary method for calculating ideal weight
    private function calculerPoidsIdeal($taille, $sexe): float
    {
        // Calculate ideal weight based on sex
        if (strcasecmp($sexe, "Male") === 0) {
            $poidsIdeal = (0.75 * $taille) - 62.5;
        } elseif (strcasecmp($sexe, "female") === 0) {
            $poidsIdeal = (0.675 * $taille) - 56.25;
        } else {
            // Si le sexe n'est ni "homme" ni "femme", lancez une exception
            throw new InvalidArgumentException("Le sexe doit être soit 'homme' soit 'femme'");
        }
    
        // Assurez-vous que $poidsIdeal est toujours supérieur ou égal à zéro
        $poidsIdeal = max(0, $poidsIdeal);
    
        return round($poidsIdeal, 2);
  
    }
    private function validateUserData( $sexe, $poids, $taille, ValidatorInterface $validator)
{
    $errors = [];



    // Validate sexe
    if (!in_array($sexe, ['Male', 'female'])) {
        $errors[] = 'Le sexe doit être soit "Male" soit "female".';
    }

    // Validate poids
    $poidsConstraint = new Assert\Range(['min' => 1, 'max' => 500]);
    $poidsErrors = $validator->validate($poids, $poidsConstraint);
    if (count($poidsErrors) > 0) {
        $errors[] = 'Le poids doit être compris entre 1 et 500.';
    }

    // Validate taille
    $tailleConstraint = new Assert\Range(['min' => 50, 'max' => 300]);
    $tailleErrors = $validator->validate($taille, $tailleConstraint);
    if (count($tailleErrors) > 0) {
        $errors[] = 'La taille doit être comprise entre 50 et 300.';
    }

    return $errors;
}

    }
    
    

    
    
    