<?php

namespace Syw\Front\MainBundle\Listener;

use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Syw\Front\MainBundle\Entity\Activity;
use Syw\Front\MainBundle\Util\DetectBotFromUserAgent;

/**
 * Class Activity
 *
 * @category Listener
 * @package  SywFrontMainBundle
 * @author   Alexander Löhner <alex.loehner@linux.com>
 * @license  GPL v3
 * @link     https://github.com/alexloehner/linuxcounter.new
 */
class ActivityListener
{
    protected $context;
    protected $em;
    protected $container;

    public function __construct(ContainerInterface $container, SecurityContext $context, Doctrine $doctrine)
    {
        $this->context = $context;
        $this->em = $doctrine->getManager();
        $this->container = $container;
    }

    /**
     * On each request we want to update the user's last activity datetime
     *
     * @param \Symfony\Component\HttpKernel\Event\FilterControllerEvent $event
     * @return void
     */
    public function onCoreController(FilterControllerEvent $event)
    {
        if (true === isset($this->context) && true === is_object($this->context)) {
            if (true === is_object($this->context->getToken()) && $this->context->getToken() != null) {
                $user = $this->context->getToken()->getUser();
            }
        }
        if (false === isset($user) || false === is_object($user) || $user == null) {
            $user = null;
        }
        $route  = $event->getRequest()->attributes->get('_route');
        if ($route == null || true === in_array($route, array('_wdt'))) {
            return true;
        }
        $ipaddress = $this->container->get('request')->server->get("REMOTE_ADDR");
        $useragent = $this->container->get('request')->server->get("HTTP_USER_AGENT");
        $obj       = new DetectBotFromUserAgent();
        $isbot     = $obj->licoIsBot($useragent, $ipaddress);

        $activity = new Activity();
        $activity->setUser($user);
        $activity->setRoute($route);

        $activity->setIpAddress($ipaddress);
        $activity->setUserAgent($useragent);
        $activity->setIsBot($isbot);

        $activity->setCreatedAt(new \DateTime());
        $this->em->persist($activity);
        $this->em->flush();
    }
}
