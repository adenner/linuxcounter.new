<?php

namespace Syw\Front\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Ob\HighchartsBundle\Highcharts\Highchart;
use Syw\Front\MainBundle\Util\XmlToArrayParser;

class StatsController extends BaseController
{
    /**
     * @Route("/statistics")
     * @Method("GET")
     *
     * @Template()
     */
    public function indexAction()
    {
        $metatitle = $this->get('translator')->trans('Statistics mainpage', array(), 'syw_front_main_stats_index');
        $title1 = $metatitle;
        $title2 = $this->get('translator')->trans('The estimation of linux users', array(), 'syw_front_main_stats_index');
        $languages = $this->get('doctrine')
            ->getRepository('SywFrontMainBundle:Languages')
            ->findBy(array('active' => 1), array('language' => 'ASC'));

        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $this->getUser();
        } else {
            $user = null;
        }
        $stats = array();
        $stats['guess'] = $this->getGuessStats();
        $online = $this->getOnlineUsers();
        $return2 = $this->getTransForm($user);
        $return1 = array(
            'online' => $online,
            'metatitle' => $metatitle,
            'metadescription' => $this->get('translator')->trans('See here how many linux users there are in the world and how many users and machines actually are registered to the Linux Counter', array(), 'syw_front_main_main_index'),
            'title1' => $title1,
            'title2' => $title2,
            'languages' => $languages,
            'user' => $user,
            'stats' => $stats
        );
        return array_merge($return1, $return2);
    }

    /**
     * @Route("/statistics/users")
     * @Method("GET")
     *
     * @Template()
     */
    public function usersAction()
    {
        $metatitle = $this->get('translator')->trans('Statistics about the Linux users', array(), 'syw_front_main_stats_users');
        $title = $metatitle;
        $languages = $this->get('doctrine')
            ->getRepository('SywFrontMainBundle:Languages')
            ->findBy(array('active' => 1), array('language' => 'ASC'));
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $this->getUser();
        } else {
            $user = null;
        }
        $stats = array();
        $stats['guess'] = $this->getGuessStats();

        $em = $this->getDoctrine()->getManager();
        $dql   = "SELECT b FROM SywFrontMainBundle:Cities b WHERE b.usernum >= 1 ORDER BY b.usernum DESC, b.name ASC";
        $ent_cities = $em->createQuery($dql);
        $dql   = "SELECT a FROM SywFrontMainBundle:Countries a WHERE a.usersnum >= 1 ORDER BY a.usersnum DESC, a.name ASC";
        $ent_countries = $em->createQuery($dql);
        $knpPaginator = $this->get('knp_paginator');
        $paginationAAA = $knpPaginator->paginate(
            $ent_cities,
            $this->get('request')->query->get('pageAAA', 1), // page number
            15, // limit per page
            array(
                'pageParameterName' => 'pageAAA',
                'sortFieldParameterName' => 'sortAAA',
                'sortDirectionParameterName' => 'directionAAA',
            )
        );
        $paginationBBB = $knpPaginator->paginate(
            $ent_countries,
            $this->get('request')->query->get('pageBBB', 1), // page number
            15, // limit per page
            array(
                'pageParameterName' => 'pageBBB',
                'sortFieldParameterName' => 'sortBBB',
                'sortDirectionParameterName' => 'directionBBB',
            )
        );

        $online = $this->getOnlineUsers();
        $return2 = $this->getTransForm($user);
        $return1 = array(
            'paginationAAA' => $paginationAAA,
            'paginationBBB' => $paginationBBB,
            'online' => $online,
            'metatitle' => $metatitle,
            'metadescription' => $this->get('translator')->trans('This is all about statistics around the Linux User base. See how many Linux users there are in your city or country.', array(), 'syw_front_main_main_index'),
            'title' => $title,
            'languages' => $languages,
            'user' => $user,
            'stats' => $stats
        );
        return array_merge($return1, $return2);
    }

    /**
     * @Route("/statistics/cities/{id}")
     * @Method("GET")
     *
     * @Template()
     */
    public function citiesAction($id)
    {

        $city = $this->get('doctrine')
            ->getRepository('SywFrontMainBundle:Cities')
            ->findOneBy(array('id' => $id));
        $country = $this->get('doctrine')
            ->getRepository('SywFrontMainBundle:Countries')
            ->findOneBy(array('code' => strtolower($city->getIsoCountryCode())));
        $userProfiles = $city->getUsers();

        $metatitle = $this->get('translator')->trans('Statistics about the Linux users in %city%', array(
            '%city%' => $city->getName().", ".strtoupper($city->getIsoCountryCode())
        ), 'syw_front_main_stats_cities');
        $title = $metatitle;
        $languages = $this->get('doctrine')
            ->getRepository('SywFrontMainBundle:Languages')
            ->findBy(array('active' => 1), array('language' => 'ASC'));
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $this->getUser();
        } else {
            $user = null;
        }
        $stats = array();
        $stats['guess'] = $this->getGuessStats();
        $online = $this->getOnlineUsers();
        $return2 = $this->getTransForm($user);
        $return1 = array(
            'city' => $city,
            'country' => $country,
            'users' => $users,
            'online' => $online,
            'metatitle' => $metatitle,
            'metadescription' => $this->get('translator')->trans('See here how many Linux users there an in this specific city', array(), 'syw_front_main_main_index'),
            'title' => $title,
            'languages' => $languages,
            'user' => $user,
            'stats' => $stats
        );
        return array_merge($return1, $return2);
    }

    /**
     * @Route("/statistics/countries/{id}")
     * @Method("GET")
     *
     * @Template()
     */
    public function countriesAction($id)
    {
        $country = $this->get('doctrine')
            ->getRepository('SywFrontMainBundle:Countries')
            ->findOneBy(array('id' => $id));

        $metatitle = $this->get('translator')->trans('Statistics about the Linux users in %country%', array(
            '%country%' => $country->getName().", ".strtoupper($country->getCode())
        ), 'syw_front_main_stats_countries');
        $title = $metatitle;
        $languages = $this->get('doctrine')
            ->getRepository('SywFrontMainBundle:Languages')
            ->findBy(array('active' => 1), array('language' => 'ASC'));
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $this->getUser();
        } else {
            $user = null;
        }
        $stats = array();
        $stats['guess'] = $this->getGuessStats();
        $online = $this->getOnlineUsers();
        $return2 = $this->getTransForm($user);
        $return1 = array(
            'country' => $country,
            'online' => $online,
            'metatitle' => $metatitle,
            'metadescription' => $this->get('translator')->trans('See here how many Linux users there an in this specific country', array(), 'syw_front_main_main_index'),
            'title' => $title,
            'languages' => $languages,
            'user' => $user,
            'stats' => $stats
        );
        return array_merge($return1, $return2);
    }

    /**
     * @Route("/statistics/machines")
     * @Method("GET")
     *
     * @Template()
     */
    public function machinesAction()
    {
        $metatitle = $this->get('translator')->trans('Statistics about the Linux machines', array(), 'syw_front_main_stats_machines');
        $languages = $this->get('doctrine')
            ->getRepository('SywFrontMainBundle:Languages')
            ->findBy(array('active' => 1), array('language' => 'ASC'));

        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $this->getUser();
        } else {
            $user = null;
        }
        $stats = array();
        $stats['guess'] = $this->getGuessStats();

        $em = $this->getDoctrine()->getManager();

        // accounts on the machines
        $title1 = $this->get('translator')->trans('Statistics about the number of accounts on the machines', array(), 'syw_front_main_stats_machines');

        $dql   = "SELECT COUNT(m) AS num FROM SywFrontMainBundle:Machines m WHERE m.accounts >= 1";
        $machine_accounts_gesamt = $em->createQuery($dql)->getSingleScalarResult();

        $dql   = "SELECT COUNT(m) AS num FROM SywFrontMainBundle:Machines m WHERE m.accounts = 1";
        $num = $em->createQuery($dql)->getSingleScalarResult();
        $percent = round((100/$machine_accounts_gesamt) * intval($num), 2);
        $stats['accounts'][0]['accounts'] = '1';
        $stats['accounts'][0]['number'] = $num;
        $stats['accounts'][0]['percent'] = $percent;

        $dql   = "SELECT COUNT(m) AS num FROM SywFrontMainBundle:Machines m WHERE m.accounts = 2";
        $num = $em->createQuery($dql)->getSingleScalarResult();
        $percent = round((100/$machine_accounts_gesamt) * intval($num), 2);
        $stats['accounts'][1]['accounts'] = '2';
        $stats['accounts'][1]['number'] = $num;
        $stats['accounts'][1]['percent'] = $percent;

        $dql   = "SELECT COUNT(m) AS num FROM SywFrontMainBundle:Machines m WHERE m.accounts = 3";
        $num = $em->createQuery($dql)->getSingleScalarResult();
        $percent = round((100/$machine_accounts_gesamt) * intval($num), 2);
        $stats['accounts'][2]['accounts'] = '3';
        $stats['accounts'][2]['number'] = $num;
        $stats['accounts'][2]['percent'] = $percent;

        $dql   = "SELECT COUNT(m) AS num FROM SywFrontMainBundle:Machines m WHERE m.accounts = 4";
        $num = $em->createQuery($dql)->getSingleScalarResult();
        $percent = round((100/$machine_accounts_gesamt) * intval($num), 2);
        $stats['accounts'][3]['accounts'] = '4';
        $stats['accounts'][3]['number'] = $num;
        $stats['accounts'][3]['percent'] = $percent;

        $dql   = "SELECT COUNT(m) AS num FROM SywFrontMainBundle:Machines m WHERE m.accounts BETWEEN 5 AND 9";
        $num = $em->createQuery($dql)->getSingleScalarResult();
        $percent = round((100/$machine_accounts_gesamt) * intval($num), 2);
        $stats['accounts'][4]['accounts'] = '5-9';
        $stats['accounts'][4]['number'] = $num;
        $stats['accounts'][4]['percent'] = $percent;

        $dql   = "SELECT COUNT(m) AS num FROM SywFrontMainBundle:Machines m WHERE m.accounts BETWEEN 10 AND 24";
        $num = $em->createQuery($dql)->getSingleScalarResult();
        $percent = round((100/$machine_accounts_gesamt) * intval($num), 2);
        $stats['accounts'][5]['accounts'] = '10-24';
        $stats['accounts'][5]['number'] = $num;
        $stats['accounts'][5]['percent'] = $percent;

        $dql   = "SELECT COUNT(m) AS num FROM SywFrontMainBundle:Machines m WHERE m.accounts BETWEEN 25 AND 49";
        $num = $em->createQuery($dql)->getSingleScalarResult();
        $percent = round((100/$machine_accounts_gesamt) * intval($num), 2);
        $stats['accounts'][6]['accounts'] = '25-49';
        $stats['accounts'][6]['number'] = $num;
        $stats['accounts'][6]['percent'] = $percent;

        $dql   = "SELECT COUNT(m) AS num FROM SywFrontMainBundle:Machines m WHERE m.accounts BETWEEN 50 AND 99";
        $num = $em->createQuery($dql)->getSingleScalarResult();
        $percent = round((100/$machine_accounts_gesamt) * intval($num), 2);
        $stats['accounts'][7]['accounts'] = '50-99';
        $stats['accounts'][7]['number'] = $num;
        $stats['accounts'][7]['percent'] = $percent;

        $dql   = "SELECT COUNT(m) AS num FROM SywFrontMainBundle:Machines m WHERE m.accounts BETWEEN 100 AND 249";
        $num = $em->createQuery($dql)->getSingleScalarResult();
        $percent = round((100/$machine_accounts_gesamt) * intval($num), 2);
        $stats['accounts'][8]['accounts'] = '100-249';
        $stats['accounts'][8]['number'] = $num;
        $stats['accounts'][8]['percent'] = $percent;

        $dql   = "SELECT COUNT(m) AS num FROM SywFrontMainBundle:Machines m WHERE m.accounts >= 250";
        $num = $em->createQuery($dql)->getSingleScalarResult();
        $percent = round((100/$machine_accounts_gesamt) * intval($num), 2);
        $stats['accounts'][9]['accounts'] = '>= 250';
        $stats['accounts'][9]['number'] = $num;
        $stats['accounts'][9]['percent'] = $percent;

        // countries of the machines
        $dql   = "SELECT SUM(c.machinesnum) AS num FROM SywFrontMainBundle:Countries c";
        $machine_countries_gesamt = $em->createQuery($dql)->getSingleScalarResult();
        $title2 = $this->get('translator')->trans('Statistics about the number of machines per country', array(), 'syw_front_main_stats_machines');
        $dql   = "SELECT a FROM SywFrontMainBundle:Countries a WHERE a.machinesnum >= 1 ORDER BY a.machinesnum DESC, a.name ASC";
        $ent_countries = $em->createQuery($dql);
        $knpPaginator = $this->get('knp_paginator');
        $paginationAAA = $knpPaginator->paginate(
            $ent_countries,
            $this->get('request')->query->get('pageAAA', 1), // page number
            15, // limit per page
            array(
                'pageParameterName' => 'pageAAA',
                'sortFieldParameterName' => 'sortAAA',
                'sortDirectionParameterName' => 'directionAAA',
            )
        );
        // end accounts on the machines

        $online = $this->getOnlineUsers();
        $return2 = $this->getTransForm($user);
        $return1 = array(
            'paginationAAA' => $paginationAAA,
            'online' => $online,
            'metatitle' => $metatitle,
            'metadescription' => $this->get('translator')->trans('This is all about statistics around the Linux machines. See how many Linux machines there are in your city or country.', array(), 'syw_front_main_main_index'),
            'title1' => $title1,
            'title2' => $title2,
            'machine_accounts_gesamt' => $machine_accounts_gesamt,
            'machine_countries_gesamt' => $machine_countries_gesamt,
            'languages' => $languages,
            'user' => $user,
            'stats' => $stats
        );
        return array_merge($return1, $return2);
    }

    /**
     * @Route("/statistics/distributions")
     * @Method("GET")
     *
     * @Template()
     */
    public function distributionsAction()
    {
        $metatitle = $this->get('translator')->trans('Statistics about the Linux distributions', array(), 'syw_front_main_stats_distributionsx');
        $title = $metatitle;
        $languages = $this->get('doctrine')
            ->getRepository('SywFrontMainBundle:Languages')
            ->findBy(array('active' => 1), array('language' => 'ASC'));

        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $this->getUser();
        } else {
            $user = null;
        }
        $stats = array();
        $stats['guess'] = $this->getGuessStats();

        $em = $this->getDoctrine()->getManager();
        $dql   = "SELECT a FROM SywFrontMainBundle:Distributions a WHERE a.machinesnum >= 1 ORDER BY a.machinesnum DESC, a.name ASC";
        $entitites_a = $em->createQuery($dql);
        $knpPaginator = $this->get('knp_paginator');
        $paginationAAA = $knpPaginator->paginate(
            $entitites_a,
            $this->get('request')->query->get('pageAAA', 1), // page number
            15, // limit per page
            array(
                'pageParameterName' => 'pageAAA',
                'sortFieldParameterName' => 'sortAAA',
                'sortDirectionParameterName' => 'directionAAA',
            )
        );

        $online = $this->getOnlineUsers();
        $return2 = $this->getTransForm($user);
        $return1 = array(
            'paginationAAA' => $paginationAAA,
            'online' => $online,
            'metatitle' => $metatitle,
            'metadescription' => $this->get('translator')->trans('Ever wanted to know which Linux distribution is the most used one. Here you get the answer.', array(), 'syw_front_main_main_index'),
            'title' => $title,
            'languages' => $languages,
            'user' => $user,
            'stats' => $stats
        );
        return array_merge($return1, $return2);
    }

    /**
     * @Route("/statistics/kernels")
     * @Method("GET")
     *
     * @Template()
     */
    public function kernelsAction()
    {
        $metatitle = $this->get('translator')->trans('Statistics about the Linux kernels', array(), 'syw_front_main_stats_kernels');
        $title = $metatitle;
        $languages = $this->get('doctrine')
            ->getRepository('SywFrontMainBundle:Languages')
            ->findBy(array('active' => 1), array('language' => 'ASC'));

        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $this->getUser();
        } else {
            $user = null;
        }
        $stats = array();
        $stats['guess'] = $this->getGuessStats();

        $em = $this->getDoctrine()->getManager();
        $dql   = "SELECT a FROM SywFrontMainBundle:Kernels a WHERE a.machinesnum >= 1 ORDER BY a.machinesnum DESC, a.name ASC";
        $entitites_a = $em->createQuery($dql);
        $knpPaginator = $this->get('knp_paginator');
        $paginationAAA = $knpPaginator->paginate(
            $entitites_a,
            $this->get('request')->query->get('pageAAA', 1), // page number
            15, // limit per page
            array(
                'pageParameterName' => 'pageAAA',
                'sortFieldParameterName' => 'sortAAA',
                'sortDirectionParameterName' => 'directionAAA',
            )
        );

        $online = $this->getOnlineUsers();
        $return2 = $this->getTransForm($user);
        $return1 = array(
            'paginationAAA' => $paginationAAA,
            'online' => $online,
            'metatitle' => $metatitle,
            'metadescription' => $this->get('translator')->trans('Ever wanted to know which Linux kernel is the most used one. Here you get the answer.', array(), 'syw_front_main_main_index'),
            'title' => $title,
            'languages' => $languages,
            'user' => $user,
            'stats' => $stats
        );
        return array_merge($return1, $return2);
    }

    /**
     * @Route("/statistics/uptimes")
     * @Method("GET")
     *
     * @Template()
     */
    public function uptimesAction()
    {
        $metatitle = $this->get('translator')->trans('Statistics about the machine uptimes', array(), 'syw_front_main_stats_uptimes');
        $title = $metatitle;
        $languages = $this->get('doctrine')
            ->getRepository('SywFrontMainBundle:Languages')
            ->findBy(array('active' => 1), array('language' => 'ASC'));

        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $this->getUser();
        } else {
            $user = null;
        }
        $stats = array();
        $stats['guess'] = $this->getGuessStats();

        $em = $this->getDoctrine()->getManager();
        $dql   = "SELECT a FROM SywFrontMainBundle:Machines a WHERE a.uptime >= 1 ORDER BY a.uptime DESC";
        $entitites_a = $em->createQuery($dql);
        $knpPaginator = $this->get('knp_paginator');
        $paginationAAA = $knpPaginator->paginate(
            $entitites_a,
            $this->get('request')->query->get('pageAAA', 1), // page number
            15, // limit per page
            array(
                'pageParameterName' => 'pageAAA',
                'sortFieldParameterName' => 'sortAAA',
                'sortDirectionParameterName' => 'directionAAA',
            )
        );

        $online = $this->getOnlineUsers();
        $return2 = $this->getTransForm($user);
        $return1 = array(
            'paginationAAA' => $paginationAAA,
            'online' => $online,
            'metatitle' => $metatitle,
            'metadescription' => $this->get('translator')->trans('Ever wanted to know how long is the longest uptime. Here you get the answer.', array(), 'syw_front_main_main_index'),
            'title' => $title,
            'languages' => $languages,
            'user' => $user,
            'stats' => $stats
        );
        return array_merge($return1, $return2);
    }

    /**
     * @Route("/statistics/counter")
     * @Method("GET")
     *
     * @Template()
     */
    public function counterAction()
    {
        $metatitle = $this->get('translator')->trans('Statistics about the Linux Counter itself', array(), 'syw_front_main_stats_counter');
        $title1 = $metatitle;
        $title2 = $this->get('translator')->trans('Statistics about the registrations', array(), 'syw_front_main_stats_counter');
        $languages = $this->get('doctrine')
            ->getRepository('SywFrontMainBundle:Languages')
            ->findBy(array('active' => 1), array('language' => 'ASC'));

        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $this->getUser();
        } else {
            $user = null;
        }
        $stats = array();
        $stats['guess'] = $this->getGuessStats();

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

        $mostcity = $this->get('doctrine')
            ->getRepository('SywFrontMainBundle:Cities')
            ->findBy(
                array(),
                array('usernum' => 'DESC'),
                1,
                0
            );
        $stats['mostcity'] = $mostcity[0]->getName();
        $stats['cityusernum'] = $mostcity[0]->getUserNum();
        $code = $mostcity[0]->getIsoCountryCode();
        $country = $this->get('doctrine')
            ->getRepository('SywFrontMainBundle:Countries')
            ->findOneBy(array('code' => strtolower($code)));
        $stats['citycountry'] = $country->getName();

        // Chart about User registrations per Month
        unset($data1);
        $registrations = $this->get('doctrine')
            ->getRepository('SywFrontMainBundle:StatsMachines')
            ->findBy(array(), array('month' => 'ASC'));
        foreach ($registrations as $reg) {
            $y = $reg->getMonth()->format('Y');
            $m = $reg->getMonth()->format('m');
            $d = $reg->getMonth()->format('d');
            $data1[] = array(
                (($reg->getMonth()->format('U') + 86400) * 1000),
                $reg->getNum()
            );
        }
        unset($data2);
        $registrations = $this->get('doctrine')
            ->getRepository('SywFrontMainBundle:StatsRegistration')
            ->findBy(array(), array('month' => 'ASC'));
        foreach ($registrations as $reg) {
            $y = $reg->getMonth()->format('Y');
            $m = $reg->getMonth()->format('m');
            $d = $reg->getMonth()->format('d');
            $data2[] = array(
                (($reg->getMonth()->format('U') + 86400) * 1000),
                $reg->getNum()
            );
        }
        $series = array(
            array(
                "type" => "area",
                "name" => $this->get('translator')->trans('User Registrations', array(), 'syw_front_main_stats_counter'),
                "data" => $data2
            ),
            array(
                "type" => "area",
                "name" => $this->get('translator')->trans('Machine Registrations', array(), 'syw_front_main_stats_counter'),
                "data" => $data1
            )
        );
        $chart_registrations_per_month = new Highchart();
        $chart_registrations_per_month->chart->renderTo('chart_registrations_per_month');
        $chart_registrations_per_month->chart->zoomType('x');
        $chart_registrations_per_month->chart->type('line');
        $chart_registrations_per_month->title->text($this->get('translator')->trans('Registrations per month', array(), 'syw_front_main_stats_counter'));
        $chart_registrations_per_month->subtitle->text($this->get('translator')->trans('Click and drag in the plot area to zoom in', array(), 'syw_front_main_stats_counter'));
        $chart_registrations_per_month->xAxis->title(array('text'  => $this->get('translator')->trans('Date', array(), 'syw_front_main_stats_counter')));
        $chart_registrations_per_month->xAxis->type('datetime');
        $chart_registrations_per_month->xAxis->minRange(14 * 24 * 3600000 * 30); // 14 Monate
        $chart_registrations_per_month->yAxis->min(0);
        $chart_registrations_per_month->yAxis->title(array('text'  => $this->get('translator')->trans('Registrations per month', array(), 'syw_front_main_stats_counter')));
        $chart_registrations_per_month->legend->enabled(true);
        $chart_registrations_per_month->plotOptions->area(array(
            'allowPointSelect'  => true,
            'dataLabels'    => array('enabled' => false),
            'showInLegend'  => true
        ));
        $chart_registrations_per_month->series($series);
        // end of chart

        $online = $this->getOnlineUsers();
        $return2 = $this->getTransForm($user);
        $return1 = array(
            'online' => $online,
            'metatitle' => $metatitle,
            'metadescription' => $this->get('translator')->trans('These are statistics around the Linux Counter itself.', array(), 'syw_front_main_main_index'),
            'title1' => $title1,
            'title2' => $title2,
            'languages' => $languages,
            'stats' => $stats,
            'user' => $user,
            'chart' => $chart_registrations_per_month
        );
        return array_merge($return1, $return2);
    }
}
