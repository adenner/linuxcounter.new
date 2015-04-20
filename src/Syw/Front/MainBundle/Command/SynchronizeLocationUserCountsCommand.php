<?php

namespace Syw\Front\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use FOS\UserBundle\Model\User;
use Syw\Front\MainBundle\Entity\StatsMachines;

/**
 *
 */
class SynchronizeLocationUserCountsCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('syw:synchronize:usercounts')
            ->setDescription('Synchronizes the user counts in the cities and countries tables')
            ->setDefinition(array())
            ->setHelp(<<<EOT
The <info>syw:synchronize:usercounts</info> command Synchronizes the user counts in the cities and countries tables.

EOT
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $db = $this->getContainer()->get('doctrine')->getManager();

        $c = 0;
        $cities = $db->getRepository('SywFrontMainBundle:Cities')->findAll();
        foreach ($cities as $city) {
            $c++;
            $userProfiles = null;
            unset($userProfiles);
            $userProfiles = $city->getUsers();
            $city->setUserNum(sizeof($userProfiles));
            $db->persist($city);
            if ($c == 100) {
                echo ".";
                $c = 0;
            }
        }
        $db->flush();

        $countries = $db->getRepository('SywFrontMainBundle:Countries')->findAll();
        foreach ($countries as $country) {
            $userProfiles = null;
            unset($userProfiles);
            $userProfiles = $country->getUsers();
            $country->setUsersNum(sizeof($userProfiles));
            $db->persist($country);
            echo ".";
        }
        $db->flush();
        echo "\n";
        $output->writeln(sprintf('Usernumbers synchronized', ''));
    }

    /**
     * @see Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {

    }
}
