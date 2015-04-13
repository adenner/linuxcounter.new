<?php

namespace Syw\Front\NewsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use BladeTester\LightNewsBundle\Controller\DefaultController as LightNewsDefaultController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Syw\Front\MainBundle\Form\Type\TranslationFormType;
use Asm\TranslationLoaderBundle\Entity\Translation;

/**
 * Class BaseController
 *
 * @author Alexander Löhner <alex.loehner@linux.com>
 */
class BaseController extends LightNewsDefaultController
{
    public function getTranslateForm()
    {
        $route = $this->get('request')->get('_route');
        $actuallocale = $this->get('request')->getLocale();
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('t')
            ->from('AsmTranslationLoaderBundle:Translation', 't')
            ->where('t.transLocale = :locale')
            ->andwhere('t.messageDomain = :domain')
            ->setParameter('locale', $actuallocale)
            ->setParameter('domain', 'general')
        ;
        $result1 = $qb->getQuery()->getResult();
        $qb->select('t')
            ->where('t.transLocale = :locale')
            ->andwhere('t.messageDomain = :domain')
            ->setParameter('locale', $actuallocale)
            ->setParameter('domain', 'navigation')
        ;
        $result2 = $qb->getQuery()->getResult();
        $qb->select('t')
            ->where('t.transLocale = :locale')
            ->andwhere('t.messageDomain = :domain')
            ->setParameter('locale', $actuallocale)
            ->setParameter('domain', $route)
        ;
        $result3 = $qb->getQuery()->getResult();
        $qb->select('t')
            ->where('t.transLocale = :locale')
            ->andwhere('t.messageDomain = :domain')
            ->setParameter('locale', $actuallocale)
            ->setParameter('domain', 'footer')
        ;
        $result4 = $qb->getQuery()->getResult();
        $translations = array('translations' => array_merge($result1, $result2, $result3, $result4));
        $formTranslations = $this->createFormBuilder($translations)
            ->add('translations', 'collection', array('type' => new TranslationFormType()))
            ->getForm();
        return $formTranslations;
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
}
