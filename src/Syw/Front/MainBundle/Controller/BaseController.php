<?php

namespace Syw\Front\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Syw\Front\MainBundle\Form\Type\TranslationFormType;
use Asm\TranslationLoaderBundle\Entity\Translation;
use Abraham\TwitterOAuth\TwitterOAuth;

/**
 * Class BaseController
 *
 * @author Alexander Löhner <alex.loehner@linux.com>
 */
class BaseController extends Controller
{
    protected function getHost()
    {
        return $this->container->getParameter('base_proto')."://".$this->container->getParameter('base_host');
    }

    /**
     * @Route("/translate/thispage")
     */
    public function getTranslateForm()
    {
        $route = $this->get('request')->get('_route');
        $actuallocale = $this->get('request')->getLocale();
        $em = $this->getDoctrine()->getManager();
        $qb = null;
        $qb = $em->createQueryBuilder();
        $qb->select('t')
            ->from('AsmTranslationLoaderBundle:Translation', 't')
            ->where('t.transLocale = :locale')
            ->andwhere('t.messageDomain = :domain')
            ->setParameter('locale', $actuallocale)
            ->setParameter('domain', 'navigation')
        ;
        $result2 = $qb->getQuery()->getResult();
        $qb = null;
        $qb = $em->createQueryBuilder();
        $qb->select('t')
            ->from('AsmTranslationLoaderBundle:Translation', 't')
            ->where('t.transLocale = :locale')
            ->andwhere('t.messageDomain = :domain')
            ->setParameter('locale', $actuallocale)
            ->setParameter('domain', $route)
        ;
        $result3 = $qb->getQuery()->getResult();
        $qb = null;
        $qb = $em->createQueryBuilder();
        $qb->select('t')
            ->from('AsmTranslationLoaderBundle:Translation', 't')
            ->where('t.transLocale = :locale')
            ->andwhere('t.messageDomain = :domain')
            ->setParameter('locale', $actuallocale)
            ->setParameter('domain', 'footer')
        ;
        $result4 = $qb->getQuery()->getResult();
        $qb = null;
        $qb = $em->createQueryBuilder();
        $qb->select('t')
            ->from('AsmTranslationLoaderBundle:Translation', 't')
            ->where('t.transLocale = :locale')
            ->andwhere('t.messageDomain NOT IN (:domain1, :domain2, :domain3)')
            ->setParameter('locale', $actuallocale)
            ->setParameter('domain1', 'navigation')
            ->setParameter('domain2', $route)
            ->setParameter('domain3', 'footer')
        ;
        $result5 = $qb->getQuery()->getResult();

        $trans_navi = array('translations' => $result2);
        $formTrans_navi = $this->createFormBuilder($trans_navi)
            ->add('translations', 'collection', array('type' => new TranslationFormType()))
            ->getForm();
        $trans_route = array('translations' => $result3);
        $formTrans_route = $this->createFormBuilder($trans_route)
            ->add('translations', 'collection', array('type' => new TranslationFormType()))
            ->getForm();
        $trans_footer = array('translations' => $result4);
        $formTrans_footer = $this->createFormBuilder($trans_footer)
            ->add('translations', 'collection', array('type' => new TranslationFormType()))
            ->getForm();
        $trans_others = array('translations' => $result5);
        $formTrans_others = $this->createFormBuilder($trans_others)
            ->add('translations', 'collection', array('type' => new TranslationFormType()))
            ->getForm();

        $return['navi'] = $formTrans_navi;
        $return['route'] = $formTrans_route;
        $return['footer'] = $formTrans_footer;
        $return['others'] = $formTrans_others;
        return $return;

        /*
        $translations = array('translations' => array_merge($result3, $result1, $result2, $result4, $result5, $result6, $result7));
        $formTranslations = $this->createFormBuilder($translations)
            ->add('translations', 'collection', array('type' => new TranslationFormType()))
            ->getForm();
        return $formTranslations;
        */
    }

    public function getGuessStats()
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('count(user.id)');
        $qb->from('SywFrontMainBundle:User', 'user');
        $uCount = $qb->getQuery()->getSingleScalarResult();
        $stats['usernum'] = $uCount;
        $qb = $em->createQueryBuilder();
        $qb->select('count(machines.id)');
        $qb->from('SywFrontMainBundle:Machines', 'machines');
        $mCount = $qb->getQuery()->getSingleScalarResult();
        $stats['machinenum'] = $mCount;
        $popcont = file("../population.db");
        $popstr = trim($popcont[0]);
        $iustr = trim($popcont[1]);
        $tmp = explode("|", $popstr);
        $pop = (float)$tmp[0];
        $date = $tmp[1];
        $rate = (float)$tmp[2];
        $tmp = explode("/", $date);
        $day = intval($tmp[1]);
        $mon = intval($tmp[0]);
        $year = intval($tmp[2]);
        $oldts = gmmktime(0, 0, 0, $mon, $day, $year);
        $diff = time() - $oldts;
        $newhuman = $rate * $diff;
        $aktpop = $pop + $newhuman;
        $stats['world_population'] =   round($aktpop);
        $tmp = explode("|", $iustr);
        $iupop = intval($tmp[0]);
        $iudate = $tmp[1];
        $iurate = (float)$tmp[2];
        $tmp = explode("/", $iudate);
        $day = intval($tmp[1]);
        $mon = intval($tmp[0]);
        $year = intval($tmp[2]);
        $oldts = mktime(0, 0, 0, $mon, $day, $year);
        $diff = time() - $oldts;
        $newiusers = $iurate * $diff;
        $aktiusers = $iupop + $newiusers;
        $stats['world_internet_users'] =   round($aktiusers);
        $estimated_num_of_linux_users =   (($stats['world_internet_users'] / 100) * 2.55);
        $stats['guestimate_users'] =   $estimated_num_of_linux_users;
        return $stats;
    }

    public function getOnlineUsers()
    {
        $counts = array();
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $qb->select($qb->expr()->countDistinct('a.ipaddress'))
            ->from('SywFrontMainBundle:Activity', 'a')
            ->where('a.createdat >= :when')
            ->setParameter('when', new \DateTime('-5 minutes'))
        ;
        $counts['complete'] = $qb->getQuery()->getSingleScalarResult();

        $qb->select($qb->expr()->countDistinct('a.ipaddress'))
            // ->from('SywFrontMainBundle:Activity', 'a')
            ->where('a.createdat >= :when')
            ->andwhere('a.isbot = :isbot')
            ->setParameter('when', new \DateTime('-5 minutes'))
            ->setParameter('isbot', '1')
        ;
        $counts['bots'] = $qb->getQuery()->getSingleScalarResult();

        $qb->select($qb->expr()->countDistinct('a.ipaddress'))
            // ->from('SywFrontMainBundle:Activity', 'a')
            ->where('a.createdat >= :when')
            ->andwhere('a.isbot = :isbot')
            ->setParameter('when', new \DateTime('-5 minutes'))
            ->setParameter('isbot', '0')
        ;
        $counts['users'] = $qb->getQuery()->getSingleScalarResult();

        $qb->select($qb->expr()->countDistinct('a.ipaddress'))
            // ->from('SywFrontMainBundle:Activity', 'a')
            ->where('a.createdat >= :when')
            ->andwhere('a.isbot = :isbot')
            ->andwhere('a.user IS NOT NULL')
            ->setParameter('when', new \DateTime('-5 minutes'))
            ->setParameter('isbot', '0')
        ;
        $counts['loggedin'] = $qb->getQuery()->getSingleScalarResult();

        $qb->select($qb->expr()->countDistinct('a.ipaddress'))
            // ->from('SywFrontMainBundle:Activity', 'a')
            ->where('a.createdat >= :when')
            ->andwhere('a.isbot = :isbot')
            ->andwhere('a.user IS NULL')
            ->setParameter('when', new \DateTime('-5 minutes'))
            ->setParameter('isbot', '0')
        ;
        $counts['guests'] = $qb->getQuery()->getSingleScalarResult();

        return $counts;
    }

    public function getLastTweetsAction()
    {
        $connection = new TwitterOAuth(
            $this->container->getParameter('twitter_consumer_key'),
            $this->container->getParameter('twitter_consumer_secret'),
            $this->container->getParameter('twitter_oauth_token'),
            $this->container->getParameter('twitter_oauth_token_secret')
        );
        $statues = $connection->get('statuses/mentions_timeline', array("count" => 5, "exclude_replies" => false));

        return $this->render('SywFrontMainBundle:Common:_tweets1.html.twig', array(
            'tweets1' => $statues,
        ));
    }

    public function getUserLineAction()
    {
        $connection = new TwitterOAuth(
            $this->container->getParameter('twitter_consumer_key'),
            $this->container->getParameter('twitter_consumer_secret'),
            $this->container->getParameter('twitter_oauth_token'),
            $this->container->getParameter('twitter_oauth_token_secret')
        );
        $statues = $connection->get('statuses/user_timeline', array("count" => 5, "exclude_replies" => false));

        return $this->render('SywFrontMainBundle:Common:_tweets2.html.twig', array(
            'tweets2' => $statues,
        ));
    }
}
