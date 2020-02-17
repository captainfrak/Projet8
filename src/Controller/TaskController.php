<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    /**
     * @Route("/tasks", name="task_list")
     */
    public function listTasks()
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('homepage');
        }
        $task = $this->getDoctrine()->getRepository(Task::class)->findBy(['isDone' => 0], ['createdAt' => 'DESC']);

        return $this->render('task/list.html.twig', ['tasks' => $task]);
    }

    /**
     * @Route("/tasks/create", name="task_create")
     *
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request, EntityManagerInterface $em)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('homepage');
        }
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task
                ->setUser($this->getUser())
                ->isDone(0);
            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/tasks/{id}/edit", name="task_edit")
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Task $task, Request $request, EntityManagerInterface $entityManager)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('homepage');
        }
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    /**
     * @Route("/tasks/{id}/toggle", name="task_toggle")
     *
     * @return RedirectResponse
     */
    public function toggleTaskAction(Task $task)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('homepage');
        }
        $task->toggle(!$task->isDone());
        $this->getDoctrine()->getManager()->flush();

        if (1 == $task->isDone()) {
            $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

            return $this->redirectToRoute('task_list');
        } else {
            $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme non faite.', $task->getTitle()));

            return $this->redirectToRoute('task_list');
        }
    }

    /**
     * @Route("/tasksdone", name="task_done")
     */
    public function doneTasks()
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('homepage');
        }
        $task = $this->getDoctrine()->getRepository(Task::class)->findBy(['isDone' => 1], ['createdAt' => 'DESC']);

        return $this->render('task/done.html.twig', ['tasks' => $task]);
    }

    /**
     * @Route("/tasks/{id}/delete", name="task_delete")
     *
     * @return RedirectResponse
     */
    public function deleteTaskAction(Task $task)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('homepage');
        }
        if ('Anonyme' == $task->getUser()->getUsername() && $this->getUser()->isAdmin()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($task);
            $em->flush();

            $this->addFlash('success', 'La tâche a bien été supprimée.');

            return $this->redirectToRoute('task_list');
        }
        if ($task->getUser() != $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez modifier que les taches que vous avez crées');

            return$this->redirectToRoute('task_list');
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($task);
        $em->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }

    // *** PART OF TASKS CONTROLLER FOR ROLE_ADMIN ONLY *** //

    /**
     * @Route("/admin/anotasks", name="task_anonymous")
     *
     * @return Response
     */
    public function anonymousTaskList()
    {
        $user = $this->getUser();
        if ($user) {
            if ($user->isAdmin()) {
                $tasks = $this->getDoctrine()->getRepository(Task::class)->findBy(['user' => null]);

                return $this->render('task/anonymoustask.html.twig', ['tasks' => $tasks]);
            }
        }

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/admin/taskto/{id}", name="task_change")
     *
     * @return RedirectResponse
     */
    public function taskToAnonymous(Task $task, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        if ($user) {
            if ($user->isAdmin) {
                $anonymous = $this->getDoctrine()->getRepository(User::class)->findOneBy(['username' => 'Anonyme']);
                $task->setUser($anonymous);
                $entityManager->persist($task);
                $entityManager->flush();
                $this->addFlash('success', 'L\'auteur de la tache a bien été modifié');

                return $this->redirectToRoute('task_anonymous');
            }
        }

        return $this->redirectToRoute('homepage');
    }
}
