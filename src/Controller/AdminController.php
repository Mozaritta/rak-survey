<?php

namespace App\Controller;

use App\Entity\Form;
use App\Entity\Survey;
use App\Repository\FormRepository;
use App\Repository\QuestionsRepository;
use App\Repository\SurveyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    ////// QUESTION CRUD /////////////////////////

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

    ////// END QUESTION CRUD /////////////////////////
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
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

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
            $srv = $surveyRepository->findOneBy(['id' => $id]);
            $frm = $formRepository->findOneBy(['id' => $idd]);
            // dd($id);
            $frm->addSurvey($srv);
            $em->flush();
            $message = 'Survey added to form successfully';
            $this->addFlash(
                'primary',
                $message
            );
            return $this->redirectToRoute('show_surveys');
        } else {
            return $this->render('qst/valid.html.twig');
        }
    }
    ////// END FORM CRUD /////////////////////////
}