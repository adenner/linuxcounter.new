<?php

namespace Syw\Front\NewsBundle\Controller;

use BladeTester\LightNewsBundle\Controller\DefaultController as LightNewsDefaultController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends BaseController
{
    /**
     * @Template()
     */
    public function indexAction()
    {
        $manager = $this->getNewsManager();
        $news    = $manager->findBy(array(), array('createdAt' => 'DESC'));

        $languages = $this->get('doctrine')
            ->getRepository('SywFrontMainBundle:Languages')
            ->findBy(array('active' => 1), array('language' => 'ASC'));

        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $this->getUser();
        } else {
            $user = null;
        }

        $metatitle = $this->get('translator')->trans('News and Announcements');
        $title = $metatitle;
        $online = $this->getOnlineUsers();
        $actuallocale = $this->get('request')->getLocale();
        $transtolanguage = $this->get('doctrine')
            ->getRepository('SywFrontMainBundle:Languages')
            ->findOneBy(array('locale' => $actuallocale));
        $transform_array = $this->getTranslateForm();
        return array(
            'formTrans_navi' => $transform_array['navi']->createView(),
            'formTrans_route' => $transform_array['route']->createView(),
            'formTrans_footer' => $transform_array['footer']->createView(),
            'formTrans_others' => $transform_array['others']->createView(),
            'transtolanguage' => $transtolanguage->getLanguage(),
            'online' => $online,
            'metatitle' => $metatitle,
            'title' => $title,
            'languages' => $languages,
            'user' => $user,
            'news' => $news
        );
    }


    /**
     * @Template()
     */
    public function viewAction($id)
    {
        $manager = $this->getNewsManager();
        $news    = $manager->find($id);

        $languages = $this->get('doctrine')
            ->getRepository('SywFrontMainBundle:Languages')
            ->findBy(array('active' => 1), array('language' => 'ASC'));

        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $this->getUser();
        } else {
            $user = null;
        }

        $body = $news->getBody();
        $metatitle = $news->getTitle();
        $metadescription = (strlen($body)>=150?substr($body, 0, 150)."...":$body);
        $title = $metatitle;
        $online = $this->getOnlineUsers();
        $actuallocale = $this->get('request')->getLocale();
        $transtolanguage = $this->get('doctrine')
            ->getRepository('SywFrontMainBundle:Languages')
            ->findOneBy(array('locale' => $actuallocale));
        $transform_array = $this->getTranslateForm();
        return array(
            'formTrans_navi' => $transform_array['navi']->createView(),
            'formTrans_route' => $transform_array['route']->createView(),
            'formTrans_footer' => $transform_array['footer']->createView(),
            'formTrans_others' => $transform_array['others']->createView(),
            'transtolanguage' => $transtolanguage->getLanguage(),
            'online' => $online,
            'metatitle' => $metatitle,
            'metadescription' => $metadescription,
            'title' => $title,
            'languages' => $languages,
            'user' => $user,
            'news' => $news
        );
    }

    private function getNewsManager()
    {
        return $this->get('blade_tester_light_news.news_manager');
    }
}
