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

class ArticlesController extends AbstractController
{
    #[Route('/articles', name: 'articles')]
    public function index(): Response
    {
        //article from database
        $em=$this->getDoctrine()->getManager();
        $repo = $em->getRepository(Article::class);
        $articles = $repo->findAll();

        return $this->render('articles/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/articles/ajouter', name: 'ajouter_articles')]
    public function ajouter(Request $request): Response
    {
        $article = new Article();
        $formBuilder=$this->createFormBuilder($article)
        ->add('Libelle', TextType::class, ['attr' => ['class' => 'form-control']])
        ->add('Price', NumberType::class, ['attr' => ['class' => 'form-control']])
        ->add('Marque', TextType::class, ['attr' => ['class' => 'form-control']])
        ->add('Description', TextareaType::class, ['attr' => ['class' => 'form-control']])
        ->add('is_disponible', ChoiceType::class, [
            'attr' => ['class' => 'form-control'],
            'choices' => [
                'oui' => true,
                'non' => false,
            ]
        ])
        ->add('Categorie', EntityType::class, [
            'class' => Categories::class,
            'attr' => ['class' => 'form-control'],
            'choice_label' => 'nomCategorie'
        ])
        ->add('Valider', SubmitType::class, ['attr' => ['class' => 'form-control btn btn-success mt-3', 'style' => 'float:left; width: 45%']]);

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($formData);
            $entityManager->flush();
            return $this->redirectToRoute('articles');
        }


        return $this->render('articles/ajouter.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/articles/modifier/{id}', name: 'modifier_articles')]
    public function modifier(Request $request, $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $article = $entityManager->getRepository(Article::class)->find($id);

        $formBuilder = $this->createFormBuilder($article)
            ->add('Libelle', TextType::class, ['attr' => ['class' => 'form-control']])
            ->add('Price', NumberType::class, ['attr' => ['class' => 'form-control']])
            ->add('Marque', TextType::class, ['attr' => ['class' => 'form-control']])
            ->add('Description', TextareaType::class, ['attr' => ['class' => 'form-control']])
            ->add('is_disponible', ChoiceType::class, [
                'attr' => ['class' => 'form-control'],
                'choices' => [
                    'oui' => true,
                    'non' => false,
                ]
            ])
            ->add('Categorie', EntityType::class, [
                'class' => Categories::class,
                'attr' => ['class' => 'form-control'],
                'choice_label' => 'nomCategorie'
            ])
            ->add('Valider', SubmitType::class, ['attr' => ['class' => 'form-control btn btn-success mt-3', 'style' => 'float:left; width: 45%']]);

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('articles');
        }

        return $this->render('articles/modifier.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/articles/{id}', name: 'afficher_article')]
    public function afficher(Request $request, $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $article = $entityManager->getRepository(Article::class)->find($id);

        return $this->render('articles/afficher.html.twig', [
            'article' => $article,
        ]);
    }


    #[Route('/articles/delete/{id}', name: 'supprimer_articles')]
    public function supprimer(Request $request, $id): Response
    {   
        $entityManager = $this->getDoctrine()->getManager();
        $article = $entityManager->getRepository(Article::class)->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($article);
        $entityManager->flush();

        return $this->redirectToRoute('articles');
    }
}
