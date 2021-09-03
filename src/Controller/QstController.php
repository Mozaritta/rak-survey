<?php

namespace App\Controller;

use App\Entity\Form;
use App\Entity\Questions;
use App\Form\QuestionsType;
use App\Repository\FormRepository;
use App\Repository\QuestionsRepository;
use App\Repository\SectionRepository;
use App\Repository\TypeRepository;
use Doctrine\ORM\EntityManagerInterface;
// use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class QstController extends AbstractController
{
    ////// START QST CRUD /////////////////////////

    /**
     * @Route("/", name="app_home", methods="GET")
     * @Route("/qst/app_home")
     */
    public function index(AuthenticationUtils $authenticationUtils, QuestionsRepository $qstRepository): Response
    {
        if ($authenticationUtils->getLastUsername()) {
            if ($this->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('home');
            } else if ($this->isGranted('ROLE_USER')) {
                return $this->render('client/home.html.twig');
            }
        } else {
            return $this->render('anonymous/first.html.twig');
        }
    }

    /**
     * @Route("/add", name="add_qst", methods="GET|POST")
     */
    public function addQst(AuthenticationUtils $authenticationUtils, TypeRepository $typeRepository, Request $request, EntityManagerInterface $em): Response
    {
        if ($authenticationUtils->getLastUsername()) {
            $question = new Questions;
            // if ($this->isGranted('ROLE_USER')) {
            //     $type = $typeRepository->findOneBy(['name' => "TextField"]);
            //     $question->setType($type);
            //     $question->setValid(false);
            //     $question->setUser($this->getUser());
            //     $form = $this->createFormBuilder($question)
            //         ->add('description', TextareaType::class)
            //         ->add('valid', CheckboxType::class, ['value' => false, 'disabled' => true])
            //         ->getForm();
            //     $form->handleRequest(($request));
            // } else
            if ($this->isGranted('ROLE_ADMIN')) {
                $form = $this->createForm(QuestionsType::class, $question);
                $form->handleRequest($request);
            }
            if ($form->isSubmitted() && $form->isValid()) {
                $question->setUser($this->getUser());
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
            return $this->render('anonymous/first.html.twig');
        }
    }


    ////// END QST CRUD /////////////////////////
    ////// FORM CRUD /////////////////////////




    ////// END FORM CRUD /////////////////////////

    ////// ANSWER THE FORM  /////////////////////////

    /**
     * @Route("/answer", name="answer_form") // should send the id of the specific form to answer
     */
    public function answerForm(AuthenticationUtils $authenticationUtils, FormRepository $formRepository, QuestionsRepository $qstRepository, SectionRepository $sectionRepository): Response
    {
        if ($authenticationUtils->getLastUsername()) {
            $frm = $formRepository->findOneBy(['id' => 3]);
            // dd($frm->getSection());
            $sections = $sectionRepository->findBy(['form' => 3]);
            // dd($srvs);
            for ($i = 0; $i < 1; $i++) {
                $qsts[$i] = $qstRepository->findBy(['section' => $sections[$i]->getId()]);
            }
            // dd(sizeof($qsts[0]));
            if (sizeof($qsts[0]) == 0) {
                $this->addFlash('danger', 'No questions in the section');
            }
            // foreach ($srvs as $test) {
            //     $qsts = $qstRepository->findOneBy(['section' => $test->getId()]);
            // }
            // dd($qsts);
            $error = $authenticationUtils->getLastAuthenticationError();
            return $this->render('client/form.html.twig', compact('frm', 'sections', 'qsts', 'error'));
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
                    $this->addFlash(
                        'success',
                        'You answers wer stocked successfully'
                    );
                } else {
                    $this->addFlash(
                        'danger',
                        mysqli_error($connection)
                    );
                    // echo "Error: " . $query . "<br>" . mysqli_error($connection);
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