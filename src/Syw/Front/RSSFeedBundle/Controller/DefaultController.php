<?php

namespace Syw\Front\RSSFeedBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * Generate the news feed
     *
     * @Route("/rss")
     * @return Response XML Feed
     */
    public function feedAction()
    {
        $articles = $this->getDoctrine()->getRepository('SywFrontNewsBundle:News')->findAll();

        $feed = $this->get('eko_feed.feed.manager')->get('article');
        $feed->addFromArray($articles);

        return new Response($feed->render('rss')); // or 'atom'
    }
}
