<?php

namespace App\Controller;

use App\Entity\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SessionController extends Controller
{
    /**
     * @Route("/{_locale}/session/findByID/{id}", name="find_session_by_id")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function findByID($id){
        $session = $this->getDoctrine()->getRepository(Session::class)->find($id);

        if(!$session){
            throw $this->createNotFoundException('Pas de Session trouvée pour l\'id'. $id);
        }

        return $this->render('session/session.html.twig', [
            'recap' => $session,

        ]);
    }
}
