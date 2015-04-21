<?php

namespace Syw\Front\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Model\UserInterface;
use Syw\Front\MainBundle\Entity\UserProfile;

/**
 * Class ImportUserCountriesCommand
 *
 * @category Command
 * @package  SywFrontMainBundle
 * @author   Alexander Löhner <alex.loehner@linux.com>
 * @license  GPL v3
 * @link     https://github.com/alexloehner/linuxcounter.new
 */
class ImportUserCountriesCommand extends ContainerAwareCommand
{

    public $container;

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('syw:import:usercountries')
            ->setDescription('Imports data from lico into licotest')
            ->setHelp(<<<EOT
The <info>syw:import:usercountries</info> imports stuff from lico into licotest

EOT
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lico       = $this->getContainer()->get('doctrine.dbal.lico_connection');
        $licotest   = $this->getContainer()->get('doctrine')->getManager();
        $licotestdb = $this->getContainer()->get('doctrine.dbal.default_connection');

        gc_collect_cycles();

        $countries = $licotest->getRepository('SywFrontMainBundle:Countries')->findAll();

        foreach ($countries as $country) {
            $code = strtoupper($country->getCode());
            echo "> ".$code.", ".$country->getName()." \n";
            $rows = $lico->fetchAll("SELECT p.f_key FROM persons p WHERE country = '".$code."'");
            $c = 0;
            foreach ($rows as $row) {
                $user = null;
                unset($user);
                $user = $licotest->getRepository('SywFrontMainBundle:User')->findOneBy(array("id" => $row['f_key']));
                if (true === isset($user) && true === is_object($user)) {
                    $profile = null;
                    unset($profile);
                    $profile = $user->getProfile();
                    if (true === isset($profile) && true === is_object($profile)) {
                        $usercountry = $profile->getCountry();
                        if (false === isset($usercountry) || false === is_object($usercountry) || $usercountry == null) {
                            echo ".";
                            $c++;
                            $country->setUsersNum($country->getUsersNum() + 1);
                            $licotest->persist($country);
                            $profile->setCountry($country);
                            $licotest->persist($profile);
                        }
                    }
                }
                $licotest->flush();
                gc_collect_cycles();
            }
            echo "\n";
        }

        $licotest->clear();
        $licotest->close();
        $licotestdb->close();
        $lico->close();

        $licotest = null;
        unset($licotest);
        $licotestdb = null;
        unset($licotestdb);
        $lico = null;
        unset($lico);

        gc_collect_cycles();
        exit(0);
    }

    /**
     * @see Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {

    }
}
