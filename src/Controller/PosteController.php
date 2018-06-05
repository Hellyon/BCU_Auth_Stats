<?php
/**
 * Created by PhpStorm.
 * User: ilbenjel
 * Date: 22/05/18
 * Time: 11:01.
 */

namespace App\Controller;

use App\Entity\Poste;
use App\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PosteController extends Controller
{
    /**
     * Affiche  les différents postes publics dans la liste des postes du site.
     *
     * @param $site
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function displayPostesSiteAction(Site $site)
    {
        $postes = $this->getDoctrine()->getRepository(Poste::class)->findByIdSite($site);

        if (!$postes) {
            throw $this->createNotFoundException('Pas de postes trouvés...');
        }

        return $this->render('poste/liste_poste_site.html.twig', [
            'liste_postes' => $postes,
        ]);
    }
}
