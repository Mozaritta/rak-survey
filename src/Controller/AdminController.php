<?php

namespace App\Controller;

use App\Entity\Form;
use App\Entity\Notification;
use App\Entity\Type;
use App\Form\FormType;
use App\Entity\Section;
use App\Entity\Questions;
use App\Form\SectionType;
use App\Form\QuestionsType;
use App\Repository\TypeRepository;
use App\Repository\FormRepository;
use App\Repository\NotificationRepository;
use App\Repository\SectionRepository;
use App\Repository\QuestionsRepository;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function homeAdmin(AuthenticationUtils $authenticationUtils, UserRepository $userRepository, RoleRepository $roleRepository, PaginatorInterface $paginatorInterface, Request $request): Response
    {
        if ($authenticationUtils->getLastUsername()) {
            if ($this->isGranted('ROLE_ADMIN')) {
                $users = $userRepository->findBy([], ['id' => 'ASC']);
                $role = $roleRepository->findBy(['name' => 'Client']);
                $r = ($role[0]->getId());
                $j = 1;
                // dd($user);
                for ($i = 0; $i < sizeof($users); $i++) {
                    $roles[$i] = ($users[0]->getRole());
                    if ($users[$i]->getRole()[0] == null) {
                        $id = $users[$i]->getId();
                        /////////
                        $connection = mysqli_connect("localhost", "root", "", "raksurvey");
                        if (!$connection) {
                            die("Connection failed: " . mysqli_connect_error());
                        }
                        $query = "INSERT INTO user_role (user_id, role_id) VALUES ('$id', '$r')"; // affecter à n'importe quel user sans role le role ROLE_USER
                        if (mysqli_query($connection, $query)) {
                            echo "Added yes";
                        } else {
                            echo "Error: " . $query . "<br>" . mysqli_error($connection);
                        }

                        mysqli_close($connection);
                        // dd($clients);
                        $j++;
                    } else {
                        continue;
                    }
                }
                $pagination = $paginatorInterface->paginate(
                    $users,
                    $request->query->getInt('page', 1),
                    10
                );
                return $this->render('admin/home.html.twig', compact('pagination'));
            } else {
                return $this->redirectToRoute('app_home');
            }
        } else {
            return $this->render('anonymous/first.html.twig');
        }
    }
    /// tell the client to answer th form ///

    /**
     * @Route("/getNotif{idd<[0-9]+>}/{id<[0-9]+>}", name="get_notif")
     */
    public function getNotification(
        int $idd,
        int $id,
        FormRepository $formRepository,
        UserRepository $userRepository,
        EntityManagerInterface $em,
        AuthenticationUtils $authenticationUtils
    ): Response {
        if ($authenticationUtils->getLastUsername()) {
            $notif = 1;
            $notification = new Notification;
            $form = $formRepository->findOneBy(['id' => $id]);
            $user = $userRepository->findOneBy(['id' => $idd]);
            $notification->setUser($user);
            $notification->setForm($form);
            $notification->setToDo('Answer this form');
            $em->persist($notification);
            $em->flush();

            return $this->redirectToRoute('list_client');
        } else {
            return $this->render('anonymous/first.html.twig');
        }
    }
    /////// end //////

    ///// questions without sections

    /**
     * @Route("/orphan_qst", name="orphan_qst")
     */
    public function orphanQst(AuthenticationUtils $authenticationUtils, QuestionsRepository $qstRepository, PaginatorInterface $paginatorInterface, Request $request): Response
    {
        if ($authenticationUtils->getLastUsername()) {
            if ($this->isGranted('ROLE_ADMIN')) {

                $orphan = $qstRepository->findBy(['section' => null], ['createdAt' => 'DESC']);
                $pagination = $paginatorInterface->paginate(
                    $orphan,
                    $request->query->getInt('page', 1),
                    4
                );
                return $this->render('qst/orphan.html.twig', compact('orphan', 'pagination'));
            } else if ($this->isGranted('ROLE_USER')) {
                return $this->redirectToRoute('app_home');
            }
        } else {
            return $this->render('anonymous/first.html.twig');
        }
    }

    ///////////////////////end
    /**
     * @Route("/list", name="list_client")
     */
    public function listClient(AuthenticationUtils $authenticationUtils, FormRepository $formRepository, UserRepository $userRepository, RoleRepository $roleRepository, PaginatorInterface $paginatorInterface, Request $request): Response
    {
        $forms = $formRepository->findAll();
        if ($authenticationUtils->getLastUsername()) {
            if ($this->isGranted('ROLE_ADMIN')) {
                $user = $userRepository->findBy([], ['id' => 'ASC']);
                $role = $roleRepository->findBy(['name' => 'Client']);
                $r = ($role[0]->getId());
                $j = 1;
                $clients = [];
                // dd($user);
                for ($i = 0; $i < sizeof($user); $i++) {
                    $roles[$i] = ($user[0]->getRole());
                    if ($user[$i]->getRole()[0] == null) {
                        $id = $user[$i]->getId();
                        /////////
                        $connection = mysqli_connect("localhost", "root", "", "raksurvey");
                        if (!$connection) {
                            die("Connection failed: " . mysqli_connect_error());
                        }
                        $query = "INSERT INTO user_role (user_id, role_id) VALUES ('$id', '$r')"; // affecter à n'importe quel user sans role le role ROLE_USER
                        if (mysqli_query($connection, $query)) {
                            echo "Added yes";
                        } else {
                            echo "Error: " . $query . "<br>" . mysqli_error($connection);
                        }

                        mysqli_close($connection);
                        $clients[$j] = $user[$i];
                        // dd($clients);
                        $j++;
                    } else if ($user[$i]->getRoles()[0] == 'ROLE_USER') {
                        $clients[$j] = $user[$i];
                        // dd($clients);
                        $j++;
                    } else {
                        continue;
                    }
                    $pagination = $paginatorInterface->paginate(
                        $clients,
                        $request->query->getInt('page', 1),
                        10
                    );
                }
                // dd($clients);
                return $this->render('admin/list.html.twig', compact('pagination', 'forms'));
            } else {
                return $this->redirectToRoute('app_home');
            }
        } else {
            return $this->render('anonymous/first.html.twig');
        }
    }
    /// from user to admin
    /**
     * @Route("/change/{id<[0-9]+>}", name="set_admin")
     */
    public function setAsAdmin(int $id, AuthenticationUtils $authenticationUtils, RoleRepository $roleRepository): Response
    {
        if ($authenticationUtils->getLastUsername()) {
            if ($this->isGranted('ROLE_ADMIN')) {
                $role = $roleRepository->findBy(['name' => 'Admin']);
                $r = ($role[0]->getId());
                $connection = mysqli_connect("localhost", "root", "", "raksurvey");
                if (!$connection) {
                    die("Connection failed: " . mysqli_connect_error());
                }
                // dd($r);
                $query = "UPDATE user_role SET role_id = $r WHERE user_id = $id"; // affecter à n'importe quel user sans role le role ROLE_USER
                if (mysqli_query($connection, $query)) {
                    $this->addFlash(
                        'success',
                        'User set to admin successfully'
                    );
                } else {
                    $this->addFlash(
                        'danger',
                        mysqli_error($connection)
                    );
                }
                mysqli_close($connection);
                return $this->redirectToRoute('list_client');
            } else {
                return $this->redirectToRoute('app_home');
            }
        } else {
            return $this->render('anonymous/first.html.twig');
        }
    }

    //// end ////
    ////// QUESTION CRUD /////////////////////////
    /**
     * @Route("/valid", name="valid_qst", methods="GET")
     */
    public function valid(
        QuestionsRepository $qstRepository,
        SectionRepository $sectionRepository,
        AuthenticationUtils $authenticationUtils,
        PaginatorInterface $paginatorInterface,
        Request $request
    ): Response {
        if ($authenticationUtils->getLastUsername()) {
            if ($this->isGranted('ROLE_ADMIN')) {
                $sections = $sectionRepository->findBy([], ['createdAt' => 'DESC']);
                $questions = $qstRepository->findBy([], ['createdAt' => 'DESC']);
                $pagination = $paginatorInterface->paginate(
                    $questions,
                    $request->query->getInt('page', 1),
                    4
                );
                return $this->render('qst/index.html.twig', compact('questions', 'sections', 'pagination'));
            } else {
                return $this->redirectToRoute('app_home');
            }
        } else {
            return $this->render('anonymous/first.html.twig');
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
            return $this->render('anonymous/first.html.twig');
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
            return $this->render('anonymous/first.html.twig');
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
            return $this->render('anonymous/first.html.twig');
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
            return $this->render('anonymous/first.html.twig');
        }
    }

    ////// END QUESTION CRUD /////////////////////////
    ////// SECTION CRUD /////////////////////////
    /**
     * @Route("/create", name="add_section", methods="GET|POST")
     */
    public function createSection(AuthenticationUtils $authenticationUtils, Request $request, EntityManagerInterface $em): Response
    {
        if ($authenticationUtils->getLastUsername()) {
            $section = new Section;
            $form = $this->createForm(SectionType::class, $section);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($section);
                $em->flush();
                $this->addFlash(
                    'primary',
                    'Section created successfully'
                );
                return $this->redirectToRoute('show_sections');
            }
            return $this->render('section/create.html.twig', [
                'form' => $form->createView()
            ]);
        } else {
            return $this->render('anonymous/first.html.twig');
        }
    }


    /**
     * @Route("/update/{id<[0-9]+>}", name="update_section"))
     */
    public function updateSection(AuthenticationUtils $authenticationUtils, Request $request, int $id, SectionRepository $secRepository, EntityManagerInterface $em): Response
    {
        if ($authenticationUtils->getLastUsername()) {
            $sec = $secRepository->findOneBy(['id' => $id]);
            $form = $this->createForm(SectionType::class, $sec);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em->flush();
                $this->addFlash(
                    'success',
                    'Section updated successfully'
                );
                return $this->redirectToRoute('show_sections');
            }
            return $this->render('section/update.html.twig', [
                'sec' => $sec,
                'form' => $form->createView()
            ]);
        } else {
            return $this->render('anonymous/first.html.twig');
        }
    }

    /**
     * @Route("/delete/{id<[0-9]+>}", name="delete_section")
     */
    public function deleteSection(AuthenticationUtils $authenticationUtils, SectionRepository $secRepository, int $id, EntityManagerInterface $em): Response
    {
        if ($authenticationUtils->getLastUsername()) {
            $sec = $secRepository->findOneBy(['id' => $id]);
            $em->remove($sec);
            $em->flush();
            $message = 'Section deleted successfully';
            $this->addFlash(
                'danger',
                $message
            );
            // }
            return $this->redirectToRoute('app_home');
        } else {
            return $this->render('anonymous/first.html.twig');
        }
    }

    /**
     * @Route("/delete/from/{id<[0-9]+>}", name="delete_from_section")
     */
    public function deleteFromSection(AuthenticationUtils $authenticationUtils, SectionRepository $sectionRepository, QuestionsRepository $qstRepository, int $id, EntityManagerInterface $em): Response
    {
        if ($authenticationUtils->getLastUsername()) {
            $qst = $qstRepository->findOneBy(['id' => $id]);
            $sec = $sectionRepository->findOneBy(['id' => $qst->getSection()]);
            $sec->removeQuestion($qst);
            $em->flush();
            $message = 'Question deleted from section successfully';
            $this->addFlash(
                'success',
                $message
            );
            // }
            return $this->redirectToRoute('show_sections');
        } else {
            return $this->render('anonymous/first.html.twig');
        }
    }

    /**
     * @Route("/insert/{idd<[0-9]+>}/{id<[0-9]+>}", name="insert_qst")
     */
    public function insertIntoSection(AuthenticationUtils $authenticationUtils, int $idd, SectionRepository $sectionRepository, QuestionsRepository $qstRepository, int $id, EntityManagerInterface $em): Response
    {
        if ($authenticationUtils->getLastUsername()) {
            // dd($idd, $id);
            $qst = $qstRepository->findOneBy(['id' => $id]);
            $sec = $sectionRepository->findOneBy(['id' => $idd]);
            $sec->addQuestion($qst);
            $em->flush();
            $message = 'Question added to section successfully';
            $this->addFlash(
                'primary',
                $message
            );
            return $this->redirectToRoute('show_sections');
        } else {
            return $this->render('anonymous/first.html.twig');
        }
    }
    /**
     * @Route("/view/{id<[0-9]+>}", name="view_section")
     */
    public function viewSection(AuthenticationUtils $authenticationUtils, Request $request, int $id, QuestionsRepository $qstRepository, SectionRepository $sectionRepository): Response
    {
        if ($authenticationUtils->getLastUsername()) {
            $qsts = $qstRepository->findBy(['section' => $id], ['createdAt' => 'DESC']);
            $sections = $sectionRepository->findOneBy(['id' => $id]);
            return $this->render('section/view.html.twig', compact('qsts', 'sections'));
        } else {
            return $this->render('anonymous/first.html.twig');
        }
    }

    /**
     * @Route("/section", name="show_sections")
     */
    public function showSections(AuthenticationUtils $authenticationUtils, SectionRepository $sectionRepository, FormRepository $formRepository, PaginatorInterface $paginatorInterface, Request $request): Response
    {
        if ($authenticationUtils->getLastUsername()) {
            $sec = $sectionRepository->findBy([], ['createdAt' => 'DESC']);
            $frm = $formRepository->findBy([], ['createdAt' => 'DESC']);
            $pagination = $paginatorInterface->paginate(
                $sec,
                $request->query->getInt('page', 1),
                4
            );
            return $this->render('section/show.html.twig', compact('sec', 'frm', 'pagination'));
        } else {
            return $this->render('anonymous/first.html.twig');
        }
    }
    ////// END SECTION CRUD /////////////////////////

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
            return $this->render('form/new.html.twig', [
                'form' => $form->createView()
            ]);
        } else {
            return $this->render('anonymous/first.html.twig');
        }
    }

    /**
     * @Route("/form/view/{id<[0-9]+>}", name="view_form")
     */
    public function viewForm(AuthenticationUtils $authenticationUtils, Request $request, int $id, FormRepository $formRepository, SectionRepository $secRepository): Response
    {
        if ($authenticationUtils->getLastUsername()) {
            $sections = $secRepository->findBy(['form' => $id], ['createdAt' => 'DESC']);
            $frm = $formRepository->findOneBy(['id' => $id]);
            return $this->render('form/view.html.twig', compact('sections', 'frm'));
        } else {
            return $this->render('anonymous/first.html.twig');
        }
    }

    /**
     * @Route("/forms", name="show_forms")
     */
    public function showForms(AuthenticationUtils $authenticationUtils, FormRepository $formRepository, PaginatorInterface $paginatorInterface, Request $request): Response
    {
        if ($authenticationUtils->getLastUsername()) {
            $frm = $formRepository->findBy([], ['createdAt' => 'DESC']);
            $pagination = $paginatorInterface->paginate(
                $frm,
                $request->query->getInt('page', 1),
                1
            );
            return $this->render('form/show.html.twig', compact('frm', 'pagination'));
        } else {
            return $this->render('anonymous/first.html.twig');
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
            return $this->render('anonymous/first.html.twig');
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
            return $this->render('anonymous/first.html.twig');
        }
    }

    /**
     * @Route("/form/delete/from/{id<[0-9]+>}", name="delete_from_form")
     */
    public function deleteFromForm(AuthenticationUtils $authenticationUtils, SectionRepository $sectionRepository, FormRepository $formRepository, int $id, EntityManagerInterface $em): Response
    {
        if ($authenticationUtils->getLastUsername()) {
            $sec = $sectionRepository->findOneBy(['id' => $id]);
            $frm = $formRepository->findOneBy(['id' => $sec->getForm()]);
            $frm->removeSection($sec);
            $em->flush();
            $message = 'Section deleted from form successfully';
            $this->addFlash(
                'success',
                $message
            );
            // }
            return $this->redirectToRoute('show_forms');
        } else {
            return $this->render('anonymous/first.html.twig');
        }
    }

    /**
     * @Route("/form/insert/{idd<[0-9]+>}/{id<[0-9]+>}", name="insert_sec")
     */
    public function insertIntoForm(AuthenticationUtils $authenticationUtils, int $idd, SectionRepository $sectionRepository, FormRepository $formRepository, int $id, EntityManagerInterface $em): Response
    {
        if ($authenticationUtils->getLastUsername()) {
            // dd($idd, $id);
            $sec = $sectionRepository->findOneBy(['id' => $id]);
            $frm = $formRepository->findOneBy(['id' => $idd]);
            // dd($id);
            $frm->addSection($sec);
            $em->flush();
            $message = 'Section added to form successfully';
            $this->addFlash(
                'primary',
                $message
            );
            return $this->redirectToRoute('show_sections');
        } else {
            return $this->render('anonymous/first.html.twig');
        }
    }
    ////// END FORM CRUD /////////////////////////
}