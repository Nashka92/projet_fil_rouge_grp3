<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Task;
use App\Entity\Status;
use App\Form\ClientType;
use App\Form\TaskType;
use App\Repository\ClientRepository;
use App\Repository\StatusRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    public function new(Request $request, TaskRepository $taskRepository, ClientRepository $clientRepository): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        $client = new Client();
        $form2 = $this->createForm(ClientType::class, $client);
        $form2->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $taskRepository->add($task);
            return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($form2->isSubmitted() && $form2->isValid()) {
            $clientRepository->add($client);
        }

        return $this->render('task/new.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
            'form2' => $form2->createView() 
               ]);
    }

    /**
     * @Route("/affichage", name="app_task_affichage", methods={"GET"})
     */
    public function affichage(TaskRepository $repoTask) : Response {

        $task = $repoTask->findAll();

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
    public function alloueTask(TaskRepository $repoTask,StatusRepository $repoStatut,ManagerRegistry $doctrine, Task $tasks, EntityManagerInterface $manager){

       
        $user = $this->getUser();
        $role = $this->getUser()->getRoles();
        $statut = $repoStatut->find(2);
        $nombre = $repoTask->CountTask($user,$statut);
        
        if ($role[0] == "ROLE_SENIOR") {
            $max = 3;
            if (count($nombre) < $max) {
                $task = $tasks->setUser($this->getUser())
                      ->setStatus($statut);
                      $manager->persist($task);
                    $manager->flush($task);
                    $this->addFlash('success','Tâche attribué! Nombre de tâches : '.(count($nombre)+1)." sur ".$max);
                return $this->redirectToRoute('app_task_affichage');
                

        
            }
            
            else {

                $this->addFlash("failed", "Impossible, vous avez ".count($nombre)." tâches en cours, votre limite est de ".$max);
                return $this->redirectToRoute('app_task_affichage');
            }

            
            
        }

        if ($role[0] == "ROLE_EXPERT") {
            $max = 5;
            if (count($nombre) < $max) {
                $task = $tasks->setUser($this->getUser())
                      ->setStatus($statut);
                      $manager->persist($task);
        $manager->flush($task);
        $this->addFlash('success','Tâche attribué! Nombre de tâches : '.(count($nombre)+1)." sur ".$max);
                
                return $this->redirectToRoute('app_task_affichage');

        
            }
            
            
            else {

                $this->addFlash("failed", "Impossible, vous avez ".count($nombre)." tâches en cours, votre limite est de ".$max);
                return $this->redirectToRoute('app_task_affichage');
            }

            
            
        }

        if ($role[0] == "ROLE_APPRENTIT") {
            $max = 1;
            if (count($nombre) > $max) {
                $task = $tasks->setUser($this->getUser())
                      ->setStatus($statut);
                      $manager->persist($task);
        $manager->flush($task);
        $this->addFlash('success','Tâche attribué! Nombre de tâches : '.(count($nombre)+1)." sur ".$max);
                return $this->redirectToRoute('app_task_affichage');

        
            }
            
            else {

                
                $this->addFlash("failed", "Impossible, vous avez ".count($nombre)." tâches en cours, votre limite est de ".$max);
                return $this->redirectToRoute('app_task_affichage');
            }

            
            
        }

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

    /**
     * @Route("/json/client", methods={"GET"})
     */
    public function client_json(): Response
    {
        $categorias = $this->getDoctrine()
                   ->getRepository(Client::class)
                   ->findAll();

                   foreach($categorias as $item) {
                    $arrayCollection[] = array(
                        'id' => $item->getId(),
                        'nom' => $item->getNom(),
                        'prenom' => $item->getPrenom(),
                        'adresse' => $item->getAdresse(),
                        
                    );
               }
               
               return new JsonResponse($arrayCollection);
    }
   
}
