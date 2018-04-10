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

    const ALIASES = [
        'mts' => 'mts.ru',
        'beeline' => 'beeline.ru',
        'megafon' => 'megafon.ru'
    ];

    const UPPER_ALIASES = [
        'mts' => 'MTS',
        'beeline' => 'Beeline',
        'megafon' => 'MegaFon'
    ];


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

    /**
     * @Route("/get_op_masks/{op}", name="get_op_masks")
     * @Method("GET")
     */
    public function getAllOpMasksAction($op){
        $em = $this->getDoctrine()->getManager();


        $op = strtolower($op);

        /** @var Operator $operator */
        $operator = $em->getRepository(Operator::class)->findOneBy(['tech' => self::ALIASES[$op]]);
        $masks = $operator->getIpMasks()->toArray();

        $ip_masks = array_map(function($mask){
            /** @var IPMask $mask */
            return $mask->getMask();
        }, $masks);

        return new JsonResponse($ip_masks);
    }

    /**
     * @Route("/get_mask_op/{mask}", name="get_mask_op")
     * @Method("GET")
     */
    public function getMaskOpAction($mask){
        $em = $this->getDoctrine()->getManager();
        //contains dots
        $mask = SUtils::full_url_decode($mask);

        /** @var IPMask $ip_mask */
        $ip_mask = $em->getRepository(IPMask::class)->findOneBy(['mask' => $mask]);
        $operator = $ip_mask->getOperator();
        $tech = $operator->getTech();

        $op = array_search($tech, self::ALIASES);
        $op = self::UPPER_ALIASES[$op];

        return new JsonResponse($op);
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