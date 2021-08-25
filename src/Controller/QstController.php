<?php

namespace App\Controller;

use App\Entity\Questions;
use App\Form\QuestionsType;
use App\Repository\QuestionsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QstController extends AbstractController
{
    /**
     * @Route("/", name="app_home", methods="GET")
     * @Route("/qst/app_home")
     */
    public function index(QuestionsRepository $qstRepository): Response
    {
        $qsts = $qstRepository->findBy([], ['createdAt' => 'DESC']);
        return $this->render('qst/index.html.twig', compact('qsts'));
    }

    /**
     * @Route("/add", name="add_qst", methods="GET|POST")
     */
    public function addQst(Request $request, EntityManagerInterface $em): Response
    {
        $question = new Questions;
        $form = $this->createForm(QuestionsType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($question);
            $em->flush();
            $this->addFlash(
                'primary',
                'Question added successfully'
            );
            return $this->redirectToRoute('app_home');
        }
        return $this->render('qst/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/qstup/{id<[0-9]+>}", name="update_qst")
     */
    public function updateQst(Request $request, int $id, QuestionsRepository $qstRepository, EntityManagerInterface $em): Response
    {
        $qst = $qstRepository->findOneBy(['id' => $id]);
        $form = $this->createForm(QuestionsType::class, $qst);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Question updated successfully'
            );
            return $this->redirectToRoute('app_home');
        }
        return $this->render('qst/edit.html.twig', [
            'qst' => $qst,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("delete{id<[0-9]+>}", name="delete_qst")
     */
    public function delete(Request $request, QuestionsRepository $qstRepository, int $id, EntityManagerInterface $em): Response
    {
        $qst = $qstRepository->findOneBy(['id' => $id]);
        $em->remove($qst);
        $em->flush();
        $message = 'Question deleted successfully';
        $this->addFlash(
            'danger',
            $message
        );
        // }
        return $this->redirectToRoute('app_home');
    }


    /**
     * @Route("/check", name="app_check")
     */
    public function check(QuestionsRepository $qstRepository): Response
    {
        $qsts = $qstRepository->findBy([], ['createdAt' => 'DESC']);
        return $this->render('qst/check.html.twig', compact('qsts'));
    }
}