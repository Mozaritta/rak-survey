<?php

namespace App\Controller;

use App\Entity\Answers;
use App\Entity\Remark;
use App\Form\RemarkType;
use App\Entity\Questions;
use App\Form\QuestionsType;
use App\Repository\FormRepository;
use App\Repository\TypeRepository;
use App\Repository\SectionRepository;
use App\Repository\QuestionsRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\NotificationRepository;
use App\Repository\RemarkRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class QstController extends AbstractController
{
    ////// START QST CRUD /////////////////////////

    /**
     * @Route("/", name="app_home", methods="GET")
     */
    public function index(AuthenticationUtils $authenticationUtils, NotificationRepository $notificationRepository): Response
    {
        if ($authenticationUtils->getLastUsername()) {
            $notification = $notificationRepository->findAll();
            $notif = 0;
            for ($i = 0; $i < sizeof($notification); $i++) {
                // dd($notification[0]->getUser()->getId());
                if ($notification[$i]->getUser()->getId() == $this->getUser()->getId()) {
                    // dd($notif);
                    $notif = 1;
                }
            }
            // dd($notification[0]->getUserId()->getId());
            // dd($this->getUser());
            // if ($this->getUser()->getAnswered() == true) {
            //     $notif = 0;
            // }
            return $this->render('client/home.html.twig', [
                'notif' => $notif,
                'notification' => $notification
            ]);
        } else {
            return $this->render('anonymous/first.html.twig');
        }
    }


    ///////////////////notifications to answer client

    //////end

    /**
     * @Route("/add", name="add_qst", methods="GET|POST")
     */
    public function addQst(
        Request $request,
        EntityManagerInterface $em,
        TypeRepository $typeRepository,
        AuthenticationUtils $authenticationUtils
    ): Response {
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

    ////// ANSWER THE FORM  /////////////////////////

    /**
     * @Route("/answer/{idd<[0-9]+>}", name="answer_form") // should send the id of the specific form to answer
     */
    public function answerForm(
        int $idd,
        UserRepository $userRepository,
        FormRepository $formRepository,
        QuestionsRepository $qstRepository,
        SectionRepository $sectionRepository,
        AuthenticationUtils $authenticationUtils
    ): Response {
        if ($authenticationUtils->getLastUsername()) {

            $frm = $formRepository->findOneBy(['id' => $idd]);
            $sections = $sectionRepository->findBy(['form' => $idd]);
            for ($i = 0; $i < 1; $i++) {
                $qsts[$i] = $qstRepository->findBy(['section' => $sections[$i]->getId()]);
            }
            if (sizeof($qsts[0]) == 0) {
                $this->addFlash('danger', 'No questions in the section');
            }
            $error = $authenticationUtils->getLastAuthenticationError();
            return $this->render('client/form.html.twig', compact('frm', 'sections', 'qsts', 'error'));
        } else {
            return $this->render('anonymous/first.html.twig');
        }
    }

    /**
     * @Route("/answer/result", name="set_answer")
     */
    public function setAnswer(
        Request $request,
        EntityManagerInterface $em,
        EntityManagerInterface $em1,
        UserRepository $userRepository,
        QuestionsRepository $qstRepository,
        AuthenticationUtils $authenticationUtils
    ): Response {
        if ($authenticationUtils->getLastUsername()) {
            if ($this->getUser()->getAnswered() == 1) {
                $this->addFlash(
                    'info',
                    'You have already answered the form'
                );
                return $this->redirectToRoute('remark');
            } else {
                $user = $this->getUser()->getId();
                // dd($user);
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
                    $answer = new Answers;
                    $question = $qstRepository->findOneBy(['id' => $id]);
                    $answer->setQuestion($question);
                    $user = $userRepository->findOneBy(['id' => $user]);
                    $answer->setClient($user);
                    $answer->setAnswer($name[$i]);
                    $em1->persist($answer);
                    $em1->flush();

                    $this->getUser()->setAnswered(true);
                    $em->persist($this->getUser());
                    $em->flush();
                    // dd($this->getUser());
                    $this->addFlash(
                        'success',
                        'You answers wer stocked successfully'
                    );
                }
                // dd($name);
                // return $this->render('test.html.twig', compact('name'));
                return $this->redirectToRoute('remark');
            }
            // } else {
            //     return $this->redirectToRoute('app_home');
            // }
        } else {
            return $this->render('anonymous/first.html.twig');
        }
    }
    /**
     * @Route("/remark", name="remark")
     */
    public function remark(
        Request $request,
        AuthenticationUtils $authenticationUtils,
        EntityManagerInterface $em,
        RemarkRepository $remarkRepository
    ): Response {
        if ($authenticationUtils->getLastUsername()) {
            $remark = new Remark;
            if ($this->getUser()->getAnswered() == 1) {
                $remarks = $remarkRepository->findOneBy(['user' => $this->getUser()]);
                if (empty($remarks)) {
                    $remark->setUser($this->getUser());
                    $form = $this->createForm(RemarkType::class, $remark);
                    $form->handleRequest($request);

                    if ($form->isSubmitted() && $form->isValid()) {
                        $em->persist($remark);
                        $em->flush();
                        $this->addFlash(
                            'success',
                            'Remark added successfully'
                        );
                        return $this->redirectToRoute('app_home');
                    }
                    return $this->render('client/note.html.twig', [
                        'form' => $form->createView()
                    ]);
                } else {
                    $this->addFlash(
                        'primary',
                        'You ave already set your remark! Thanks'
                    );
                    return $this->redirectToRoute('app_home');
                }
            } else {
                $this->addFlash(
                    'danger',
                    'You still haven\'t answered your survey'
                );
                return $this->redirectToRoute('answer_form');
            }
        } else {
            return $this->render('anonymous/first.html.twig');
        }
    }
}