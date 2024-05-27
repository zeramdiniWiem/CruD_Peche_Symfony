<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use App\Entity\Categories;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CategoriesController extends AbstractController
{
    #[Route('/categories', name: 'categories')]
    public function index(): Response
    {
        //categories from database
        $em=$this->getDoctrine()->getManager();
        $repo = $em->getRepository(Categories::class);
        $categories = $repo->findAll();

        return $this->render('categories/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/categories/ajouter', name: 'ajouter_categories')]
    public function ajouter(Request $request): Response
    {
        $categorie = new Categories();
        $formBuilder=$this->createFormBuilder($categorie)
        ->add('NomCategorie', TextType::class, ['attr' => ['class' => 'form-control']])
        ->add('Valider', SubmitType::class, ['attr' => ['class' => 'form-control btn btn-success mt-3', 'style' => 'float:left; width: 45%']]);

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($formData);
            $entityManager->flush();
            return $this->redirectToRoute('categories');
        }


        return $this->render('categories/ajouter.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/categories/delete/{id}', name: 'supprimer_categories')]
    public function supprimer(Request $request, $id): Response
    {   
        $entityManager = $this->getDoctrine()->getManager();
        $categorie = $entityManager->getRepository(Categories::class)->find($id);

        $relatedArticles = $categorie->getArticles();
        foreach ($relatedArticles as $article) {
            $categorie->removeArticle($article);
        }

        $entityManager->remove($categorie);
        $entityManager->flush();

        return $this->redirectToRoute('categories');

    }
}
