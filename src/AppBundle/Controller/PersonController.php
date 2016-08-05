<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Address;
use AppBundle\Entity\Person;
use AppBundle\Form\AddressType;
use AppBundle\Form\PersonType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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

            return $this->redirectToRoute('app_person_show', ['id' => $person->getId(), 'person' => $person]);
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
        }

        $address = new Address();
        $addressForm = $this->createForm(new AddressType(), $address);
        $addressForm->add('Dodaj adres', 'submit');
        $addressForm->handleRequest($request);

        if ($addressForm->isValid()) {
            $address->setPerson($person);
            $em = $this->getDoctrine()->getManager();
            $em->persist($address);
            $em->flush();

            return $this->redirectToRoute('app_person_modify', ['id' => $person->getId(), 'person' => $person]);
        }

        return ['form' => $form->createView(),
            'addressForm' => $addressForm->createView(),
            'person' => $person];
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

        return $this->redirectToRoute('app_person_showall');
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
        $people = $this->getDoctrine()->getRepository("AppBundle:Person")->findBy(array(),array('lastName' => 'ASC'));;

        if (!$people) {
            throw $this->createNotFoundException("Błąd wyszukiwania osób z bazy");
        }

        return ['people' => $people];
    }

}

//Dodaj do widoku formularz (przypisany do adresu) który będzie odsyłał do
//strony POST /{id}/addAddress
//2. Dodaj akcję która obsłuży formularz.
