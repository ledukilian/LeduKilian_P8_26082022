<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class TaskController extends AbstractController
{
    /**
     * @Route("/tasks", name="task_list")
     */
    public function listAction(ManagerRegistry $doctrine): Response
    {
        return $this->render('task/list.html.twig', [
            'tasks' => $doctrine->getRepository('App:Task')->findBy(['isDone' => 0], ['createdAt' => 'DESC', 'isDone' => 'ASC']),
        ]);
    }

    /**
     * @Route("/tasks/done", name="task_done")
     */
    public function showTasksDone(ManagerRegistry $doctrine): Response
    {
        return $this->render('task/list.html.twig', [
            'tasks' => $doctrine->getRepository('App:Task')->findBy(['isDone' => 1], ['createdAt' => 'DESC', 'isDone' => 'ASC']),
        ]);
    }

    /**
     * @Route("/tasks/create", name="task_create")
     */
    public function createAction(Request $request, ManagerRegistry $doctrine): RedirectResponse|Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();

            if ($this->getUser()) {
                $task->setUser($this->getUser());
            } else {
                $task->setUser($doctrine->getRepository('App:User')->findOneBy(['username' => 'Anonyme']));
            }


            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'La tâche a bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/tasks/{id}/edit", name="task_edit")
     */
    public function editAction(Task $task, Request $request, ManagerRegistry $doctrine): RedirectResponse|Response
    {
        if ($this->isGranted('edit-task', $task)) {
            $form = $this->createForm(TaskType::class, $task);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $doctrine->getManager()->flush();

                $this->addFlash('success', 'La tâche a bien été modifiée.');

                return $this->redirectToRoute('task_list');
            }

            return $this->render('task/edit.html.twig', [
                'form' => $form->createView(),
                'task' => $task,
            ]);
        } else {
            $this->addFlash('error', 'Vous n\'avez pas les droits pour modifier cette tâche.');
            return $this->redirectToRoute('task_list');
        }
    }

    /**
     * @Route("/tasks/{id}/toggle", name="task_toggle")
     */
    public function toggleTaskAction(Task $task, ManagerRegistry $doctrine): RedirectResponse
    {
        $task->toggle(!$task->isDone());
        $doctrine->getManager()->flush();

        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

        return $this->redirectToRoute('task_list');
    }

    /**
     * @Route("/tasks/{id}/delete", name="task_delete")
     */
    public function deleteTaskAction(Task $task, ManagerRegistry $doctrine): RedirectResponse
    {
        if ($this->isGranted('delete-task', $task)) {
            $em = $doctrine->getManager();
            $em->remove($task);
            $em->flush();

            $this->addFlash('success', 'La tâche a bien été supprimée.');

        } else {
            $this->addFlash('error', 'Vous n\'avez pas les droits pour supprimer cette tâche.');
        }
        return $this->redirectToRoute('task_list');
    }
}
