<?php

namespace Syw\Front\MainBundle\Entity;

use Asm\TranslationLoaderBundle\Model\Translation as BaseTranslation;
use Doctrine\ORM\Mapping as ORM;

/**
 * Translation entity class for the Doctrine ORM storage layer implementation.
 *
 * @ORM\Table(name="translation", indexes={@ORM\Index(name="gettranslations", columns={"trans_locale", "message_domain"})})
 * @ORM\Entity
 *
 */
class Translation extends BaseTranslation
{
}
