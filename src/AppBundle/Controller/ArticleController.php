<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\ArticleComment;
use AppBundle\Form\ArticleCommentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ArticleController extends Controller
{
    /**
     * @Route("/articles", name="homepage")
     */
    public function indexAction()
    {
        $articles = $this->getDoctrine()
            ->getRepository('AppBundle:Article')
            ->findAll();

        return $this->render(
            'AppBundle:Article:index.html.twig',
            [
                'articles' => $articles
            ]
        );
    }

    /**
     * @Route("/test", name="test_article")
     */
    public function testAction()
    {
        $newArticle = new Article();
        $newArticle->setAuthor('Austin');
        $newArticle->setContent(
            'This is my second blog post test. Blah blah blah blah. Even longer than the first one. haha.'
        );
        $newArticle->setTitle('Blog Post 2');
        $newArticle->setCreatedAt(new \DateTime());

        $this->getDoctrine()->getManager()->persist($newArticle);
        $this->getDoctrine()->getManager()->flush();

        return new Response('Added test entities to the database!');
    }


    /**
     * @Route("/article/{id}", name="view_article")
     */
    public function viewAction(Article $article, Request $request)
    {

        $comment = new ArticleComment();
        $comment->setArticle($article);

        $form = $this->createForm(
            new ArticleCommentType(),
            $comment,
            array(
                'action' => $this->generateUrl('view_article', ['id' => $article->getId()]),
                'method' => 'POST',
            )
        );

        $em = $this->getDoctrine()->getManager();

        if ($request->getMethod() == "POST") {
            $form->handleRequest($request);
            $data = $form->getData();

            if ($form->isValid()) {
                $em->persist($data);
                $em->flush();
            }

            return $this->redirectToRoute('view_article', ['id' => $article->getId()]);
        }


        return $this->render(
            'AppBundle:Article:view.html.twig',
            [
                'article' => $article,
                'form' => $form->createView()
            ]
        );
    }
}
