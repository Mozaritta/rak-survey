<?php

namespace App\Controller;

use App\Entity\Form;
use App\Entity\Type;
use App\Form\FormType;
use App\Entity\Section;
use App\Entity\Questions;
use App\Form\SectionType;
use App\Form\QuestionsType;
use App\Repository\TypeRepository;
use App\Repository\FormRepository;
use App\Repository\SectionRepository;
use App\Repository\QuestionsRepository;
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
    public function homeAdmin(AuthenticationUtils $authenticationUtils): Response
    {
        if ($authenticationUtils->getLastUsername()) {
            if ($this->isGranted('ROLE_ADMIN')) {
                return $this->render('admin/home.html.twig', []);
            } else {
                return $this->redirectToRoute('app_home');
            }
        } else {
            return $this->render('anonymous/first.html.twig');
        }
    }

    /**
     * @Route("/test", name="test")
     */
    public function test(AuthenticationUtils $authenticationUtils, Request $request, QuestionsRepository $formRepository, PaginatorInterface $paginatorInterface): Response
    {
        if ($authenticationUtils->getLastUsername()) {
            $frm = $formRepository->findAll();
            $pagination = $paginatorInterface->paginate(
                $frm,
                $request->query->getInt('page', 1),
                2
            );
            return $this->render('test/test.html.twig', ['frm' => $frm, 'pagination' => $pagination]);
        } else {
            return $this->render('anonymous/first.html.twig');
        }
    }
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
            $sec = $sectionRepository->findOneBy(['id' => $id]);
            return $this->render('section/view.html.twig', compact('qsts', 'sec'));
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
            $secs = $secRepository->findBy(['form' => $id], ['createdAt' => 'DESC']);
            $frm = $formRepository->findOneBy(['id' => $id]);
            return $this->render('form/view.html.twig', compact('secs', 'frm'));
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