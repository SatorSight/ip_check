<?php

namespace AppBundle\Controller;

use AppBundle\Entity\IPMask;
use AppBundle\Entity\Operator;
use AppBundle\Helpers\IPHelper;
use AppBundle\Resources\SUtils;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/api")
 */
class ApiController extends Controller
{
    /**
     * @Route("/get_op/{ip}", name="get_op_by_ip")
     * @Method("GET")
     */
    public function indexAction($ip)
    {
        $resp = new \stdClass();
        $resp->operator = 'unknown';

        /** @var IPMask $mask */
        $mask = $this->getMask($ip);
        if($mask)
            $resp->operator = $mask->getOperator()->getTech();

        return new JsonResponse($resp);
    }

    public function getMask($ip){
        $em = $this->getDoctrine()->getManager();
        $masks = $em->getRepository(IPMask::class)->findAll();

        $masks_arr = [];
        /** @var IPMask $mask */
        foreach($masks as $mask)
            $masks_arr[$mask->getId()] = $mask->getMask();

        /** @var IPMask $maskFound */
        $maskFound = null;
        foreach ($masks_arr as $key => $mask){
            if(IPHelper::match($ip, $mask)) {
                $maskFound = $em->getRepository(IPMask::class)->findOneBy(['id' => $key]);
                break;
            }
        }

        if(!$maskFound) return false;
        return $maskFound;
    }

}