<?php

namespace Syw\Front\MainBundle\Profiler;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;

/**
 * Class SuperAdminMatcher
 *
 * @category Profiler
 * @package  SywFrontMainBundle
 * @author   Alexander Löhner <alex.loehner@linux.com>
 * @license  GPL v3
 * @link     https://github.com/alexloehner/linuxcounter.new
 */
class SuperAdminMatcher implements RequestMatcherInterface
{
    protected $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public function matches(Request $request)
    {
        return $this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN');
    }
}
