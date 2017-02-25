<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\News;
use AppBundle\Form\NewsType;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Exception\NotValidCurrentPageException;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * News controller.
 *
 * @Route("/admin/news")
 *
 */
class NewsController extends Controller
{
    /**
     * Lists all news entities.
     *
     * @Route("/", name="admin_index", defaults={"page": "1"})
     * @Route("/", name="admin_news_index")
     * @Route("/page/{page}", requirements={"page":"[1-9]\d*"}, name="news_admin_paginated")
     * @Method("GET")
     */
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();
        $news = $em->getRepository(News::class)->findBy([], ['created' => 'DESC']);

        $adapter = new ArrayAdapter($news);
        $adminPager = new Pagerfanta($adapter);
        $adminPager->setMaxPerPage(10);
        dump($adminPager);

        try {
            $adminPager->setCurrentPage($page);
        } catch(NotValidCurrentPageException $e) {
            throw new NotFoundHttpException();
        }

        return $this->render('admin/news/index.html.twig', [
            'news' => $news,
            'adminPager' => $adminPager,
        ]);
    }

    /**
     * Creates a new news entity.
     *
     * @Route("/new", name="admin_news_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $news = new News();
        $news->setAuthor($this->getUser());
        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $news->setSlug($this->get('slugger')->slugify($news->getTitle()));

            $em = $this->getDoctrine()->getManager();
            $em->persist($news);
            $em->flush();

            $this->addFlash('success', 'News created successfully');

            return $this->redirectToRoute('admin_news_index');
        }

        return $this->render('admin/news/new.html.twig', [
            'news' => $news,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a news entity.
     *
     * @Route("/{id}", name="admin_news_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(News $news)
    {
        return $this->render('admin/news/show.html.twig', [
            'news' => $news,
        ]);
    }

    /**
     * Displays a form to edit an existing news entity.
     *
     * @Route("/{id}/edit", name="admin_news_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, News $news)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $news->setSlug($this->get('slugger')->slugify($news->getTitle()));
            $em->flush();

            $this->addFlash('success', 'News updated successfully');

            return $this->redirectToRoute('admin_news_edit', ['id' => $news->getId()]);
        }

        return $this->render('admin/news/edit.html.twig', [
            'news' => $news,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a news entity.
     *
     * @Route("/{id}/delete", name="admin_news_delete")
     * @Method("POST")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function deleteAction(Request $request, News $news)
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('admin_news_index');
        }

        $em = $this->getDoctrine()->getManager();

        $news->getTags()->clear();

        $em->remove($news);
        $em->flush();

        $this->addFlash('success', 'News deleted successfully');

        return $this->redirectToRoute('admin_news_index');
    }
}
