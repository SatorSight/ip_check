<?php

namespace AppBundle\Controller;

use AppBundle\Entity\IPMask;
use AppBundle\Entity\Operator;
use AppBundle\Resources\SUtils;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Ipmask controller.
 *
 * @Route("/")
 */
class IPMaskController extends Controller
{
    /**
     * Lists all iPMask entities.
     *
     * @Route("/", name="admin_ipmasks_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $iPMasks = $em->getRepository('AppBundle:IPMask')->findAll();
        $operators = $em->getRepository('AppBundle:Operator')->findAll();

        return $this->render('ipmask/index.html.twig', array(
            'iPMasks' => $iPMasks,
            'operators' => $operators
        ));
    }

    /**
     * Creates a new iPMask entity.
     *
     * @Route("/new", name="admin_ipmasks_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $iPMask = new Ipmask();
        $form = $this->createForm('AppBundle\Form\IPMaskType', $iPMask);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($iPMask);
            $em->flush();

            return $this->redirectToRoute('admin_ipmasks_show', array('id' => $iPMask->getId()));
        }

        return $this->render('ipmask/new.html.twig', array(
            'iPMask' => $iPMask,
            'form' => $form->createView(),
        ));
    }


    /**
     * Creates a new iPMask entity.
     *
     * @Route("/load", name="admin_ipmasks_load")
     * @Method({"GET", "POST"})
     */
    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $operators = $em->getRepository('AppBundle:Operator')->findAll();

        if($request->request->get('operator')){

            $dir = realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR;
            $uploads_dir = $dir.'/web/tmp';

            $true_path = '';

            if ($_FILES["file"]["error"] == UPLOAD_ERR_OK) {
                $tmp_name = $_FILES["file"]["tmp_name"];
                $name = time().basename($_FILES["file"]["name"]);

                $true_path = $uploads_dir.'/'.str_replace(' ','_',$name);
                move_uploaded_file($tmp_name, $true_path);
            }

            if(empty($true_path)) die('Failed to load file');

            $array = array_map('str_getcsv', file($true_path));
            array_shift($array);

            foreach ($array as $key => $row){
                $vals = $row[0];
                $vals = explode(';',$vals);

                $ipMask = new IPMask();
                $ipMask->setOperator($em->getRepository('AppBundle:Operator')->findOneBy(['id' => $request->request->get('operator')]));
                $ipMask->setMacroRegion($vals[0]);
                $ipMask->setRegion($vals[1]);
                $ipMask->setMask($vals[2]);

                if(!empty($ipMask->getMask()))
                    $em->persist($ipMask);
            }


            $em->flush();

            return $this->redirectToRoute('admin_ipmasks_index');
        }

        return $this->render('ipmask/load.html.twig', array(
            'operators' => $operators,
        ));
    }

    /**
     * Finds and displays a iPMask entity.
     *
     * @Route("/{id}", name="admin_ipmasks_show")
     * @Method("GET")
     */
    public function showAction(IPMask $iPMask)
    {
        $deleteForm = $this->createDeleteForm($iPMask);

        return $this->render('ipmask/show.html.twig', array(
            'iPMask' => $iPMask,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing iPMask entity.
     *
     * @Route("/{id}/edit", name="admin_ipmasks_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, IPMask $iPMask)
    {
        $deleteForm = $this->createDeleteForm($iPMask);
        $editForm = $this->createForm('AppBundle\Form\IPMaskType', $iPMask);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_ipmasks_edit', array('id' => $iPMask->getId()));
        }

        return $this->render('ipmask/edit.html.twig', array(
            'iPMask' => $iPMask,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a iPMask entity.
     *
     * @Route("/{id}", name="admin_ipmasks_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, IPMask $iPMask)
    {
        $form = $this->createDeleteForm($iPMask);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($iPMask);
            $em->flush();
        }

        return $this->redirectToRoute('admin_ipmasks_index');
    }

    /**
     * Deletes a iPMask entities by operator.
     *
     * @Route("/delete_op/{id}", name="admin_ipmasks_delete_op")
     * @Method({"GET", "POST"})
     */
    public function deleteOpAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();

        /** @var Operator $operator */
        $operator = $em->getRepository('AppBundle:Operator')->findOneBy(['id' => $id]);
        $masks = $operator->getIpMasks();

        foreach ($masks as $mask)
            $em->remove($mask);

        $em->flush();

        return $this->redirectToRoute('admin_ipmasks_index');
    }

    /**
     * Creates a form to delete a iPMask entity.
     *
     * @param IPMask $iPMask The iPMask entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(IPMask $iPMask)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_ipmasks_delete', array('id' => $iPMask->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
