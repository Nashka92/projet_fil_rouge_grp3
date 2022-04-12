<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\Status;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/task")
 */
class TaskController extends AbstractController
{
    /**
     * @Route("/", name="app_task_index", methods={"GET"})
     */
    public function index(TaskRepository $taskRepository): Response
    {
        return $this->render('task/index.html.twig', [
            'tasks' => $taskRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_task_new", methods={"GET", "POST"})
     */
    public function new(Request $request, TaskRepository $taskRepository): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $taskRepository->add($task);
            return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task/new.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/affichage", name="app_task_affichage", methods={"GET"})
     */
    public function affichage() : Response {

        $repo = $this->getDoctrine()->getRepository(Task::class);
        $task = $repo->findAll();

        return $this->render('affichage_taches.html.twig', [
            'tasks' => $task
        ]);

    }

    /**
     * @Route("/{id}", name="app_task_show", methods={"GET"})
     */
    public function show(Task $task): Response
    {
        return $this->render('task/show.html.twig', [
            'task' => $task,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_task_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Task $task, TaskRepository $taskRepository): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $taskRepository->add($task);
            return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task/edit.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_task_delete", methods={"POST"})
     */
    public function delete(Request $request, Task $task, TaskRepository $taskRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->request->get('_token'))) {
            $taskRepository->remove($task);
        }

        return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/task/{id}", name="app_task_alloueTask")
     */
    public function alloueTask(ManagerRegistry $doctrine, Task $tasks, EntityManagerInterface $manager): Response{

        

        $task = $tasks->setUser($this->getUser())
                      ->setStatus($doctrine->getRepository(Status::class)->find(2));

                      
        $manager->persist($task);
        $manager->flush($task);

        return $this->redirectToRoute('app_task_affichage', [], Response::HTTP_SEE_OTHER);

    }

    /**
     * @Route("/task/termine/{id}", name="app_task_termineTask")
     */
    public function termineTask(ManagerRegistry $doctrine, Task $tasks, EntityManagerInterface $manager) :Response {
        $task = $tasks->setUser($this->getUser())
                      ->setStatus($doctrine->getRepository(Status::class)->find(3));

                      
        $manager->persist($task);
        $manager->flush($task);

        return $this->redirectToRoute('app_task_affichage', [], Response::HTTP_SEE_OTHER);
    }
   
}
