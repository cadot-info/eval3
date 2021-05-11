<?php

namespace App\Controller;

use App\Entity\Crypto;
use App\Form\CryptoType;
use App\Repository\CryptoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/crypto")
 */
class CryptoController extends AbstractController
{
    /**
     * @Route("/", name="crypto_index", methods={"GET"})
     */
    public function index(CryptoRepository $cryptoRepository): Response
    {
        return $this->render('crypto/index.html.twig', [
            'cryptos' => $cryptoRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="crypto_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $crypto = new Crypto();
        $form = $this->createForm(CryptoType::class, $crypto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($crypto);
            $entityManager->flush();

            return $this->redirectToRoute('crypto_index');
        }

        return $this->render('crypto/new.html.twig', [
            'crypto' => $crypto,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="crypto_show", methods={"GET"})
     */
    public function show(Crypto $crypto): Response
    {
        return $this->render('crypto/show.html.twig', [
            'crypto' => $crypto,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="crypto_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Crypto $crypto): Response
    {
        $form = $this->createForm(CryptoType::class, $crypto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('crypto_index');
        }

        return $this->render('crypto/edit.html.twig', [
            'crypto' => $crypto,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="crypto_delete", methods={"POST"})
     */
    public function delete(Request $request, Crypto $crypto): Response
    {
        if ($this->isCsrfTokenValid('delete'.$crypto->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($crypto);
            $entityManager->flush();
        }

        return $this->redirectToRoute('crypto_index');
    }
}
