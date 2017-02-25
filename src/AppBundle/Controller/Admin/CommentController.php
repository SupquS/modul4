<?php

namespace AppBundle\Controller\Admin;


use AppBundle\Entity\Comment;
use AppBundle\Form\CommentType;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Exception\NotValidCurrentPageException;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Comments controller.
 *
 * @Route("/admin/comments")
 *
 */
class CommentController extends Controller
{
    /**
     * @Route("/", name="admin_comment_index", defaults={"page": "1"})
     * @Route("/page/{page}", requirements={"page":"[1-9]\d*"}, name="comment_admin_paginated")
     * @Method("GET")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();
        $comments = $em->getRepository(Comment::class)->queryCommentsAll();
        dump($comments);
        $adapter = new ArrayAdapter($comments);
        $adminPager = new Pagerfanta($adapter);
        $adminPager->setMaxPerPage(10);

        try {
            $adminPager->setCurrentPage($page);
        } catch(NotValidCurrentPageException $e) {
            throw new NotFoundHttpException();
        }

        return $this->render('admin/comment/index.html.twig', [
            'comments' => $comments,
            'adminPager' => $adminPager,
        ]);
    }

    /**
     * Finds and displays a comment entity.
     *
     * @Route("/{id}", name="admin_comment_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Comment $comment)
    {
        return $this->render('admin/comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    /**
     * Displays a form to edit an existing comment entity.
     *
     * @Route("/{id}/edit", name="admin_comment_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Comment $comment)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            $comment->setSlug($this->get('slugger')->slugify($comment->getTitle()));
            $em->flush();

            $this->addFlash('success', 'Comment updated successfully');

            return $this->redirectToRoute('admin_comment_edit', ['id' => $comment->getId()]);
        }

        return $this->render('admin/comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Creates a new comment entity.
     *
     * @Route("/new", name="admin_comment_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $comment = new Comment();
        $comment->setAuthor($this->getUser());
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            $comment->setSlug($this->get('slugger')->slugify($comment->getTitle()));

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'Comment added successfully');

            return $this->redirectToRoute('admin_comment_index');
        }

        return $this->render('admin/comment/new.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }
}