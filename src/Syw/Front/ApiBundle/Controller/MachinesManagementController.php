<?php

namespace Syw\Front\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\View;

use Symfony\Component\Form\FormError;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

use Syw\Front\ApiBundle\Controller\BaseRestController;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

use Syw\Front\MainBundle\Entity\Machines;

class MachinesManagementController extends BaseRestController
{
    /**
     * @Route("/v1/machines", name="api_create_machine")
     * @Method("POST")
     * @Rest\View()
     */
    public function createAction(Request $request)
    {
        $aResponse = array();
        $apikey = $request->headers->get('x-lico-apikey');
        if (false === isset($apikey) || trim($apikey) == "" || strlen($apikey) != 32 || false === preg_match("`^([a-z0-9]{32})$`", $apikey)) {
            $response = new JsonResponse($aResponse);
            $response->setStatusCode(400);
        } else {
            $apiaccess = $this->get('doctrine')
                ->getRepository('SywFrontApiBundle:ApiAccess')
                ->findOneBy(array('apikey' => $apikey));
            if (false === isset($apiaccess) || false === is_object($apiaccess) || $apiaccess == null) {
                $response = new JsonResponse($aResponse);
                $response->setStatusCode(404);
            } else {
                $user = $apiaccess->getUser();
                $em = $this->getDoctrine()->getManager();
                $machine = new Machines();
                $machine->setCreatedAt(new \DateTime());
                $machine->setUser($user);
                $updateKey = $password = mt_rand(10000000, 99999999);
                $machine->setUpdateKey($updateKey);
                $em->persist($machine);
                $em->flush();
                $machine_id =  $machine->getId();
                $aResponse = array(
                    'machine_id' => $machine_id,
                    'machine_updatekey' => $updateKey
                );
                $response = new JsonResponse($aResponse);
                $response->setStatusCode(200);
            }
        }
        return $response;
    }
}
