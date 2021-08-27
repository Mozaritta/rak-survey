<?php

namespace App\Controller;

use App\Entity\Questions;
use App\Entity\Survey;
use App\Form\QuestionsType;
use App\Form\SurveyType;
use App\Repository\QuestionsRepository;
use App\Repository\SurveyRepository;
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
    public function index(QuestionsRepository $qstRepository, SurveyRepository $surveyRepository): Response
    {
        $srvs = $surveyRepository->findBy([], ['createdAt' => 'DESC']);
        $qsts = $qstRepository->findBy([], ['createdAt' => 'DESC']);
        return $this->render('qst/index.html.twig', compact('qsts', 'srvs'));
    }
    ////// START QST CRUD /////////////////////////

    /**
     * @Route("/valid", name="valid_qst", methods="GET")
     */
    public function valid(QuestionsRepository $qstRepository): Response
    {
        $qsts = $qstRepository->findBy([], ['createdAt' => 'DESC']);
        return $this->render('qst/valid.html.twig', compact('qsts'));
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
     * @Route("/updateQst/{id<[0-9]+>}", name="update_qst")
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
            if ($qst->getValid() == false) {
                return $this->redirectToRoute('app_check');
            } else {
                return $this->redirectToRoute('app_home');
            }
        }
        return $this->render('qst/edit.html.twig', [
            'qst' => $qst,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/deleteQst/{id<[0-9]+>}", name="delete_qst")
     */
    public function deleteQst(QuestionsRepository $qstRepository, int $id, EntityManagerInterface $em): Response
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
     * @Route("/validateQst/{id<[0-9]+>}", name="validate_qst")
     */
    public function validateQst(QuestionsRepository $qstRepository, int $id, EntityManagerInterface $em): Response
    {
        $qst = $qstRepository->findOneBy(['id' => $id]);
        $qst->setValid(true);
        $em->flush();
        $message = 'Question validated successfully';
        $this->addFlash(
            'primary',
            $message
        );
        // }
        return $this->redirectToRoute('app_check');
    }

    /**
     * @Route("/check", name="app_check")
     */
    public function check(QuestionsRepository $qstRepository): Response
    {
        $qsts = $qstRepository->findBy([], ['createdAt' => 'DESC']);
        return $this->render('qst/check.html.twig', compact('qsts'));
    }

    ////// END QST CRUD /////////////////////////
    ////// SURVEY CRUD /////////////////////////
    /**
     * @Route("/create", name="add_form", methods="GET|POST")
     */
    public function createSurvey(Request $request, EntityManagerInterface $em): Response
    {
        $survey = new Survey;
        $form = $this->createForm(SurveyType::class, $survey);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($survey);
            $em->flush();
            $this->addFlash(
                'primary',
                'Form created successfully'
            );
            return $this->redirectToRoute('show_form');
        }
        return $this->render('survey/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/view/{id<[0-9]+>}", name="view_form")
     */
    public function viewSurvey(Request $request, int $id, QuestionsRepository $qstRepository, SurveyRepository $surveyRepository): Response
    {
        $qsts = $qstRepository->findBy(['survey' => $id], ['createdAt' => 'DESC']);
        $srv = $surveyRepository->findOneBy(['id' => $id]);
        return $this->render('survey/view.html.twig', compact('qsts', 'srv'));
    }

    /**
     * @Route("/survey", name="show_form")
     */
    public function showSurvey(SurveyRepository $srvRepository): Response
    {
        $srv = $srvRepository->findBy([], ['createdAt' => 'DESC']);
        return $this->render('survey/show.html.twig', compact('srv'));
    }

    /**
     * @Route("/update/{id<[0-9]+>}", name="update_form"))
     */
    public function updateSurvey(Request $request, int $id, SurveyRepository $srvRepository, EntityManagerInterface $em): Response
    {
        $srv = $srvRepository->findOneBy(['id' => $id]);

        $form = $this->createForm(SurveyType::class, $srv);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash(
                'success',
                'Survey updated successfully'
            );
            return $this->redirectToRoute('show_form');
        }
        return $this->render('survey/update.html.twig', [
            'srv' => $srv,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id<[0-9]+>}", name="delete_form")
     */
    public function deleteSurvey(QuestionsRepository $qstRepository, int $id, EntityManagerInterface $em): Response
    {
        $qst = $qstRepository->findOneBy(['id' => $id]);
        $em->remove($qst);
        $em->flush();
        $message = 'Survey deleted successfully';
        $this->addFlash(
            'danger',
            $message
        );
        // }
        return $this->redirectToRoute('app_home');
    }

    /**
     * @Route("/delete/from/{id<[0-9]+>}", name="delete_from_form")
     */
    public function deleteFromSurvey(SurveyRepository $surveyRepository, QuestionsRepository $qstRepository, int $id, EntityManagerInterface $em): Response
    {
        $qst = $qstRepository->findOneBy(['id' => $id]);
        $srv = $surveyRepository->findOneBy(['id' => $qst->getSurvey()]);
        $srv->removeQuestion($qst);
        $em->flush();
        $message = 'Question deleted from survey successfully';
        $this->addFlash(
            'success',
            $message
        );
        // }
        return $this->redirectToRoute('show_form');
    }

    /**
     * @Route("/insert/{idd<[0-9]+>}/{id<[0-9]+>}", name="insert_qst")
     */
    public function insertIntoSurvey(int $idd, SurveyRepository $surveyRepository, QuestionsRepository $qstRepository, int $id, EntityManagerInterface $em): Response
    {
        // dd($idd, $id);
        $qst = $qstRepository->findOneBy(['id' => $id]);
        $srv = $surveyRepository->findOneBy(['id' => $idd]);
        $srv->addQuestion($qst);
        $em->flush();
        $message = 'Question added to survey successfully';
        $this->addFlash(
            'primary',
            $message
        );
        return $this->redirectToRoute('app_home');
    }
}