<?php

namespace Syw\Front\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use FOS\UserBundle\Model\User;
use Syw\Front\MainBundle\Entity\StatsRegistration;

/**
 *
 */
class CorrectMachinesDistributionsCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('syw:correct:distributions')
            ->setDescription('Re-adds the profile id to the user table')
            ->setDefinition(array())
            ->setHelp(<<<EOT
The <info>syw:correct:distributions</info> command corrects the distributions.

EOT
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $db = $this->getContainer()->get('doctrine')->getManager();

        $importlogfile = "import.correct.machines.distributions";

        $distributions = $db->getRepository('SywFrontMainBundle:Distributions')->findAll();
        foreach ($distributions as $distribution) {
            $distribution->setMachinesNum(count($distribution->getMachines()));
            $db->persist($distribution);
        }
        $db->flush();
    }

    /**
     * @see Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {

    }
}
