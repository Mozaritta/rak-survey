<?php

namespace App\Controller;

use App\Entity\Questions;
use App\Repository\QuestionsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}