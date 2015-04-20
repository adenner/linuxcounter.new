<?php

namespace Syw\Front\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use FOS\UserBundle\Model\User;

/**
 *
 */
class ModifyUserCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('syw:user:modify')
            ->setDescription('Delete a user.')
            ->setDefinition(array(
                new InputArgument('number', InputArgument::REQUIRED, 'A counter number'),
                new InputArgument('action', InputArgument::REQUIRED, 'Either "search" or a setFunction name from User entity.'),
                new InputArgument('value', InputArgument::OPTIONAL, 'Only when action is a setFunction name, then this is the value for the field to set')
            ))
            ->setHelp(<<<EOT
The <info>syw:user:modify</info> command modifies a user:

  <info>php app/console syw:user:modify {userid} search</info>
  <info>php app/console syw:user:modify {userid} setEmail foo@bar.com</info>

EOT
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $licotest   = $this->getContainer()->get('doctrine')->getManager();
        $licotestdb = $this->getContainer()->get('doctrine.dbal.default_connection');

        $userid   = $input->getArgument('number');
        $action   = $input->getArgument('action');

        if ($action == "search") {
            $user = $em->getRepository('SywFrontMainBundle:User')->findOneBy(array("id" => $userid));
            if (true === isset($user) && is_object($user)) {
                $output->writeln(sprintf('User with ID <comment>%s</comment> found. His email actually is <comment>%s</comment>', $userid, $user->getEmail()));
            }
        } else {
            $user = $em->getRepository('SywFrontMainBundle:User')->findOneBy(array("id" => $userid));
            if (true === isset($user) && is_object($user)) {
                $value   = $input->getArgument('value');
                $user->$action($value);
                $em->persist($user);
                $em->flush();
                $getaction = preg_replace("`^s(.*)`", "g$1", $action);
                $output->writeln(sprintf('User with ID <comment>%s</comment> successfully modified! The new value is <comment>%s</comment>', $userid, $user->$getaction()));
            } else {
                $output->writeln(sprintf('User with ID <comment>%s</comment> NOT found!', $userid));
            }
        }
    }

    /**
     * @see Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('number')) {
            $number = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please set a number:',
                function ($number) {
                    if (empty($number)) {
                        throw new \Exception('Number can not be empty');
                    }

                    return $number;
                }
            );
            $input->setArgument('number', $number);
        }
        if (!$input->getArgument('action')) {
            $action = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a action:',
                function ($action) {
                    if (empty($action)) {
                        throw new \Exception('action can not be empty');
                    }

                    return $action;
                }
            );
            $input->setArgument('action', $action);
        }
    }
}
