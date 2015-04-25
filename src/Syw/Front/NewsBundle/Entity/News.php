<?php

namespace Syw\Front\NewsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use BladeTester\LightNewsBundle\Entity\News as BaseNews;
use Eko\FeedBundle\Item\Writer\RoutedItemInterface;

/**
 * Class News
 *
 * @category Entity
 * @package  SywFrontNewsBundle
 * @author   Alexander Löhner <alex.loehner@linux.com>
 * @license  GPL v3
 * @link     https://github.com/alexloehner/linuxcounter.new
 *
 * @ORM\Entity()
 * @ORM\Table(name="news")
 */
class News extends BaseNews implements RoutedItemInterface
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    public function getId()
    {
        return $this->id;
    }

    public function getFeedItemTitle()
    {
        return $this->title;
    }

    public function getFeedItemDescription()
    {
        if (strlen($this->body) >= 400) {
            $body = mb_substr($this->body, 0, 400)."...";
        } else {
            $body = $this->body;
        }
        return $body;
    }

    public function getFeedItemPubDate()
    {
        return $this->createdAt;
    }

    public function getFeedItemRouteName()
    {
        return 'news_view';
    }

    public function getFeedItemRouteParameters()
    {
        return array('id' => $this->id);
    }

    public function getFeedItemUrlAnchor()
    {
        return '';
    }
}
