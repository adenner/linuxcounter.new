<?php

/*
 * This file is part of the AsmTranslationLoaderBundle package.
 *
 * (c) Marc Aschmann <maschmann@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Syw\Front\MainBundle\Command;

use Asm\TranslationLoaderBundle\Command\BaseTranslationCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;

/**
 * Class UpdateTranslationsCommand
 *
 * @package Asm\TranslationLoaderBundle\Command
 * @author  marc aschmann <maschmann@gmail.com>
 * @uses    Symfony\Component\Console\Input\InputArgument
 * @uses    Symfony\Component\Console\Input\InputInterface
 * @uses    Symfony\Component\Console\Input\InputOption
 * @uses    Symfony\Component\Console\Output\OutputInterface
 * @uses    Symfony\Component\Translation\Catalogue\DiffOperation
 * @uses    Symfony\Component\Translation\Catalogue\MergeOperation
 * @uses    Symfony\Component\Translation\MessageCatalogue
 * @uses    Symfony\Component\Finder\Finder
 * @uses    Asm\TranslationLoaderBundle\Entity\Translation
 */
class UpdateTranslationsCommand extends BaseTranslationCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('syw:update:translations')
            ->setDescription('Updates Translations from templates into database');
    }


    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $db = $this->getContainer()->get('doctrine.dbal.default_connection');

        $bundles = array('SywFrontApiBundle', 'SywFrontMainBundle', 'SywFrontNewsBundle');
        $rows    = $db->fetchAll('SELECT l.locale FROM languages l ORDER BY l.locale ASC');
        $locales = array();
        foreach ($rows as $row) {
            $locales[] = $row['locale'];
        }

        foreach ($bundles as $bundle) {
            foreach ($locales as $locale) {
                @passthru('php app/console translation:update --prefix "" --force ' . $locale . ' ' . $bundle . '');
            }
        }
        @passthru('php app/console syw:new:translations:to:db');


        $output->writeln('<comment>finished!</comment>');
    }
}
