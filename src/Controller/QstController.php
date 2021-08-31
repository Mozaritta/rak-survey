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


    ////// END QST CRUD /////////////////////////
    ////// SURVEY CRUD /////////////////////////

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


    ////// END SURVEY CRUD /////////////////////////
    ////// FORM CRUD /////////////////////////

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


    ////// END FORM CRUD /////////////////////////

    ////// ANSWER THE FORM  /////////////////////////

    /**
     * @Route("/answer", name="answer_form")
     */
    public function answerForm(AuthenticationUtils $authenticationUtils, FormRepository $formRepository, QuestionsRepository $qstRepository, SurveyRepository $surveyRepository): Response
    {
        if ($authenticationUtils->getLastUsername()) {
            $frm = $formRepository->findOneBy(['id' => 3]);
            // dd($frm->getSurvey());
            $srvs = $surveyRepository->findBy(['form' => 3]);
            // dd($srvs);
            for ($i = 0; $i < 3; $i++) {
                $qsts[$i] = $qstRepository->findBy(['survey' => $srvs[$i]->getId()]);
            }
            $error = $authenticationUtils->getLastAuthenticationError();
            return $this->render('client/form.html.twig', compact('frm', 'srvs', 'qsts', 'error'));
        } else {
            return $this->render('qst/valid.html.twig');
        }
    }

    /**
     * @Route("/result", name="set_answer")
     */
    public function setAnswer(QuestionsRepository $qstRepository, Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        if ($authenticationUtils->getLastUsername()) {
            $user = $this->getUser()->getId();
            // if (isset($_POST['answers'])) {
            $qst = $qstRepository->findAll();
            for ($i = 0; $i < sizeof($qst); $i++) {
                $id = $qst[$i]->getId();
                // dd("check$id");
                $name[$i] = $request->query->get("check$id");
                if ($name[$i] == null) {
                    $name[$i] = 'No';
                }
                if (is_array($name[$i])) {
                    $name[$i] = 'Yes';
                }
                // dd($name[$i]);
                $connection = mysqli_connect("localhost", "root", "", "raksurvey");
                if (!$connection) {
                    die("Connection failed: " . mysqli_connect_error());
                }
                $query = "INSERT INTO answers (question_id, client_id,answer) VALUES ('$id', '$user', '$name[$i]')";
                if (mysqli_query($connection, $query)) {
                    echo "New record created successfully";
                } else {
                    echo "Error: " . $query . "<br>" . mysqli_error($connection);
                }

                mysqli_close($connection);
            }
            // dd($name);
            return $this->render('test.html.twig', compact('name'));
            // } else {
            //     return $this->redirectToRoute('app_home');
            // }
        } else {
            return $this->redirectToRoute('app_home');
        }
    }
}