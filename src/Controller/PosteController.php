<?php
/**
 * Created by PhpStorm.
 * User: ilbenjel
 * Date: 22/05/18
 * Time: 11:01
 */

namespace App\Controller;


use App\Entity\Poste;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Recapitulatif;
use App\Entity\Site;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class PosteController extends Controller
{
    public function displayPostesSite($site){
        $postes = $this->getDoctrine()->getRepository(Poste::class)->findByIdSite($site);

        if(!$postes){
            throw $this->createNotFoundException('Pas de postes trouvés...');
        }

        return $this->render('poste/liste_poste_site.html.twig', [
            'liste_postes' => $postes,
        ]);
    }
}