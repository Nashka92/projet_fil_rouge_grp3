<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\StatusRepository;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping\Id;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UtilisateurController extends AbstractController
{
    /**
     * @Route("/utilisateur", name="app_utilisateur")
     */
    public function index(): Response
    {
   
        return $this->render('utilisateur/index.html.twig');
    }

    /**
     * @Route("/uti", name="app_uti")
     */
    public function user(UserRepository $user)
    {
        $users = $user->findAll();

        $data = array();
        foreach ($users as $key => $value) {
            $data[$key]['nom'] = $value->getNom();
            $data[$key]['prenom'] = $value->getPrenom();
            $data[$key]['email'] = $value->getEmail();
            $data[$key]['id'] = $value->getId();
        }
        $data = ['data' => $data];

        return new JsonResponse($data);
    }

    /**
    * @Route("/utilisateur/{id}/delete", name="app_user_delete", methods={"POST"})
    */
    public function deleteUser(Request $request, User $user, UserRepository $userRepository, StatusRepository $repoStatut, TaskRepository $repoTask): Response
    {
        
        $statut_tache_user = $repoStatut->find(2);
        $nb_tache_en_cours = $repoTask->CountTask($user,$statut_tache_user);

        if ( count($nb_tache_en_cours) == 0 ){
            // dd($nb_tache_en_cours);
            
           if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user);
            }

        }else{
            $this->addFlash("failed", "Impossible, tâches en cours pour l'utilisateur ");
            return $this->redirectToRoute('app_utilisateur');
        }
        
        

        return $this->redirectToRoute('app_utilisateur', [], Response::HTTP_SEE_OTHER);
    }


    /**
     * @Route("/test", name="apptest")
     */
    public function test(): Response
    {
        
        return $this->render('test.html.twig');
    }

}
