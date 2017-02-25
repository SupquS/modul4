<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\News;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Exception\NotValidCurrentPageException;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Category Controller
 *
 * @Route("/category")
 */
class CategoryController extends Controller
{
    /**
     * Lists all category entities.
     *
     * @Route("/", name="category_index", defaults={"page": "1"})
     * @Route("/page/{page}", requirements={"page":"[1-9]\d*"}, name="category_index_paginated")
     * @Method("GET")
     */
    public function indexAction($page)
    {
        $news = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll($page);

        return $this->render('category/index.html.twig', [
            'news' => $news,
        ]);
    }

    /**
     * Finds and displays a news entity.
     *
     * @Route("/{catSlug}", name="category_show", defaults={"page": "1"})
     * @Route("/{catSlug}/page/{page}", requirements={"page":"[1-9]\d*"}, name="category_show_paginated")
     * @Method("GET")
     *
     * @ParamConverter("category", options={"mapping": {"catSlug": "slug"}})
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function categoryShowAction(Category $category, $catSlug, $page)
    {

        $newsPosts = $this->getDoctrine()->getRepository(News::class)->queryNewsByCat($catSlug, 5);
//        $newsPosts = $this->getDoctrine()->getRepository(News::class)->findAll();
//        dump($newsPosts);
        $adapter = new ArrayAdapter($newsPosts);
        $pager = new Pagerfanta($adapter);
        $pager->setMaxPerPage(5);

        try {
            $pager->setCurrentPage($page);
        } catch(NotValidCurrentPageException $e) {
            throw new NotFoundHttpException();
        }

        return $this->render('category/show.html.twig', [
            'category' => $category,
            'news' => $newsPosts,
            'pager' => $pager,
        ]);
    }
}