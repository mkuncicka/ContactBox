<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Person;
use AppBundle\Form\PersonType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class PersonController extends Controller
{
    /**
     * @Route("/new")
     * @Template()
     */
    public function newAction(Request $request)
    {
        $person = new Person();

        $form = $this->createForm(new PersonType(), $person);

        $form->add('Dodaj', 'submit');

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($person);
            $em->flush();

            return $this->redirectToRoute('app_person_showall');
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/{id}/modify")
     * @Template()
     */
    public function modifyAction(Request $request, $id)
    {
        $person = $this->getDoctrine()->getRepository("AppBundle:Person")->find($id);

        if (!$person) {
            throw $this->createNotFoundException("Nie znaleziono osoby");
        }

        $form = $this->createForm(new PersonType(), $person);

        $form->add('Zapisz', 'submit');

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_person_showall');
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/{id}/delete")
     * @Template()
     */
    public function deleteAction($id)
    {
        $person = $this->getDoctrine()->getRepository("AppBundle:Person")->find($id);
        $em = $this->getDoctrine()->getManager();

        if (!$person) {
            throw $this->createNotFoundException("Nie znaleziono osoby");
        }
        $em->remove($person);
        $em->flush();

        return $this->redirectToRoute('app_person_delete');
    }

    /**
     * @Route("/{id}")
     * @Template()
     */
    public function showAction(Request $request, $id)
    {
        $person = $this->getDoctrine()->getRepository("AppBundle:Person")->find($id);

        if (!$person) {
            throw $this->createNotFoundException("Nie znaleziono osoby");
        }

        return ['person' => $person];
    }

    /**
     * @Route("/")
     * @Template()
     */
    public function showAllAction()
    {
        $people = $this->getDoctrine()->getRepository("AppBundle:Person")->findAll();

        if (!$people) {
            throw $this->createNotFoundException("Błąd wyszukiwania osób z bazy");
        }

        return ['people' => $people];
    }
}

