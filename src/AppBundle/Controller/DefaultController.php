<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Comment;
use AppBundle\Entity\News;
use AppBundle\Entity\Tag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class DefaultController
 * @package AppBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Method("GET")
     * @throws \LogicException
     */
    public function indexAction()
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $businessPost = $this->getDoctrine()->getRepository(News::class)->queryNewsByCat('business', 5);
        $analyticsPost = $this->getDoctrine()->getRepository(News::class)->queryNewsByCat('analytics', 5);
        $sportPost = $this->getDoctrine()->getRepository(News::class)->queryNewsByCat('sport', 5);
        $politicsPost = $this->getDoctrine()->getRepository(News::class)->queryNewsByCat('politics', 5);

        $news = $this->getDoctrine()->getRepository(News::class)->findBy([],['id' => 'DESC'], 5);

        $tags = $this->getDoctrine()->getRepository(Tag::class)->findAll();
        $comments = $this->getDoctrine()->getRepository(Comment::class)->findAll();

        return $this->render('default/homepage.html.twig', [
            'business' => $businessPost,
            'categories' => $categories,
            'analytics' => $analyticsPost,
            'sport' => $sportPost,
            'politics' => $politicsPost,
            'comments' => $comments,
            'tags' => $tags,
            'news' => $news
        ]);
    }
}