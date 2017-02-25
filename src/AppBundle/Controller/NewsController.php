<?php

namespace AppBundle\Controller;

use AppBundle\Entity\News;
use AppBundle\Form\CommentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * News controller.
 *
 * @Route("/news")
 */
class NewsController extends Controller
{
    /**
     * Lists all news entities.
     *
     * @Route("/", name="news_index", defaults={"page": "1"})
     * @Route("/page/{page}", requirements={"page":"[1-9]\d*"}, name="news_index_paginated")
     * @Method("GET")
     */
    public function indexAction($page)
    {
        $news = $this->getDoctrine()
                ->getRepository(News::class)
                ->findLatest($page);

        return $this->render('news/index.html.twig', [
            'news' => $news,
        ]);
    }

    /**
     * Finds and displays a news entity.
     *
     * @Route("/{slug}", name="news_show")
     * @Method("GET")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newsShowAction(News $news)
    {
        dump($news);
        return $this->render('news/show.html.twig', [
            'news' => $news,
        ]);
    }

    /**
     * @param Request $request
     * @param News $news
     *
     * @Route("/comment/{newsSlug}/new", name="comment_new")
     * @Method("POST")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @ParamConverter("news", options={"mapping": {"newsSlug": "slug"}})
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function commentAddAction(Request $request, News $news)
    {
        $form = $this->createForm(CommentType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setAuthor($this->getUser());
            $comment->setNews($news);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('news_show', ['slug' => $news->getSlug()]);
        }

        return $this->render('news/comment_error_form.html.twig', [
            'news' => $news,
            'form' => $form->createView(),
        ]);
    }

    public function commentFormAction(News $news)
    {
        $form = $this->createForm(CommentType::class);

        return $this->render('news/comment_form.html.twig', [
            'news' => $news,
            'form' => $form->createView(),
        ]);
    }
}
