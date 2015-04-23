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
class ImportDistributionsCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('syw:import:distributions')
            ->setDescription('')
            ->setDefinition(array())
            ->setHelp(<<<EOT
The <info>syw:import:distributions</info> command imports the distributions.

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
        $db = $this->getContainer()->get('doctrine')->getManager();

        $importlogfile = "import.distributions";

        $filetoimport = "/srv/test.linuxcounter.net/distributions.html";

        $content = file_get_contents($filetoimport);

        $matches = array();
        preg_match_all("`\s*\<p\>\s*(\<li\>\s*.*\s*\<\/li\>)\s*\<\/p\>\s*`isU", $content, $matches);

        foreach ($matches[1] as $match) {
            $url            = preg_replace("`.*\<b\>\s*\<a\s*href=\"([^\"]+)\"[^\>]*\>\s*([^\<]+)\s*\<\/a\>\s*\<\/b\>\s*.*`s", "$1", trim($match));
            $name           = preg_replace("`.*\<b\>\s*\<a\s*href=\"([^\"]+)\"[^\>]*\>\s*([^\<]+)\s*\<\/a\>\s*\<\/b\>\s*.*`s", "$2", trim($match));
            $description    = preg_replace("`.*\<\/a\>.?\<br\>\s*([^\<]+)\s*\<\/li\>.*`sU", "$1", trim($match));
            $url            = trim(str_replace("\n", " ", $url));
            $name           = trim(str_replace("\n", " ", $name));

            if (strlen($name) >= 150 || strlen($url) >= 150) {
                // something went wrong... the source seems to be not correctly formatted.
                $url        = preg_replace("`.*\<b\>\s*\<a\s*href=\"([^\"]+)\"[^\>]*\>\s*\<\/a\>\s*([^\<]+)\s*\<\/b\>\s*.*`s", "$1", trim($match));
                $name       = preg_replace("`.*\<b\>\s*\<a\s*href=\"([^\"]+)\"[^\>]*\>\s*\<\/a\>\s*([^\<]+)\s*\<\/b\>\s*.*`s", "$2", trim($match));
                $url        = trim(str_replace("\n", " ", $url));
                $name       = trim(str_replace("\n", " ", $name));
            }


            echo " - $name | $url \n";
            echo "$description \n=============================================================================\n";




            if ($name == "libreCMC") {
                exit;
            }



        }

    }

    /**
     * @see Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {

    }
}
