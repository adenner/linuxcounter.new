<?php

namespace Syw\Front\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class ErrorController extends BaseController
{
    /**
     * @Route("/error/{error}")
     *
     * @Template()
     */
    public function errorAction(Request $request, $error)
    {
        $languages = $this->get('doctrine')
            ->getRepository('SywFrontMainBundle:Languages')
            ->findBy(array('active' => 1), array('language' => 'ASC'));

        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $this->getUser();
        } else {
            $user = null;
        }
        $stats = array();
        $metatitle = "";
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
            'stats' => $stats
        );
    }
}
