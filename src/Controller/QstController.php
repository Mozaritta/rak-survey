<?php

namespace App\Controller;

use App\Entity\Form;
use App\Entity\Questions;
use App\Entity\Survey;
use App\Form\FormType;
use App\Form\QuestionsType;
use App\Form\SurveyType;
use App\Repository\FormRepository;
use App\Repository\QuestionsRepository;
use App\Repository\SurveyRepository;
use Doctrine\ORM\EntityManagerInterface;
// use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class QstController extends AbstractController
{
    /**
     * @Route("/valid", name="valid_qst", methods="GET")
     */
    public function valid(
        QuestionsRepository $qstRepository,
        SurveyRepository $surveyRepository,
        AuthenticationUtils $authenticationUtils
        // ,PaginatorInterface $paginator,
        // Request $request
    ): Response {
        if ($authenticationUtils->getLastUsername()) {
            $srvs = $surveyRepository->findBy([], ['createdAt' => 'DESC']);
            $qsts = $qstRepository->findBy([], ['createdAt' => 'DESC']);
            // $data = $surveyRepository->findAll();
            // $test = $paginator->paginate(
            //     $data,
            //     /**query Not result */
            //     $request->query->getInt('page', 1),
            //     /**page number */
            //     2
            //     /**limit per page */
            // );
            return $this->render('qst/index.html.twig', compact('qsts', 'srvs')); //, 'test'));

        } else {
            dump($authenticationUtils->getLastUsername());
            return $this->render('qst/valid.html.twig');
        }
    }
    ////// START QST CRUD /////////////////////////

    /**
     * @Route("/", name="app_home", methods="GET")
     * @Route("/qst/app_home")
     */
    public function index(AuthenticationUtils $authenticationUtils, QuestionsRepository $qstRepository): Response
    {
        if ($authenticationUtils->getLastUsername()) {
            return $this->redirectToRoute('valid_qst');
        } else {
            dump($authenticationUtils->getLastUsername());
            return $this->render('qst/valid.html.twig');
        }
    }

    /**
     * @Route("/add", name="add_qst", methods="GET|POST")
     */
    public function addQst(AuthenticationUtils $authenticationUtils, Request $request, EntityManagerInterface $em): Response
    {
        if ($authenticationUtils->getLastUsername()) {
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
        } else {
            return $this->render('qst/valid.html.twig');
        }
    }

    /**
     * @Route("/updateQst/{id<[0-9]+>}", name="update_qst")
     */
    public function updateQst(AuthenticationUtils $authenticationUtils, Request $request, int $id, QuestionsRepository $qstRepository, EntityManagerInterface $em): Response
    {
        if ($authenticationUtils->getLastUsername()) {
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
        } else {
            return $this->render('qst/valid.html.twig');
        }
    }

    /**
     * @Route("/deleteQst/{id<[0-9]+>}", name="delete_qst")
     */
    public function deleteQst(AuthenticationUtils $authenticationUtils, QuestionsRepository $qstRepository, int $id, EntityManagerInterface $em): Response
    {
        if ($authenticationUtils->getLastUsername()) {
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
        } else {
            return $this->render('qst/valid.html.twig');
        }
    }

    /**
     * @Route("/validateQst/{id<[0-9]+>}", name="validate_qst")
     */
    public function validateQst(AuthenticationUtils $authenticationUtils, QuestionsRepository $qstRepository, int $id, EntityManagerInterface $em): Response
    {
        if ($authenticationUtils->getLastUsername()) {
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
        } else {
            return $this->render('qst/valid.html.twig');
        }
    }

    /**
     * @Route("/check", name="app_check")
     */
    public function check(AuthenticationUtils $authenticationUtils, QuestionsRepository $qstRepository): Response
    {
        if ($authenticationUtils->getLastUsername()) {
            $qsts = $qstRepository->findBy([], ['createdAt' => 'DESC']);
            return $this->render('qst/check.html.twig', compact('qsts'));
        } else {
            return $this->render('qst/valid.html.twig');
        }
    }

    ////// END QST CRUD /////////////////////////
    ////// SURVEY CRUD /////////////////////////
    /**
     * @Route("/create", name="add_survey", methods="GET|POST")
     */
    public function createSurvey(AuthenticationUtils $authenticationUtils, Request $request, EntityManagerInterface $em): Response
    {
        if ($authenticationUtils->getLastUsername()) {
            $survey = new Survey;
            $form = $this->createForm(SurveyType::class, $survey);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($survey);
                $em->flush();
                $this->addFlash(
                    'primary',
                    'Survey created successfully'
                );
                return $this->redirectToRoute('show_form');
            }
            return $this->render('survey/create.html.twig', [
                'form' => $form->createView()
            ]);
        } else {
            return $this->render('qst/valid.html.twig');
        }
    }

    /**
     * @Route("/view/{id<[0-9]+>}", name="view_survey")
     */
    public function viewSurvey(AuthenticationUtils $authenticationUtils, Request $request, int $id, QuestionsRepository $qstRepository, SurveyRepository $surveyRepository): Response
    {
        if ($authenticationUtils->getLastUsername()) {
            $qsts = $qstRepository->findBy(['survey' => $id], ['createdAt' => 'DESC']);
            $srv = $surveyRepository->findOneBy(['id' => $id]);
            return $this->render('survey/view.html.twig', compact('qsts', 'srv'));
        } else {
            return $this->render('qst/valid.html.twig');
        }
    }

    /**
     * @Route("/survey", name="show_surveys")
     */
    public function showSurveys(AuthenticationUtils $authenticationUtils, SurveyRepository $srvRepository, FormRepository $formRepository): Response
    {
        if ($authenticationUtils->getLastUsername()) {
            $srv = $srvRepository->findBy([], ['createdAt' => 'DESC']);
            $frm = $formRepository->findBy([], ['createdAt' => 'DESC']);
            return $this->render('survey/show.html.twig', compact('srv', 'frm'));
        } else {
            return $this->render('qst/valid.html.twig');
        }
    }

    /**
     * @Route("/update/{id<[0-9]+>}", name="update_survey"))
     */
    public function updateSurvey(AuthenticationUtils $authenticationUtils, Request $request, int $id, SurveyRepository $srvRepository, EntityManagerInterface $em): Response
    {
        if ($authenticationUtils->getLastUsername()) {
            $srv = $srvRepository->findOneBy(['id' => $id]);
            $form = $this->createForm(SurveyType::class, $srv);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em->flush();
                $this->addFlash(
                    'success',
                    'Survey updated successfully'
                );
                return $this->redirectToRoute('show_surveys');
            }
            return $this->render('survey/update.html.twig', [
                'srv' => $srv,
                'form' => $form->createView()
            ]);
        } else {
            return $this->render('qst/valid.html.twig');
        }
    }

    /**
     * @Route("/delete/{id<[0-9]+>}", name="delete_survey")
     */
    public function deleteSurvey(AuthenticationUtils $authenticationUtils, SurveyRepository $srvRepository, int $id, EntityManagerInterface $em): Response
    {
        if ($authenticationUtils->getLastUsername()) {
            $srv = $srvRepository->findOneBy(['id' => $id]);
            $em->remove($srv);
            $em->flush();
            $message = 'Survey deleted successfully';
            $this->addFlash(
                'danger',
                $message
            );
            // }
            return $this->redirectToRoute('app_home');
        } else {
            return $this->render('qst/valid.html.twig');
        }
    }

    /**
     * @Route("/delete/from/{id<[0-9]+>}", name="delete_from_survey")
     */
    public function deleteFromSurvey(AuthenticationUtils $authenticationUtils, SurveyRepository $surveyRepository, QuestionsRepository $qstRepository, int $id, EntityManagerInterface $em): Response
    {
        if ($authenticationUtils->getLastUsername()) {
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
            return $this->redirectToRoute('show_surveys');
        } else {
            return $this->render('qst/valid.html.twig');
        }
    }

    /**
     * @Route("/insert/{idd<[0-9]+>}/{id<[0-9]+>}", name="insert_qst")
     */
    public function insertIntoSurvey(AuthenticationUtils $authenticationUtils, int $idd, SurveyRepository $surveyRepository, QuestionsRepository $qstRepository, int $id, EntityManagerInterface $em): Response
    {
        if ($authenticationUtils->getLastUsername()) {
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
        } else {
            return $this->render('qst/valid.html.twig');
        }
    }
    ////// END SURVEY CRUD /////////////////////////
    ////// FORM CRUD /////////////////////////

    /**
     * @Route("/new}", name="add_form")
     */
    public function createFormulaire(AuthenticationUtils $authenticationUtils, Request $request, EntityManagerInterface $em): Response
    {
        if ($authenticationUtils->getLastUsername()) {
            $formm = new Form;
            $form = $this->createForm(FormType::class, $formm);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($formm);
                $em->flush();
                $this->addFlash(
                    'primary',
                    'Form created successfully'
                );
                return $this->redirectToRoute('show_forms');
            }
            return $this->render('survey/create.html.twig', [
                'form' => $form->createView()
            ]);
        } else {
            return $this->render('qst/valid.html.twig');
        }
    }

    /**
     * @Route("/form/view/{id<[0-9]+>}", name="view_form")
     */
    public function viewForm(AuthenticationUtils $authenticationUtils, Request $request, int $id, FormRepository $formRepository, SurveyRepository $srvRepository): Response
    {
        if ($authenticationUtils->getLastUsername()) {
            $srvs = $srvRepository->findBy(['form' => $id], ['createdAt' => 'DESC']);
            $frm = $formRepository->findOneBy(['id' => $id]);
            return $this->render('form/view.html.twig', compact('srvs', 'frm'));
        } else {
            return $this->render('qst/valid.html.twig');
        }
    }

    /**
     * @Route("/forms", name="show_forms")
     */
    public function showForms(AuthenticationUtils $authenticationUtils, FormRepository $formRepository): Response
    {
        if ($authenticationUtils->getLastUsername()) {
            $frm = $formRepository->findBy([], ['createdAt' => 'DESC']);
            return $this->render('form/show.html.twig', compact('frm'));
        } else {
            return $this->render('qst/valid.html.twig');
        }
    }

    /**
     * @Route("/form/update/{id<[0-9]+>}", name="update_form"))
     */
    public function updateForm(AuthenticationUtils $authenticationUtils, Request $request, int $id, FormRepository $formRepository, EntityManagerInterface $em): Response
    {
        if ($authenticationUtils->getLastUsername()) {
            $frm = $formRepository->findOneBy(['id' => $id]);
            $form = $this->createForm(FormType::class, $frm);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em->flush();
                $this->addFlash(
                    'success',
                    'Form updated successfully'
                );
                return $this->redirectToRoute('show_forms');
            }
            return $this->render('form/update.html.twig', [
                'frm' => $frm,
                'form' => $form->createView()
            ]);
        } else {
            return $this->render('qst/valid.html.twig');
        }
    }

    /**
     * @Route("/form/delete/{id<[0-9]+>}", name="delete_form")
     */
    public function deleteForm(AuthenticationUtils $authenticationUtils, FormRepository $formRepository, int $id, EntityManagerInterface $em): Response
    {
        if ($authenticationUtils->getLastUsername()) {
            $frm = $formRepository->findOneBy(['id' => $id]);
            $em->remove($frm);
            $em->flush();
            $message = 'Form deleted successfully';
            $this->addFlash(
                'danger',
                $message
            );
            // }
            return $this->redirectToRoute('app_home');
        } else {
            return $this->render('qst/valid.html.twig');
        }
    }

    /**
     * @Route("/form/delete/from/{id<[0-9]+>}", name="delete_from_form")
     */
    public function deleteFromForm(AuthenticationUtils $authenticationUtils, SurveyRepository $surveyRepository, FormRepository $formRepository, int $id, EntityManagerInterface $em): Response
    {
        if ($authenticationUtils->getLastUsername()) {
            $srv = $surveyRepository->findOneBy(['id' => $id]);
            $frm = $formRepository->findOneBy(['id' => $srv->getForm()]);
            $frm->removeSurvey($srv);
            $em->flush();
            $message = 'Survey deleted from form successfully';
            $this->addFlash(
                'success',
                $message
            );
            // }
            return $this->redirectToRoute('show_forms');
        } else {
            return $this->render('qst/valid.html.twig');
        }
    }

    /**
     * @Route("/form/insert/{idd<[0-9]+>}/{id<[0-9]+>}", name="insert_srv")
     */
    public function insertIntoForm(AuthenticationUtils $authenticationUtils, int $idd, SurveyRepository $surveyRepository, FormRepository $formRepository, int $id, EntityManagerInterface $em): Response
    {
        if ($authenticationUtils->getLastUsername()) {
            // dd($idd, $id);
            $srv = $surveyRepository->findOneBy(['id' => $idd]);
            $frm = $formRepository->findOneBy(['id' => $id]);
            $frm->addSurvey($srv);
            $em->flush();
            $message = 'Survey added to form successfully';
            $this->addFlash(
                'primary',
                $message
            );
            return $this->redirectToRoute('app_home');
        } else {
            return $this->render('qst/valid.html.twig');
        }
    }
    ////// END FORM CRUD /////////////////////////

    ////// ANSWER THE FORM  /////////////////////////

    /**
     * @Route("/answer", name="answer_form")
     */
    public function answerForm(AuthenticationUtils $authenticationUtils, FormRepository $formRepository, QuestionsRepository $qstRepository, SurveyRepository $surveyRepository): Response
    {
        if ($authenticationUtils->getLastUsername()) {
            $frm = $formRepository->findOneBy(['id' => 1]);
            // dd($frm->getSurvey());
            $srvs = $surveyRepository->findBy(['form' => !null]);
            for ($i = 0; $i < 2; $i++) {
                $qsts[$i] = $qstRepository->findBy(['survey' => $srvs[$i]->getId()]);
            }
            return $this->render('client/form.html.twig', compact('frm', 'srvs', 'qsts'));
        } else {
            return $this->render('qst/valid.html.twig');
        }
    }
}