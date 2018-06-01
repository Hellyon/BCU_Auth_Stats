<?php

namespace App\Controller;

use App\Entity\ChartBuilder;
use App\Entity\Recapitulatif;
use App\Entity\Site;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\Material\BarChart;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SiteController extends Controller
{
    /**
     * @Route("/{_locale}/site/{idSite}", name="page_recap_site")
     *
     * @param $idSite
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function page_recap_site($idSite)
    {
        $site = $this->getDoctrine()->getRepository(Site::class)->find($idSite);

        if (!$site) {
            throw $this->createNotFoundException('Pas de site trouvé pour l\'id'.$idSite);
        }

        $evolutionChart = $this->createWeeklyEvolutionBarChart($site);

        return $this->render('site/main_page.html.twig', [
            'evolutionChart' => $evolutionChart,
            'site' => $site,
        ]);
    }

    /**
     * @param $site
     *
     * @return BarChart
     */
    private function createWeeklyEvolutionBarChart($site)
    {
        $recapitulatifs = $this->getDoctrine()
            ->getRepository(Recapitulatif::class)
            ->findBySiteAndAWeekBackward($site);

        $dataTable = [['Jour', 'Nombre d\'Heures', 'Nombre de sessions']];

        foreach ($recapitulatifs as $recapitulatif) {
            $jour = strftime('%A %e %B', $recapitulatif['date']->getTimestamp());
            $dataTable[] = [$jour, $recapitulatif[1] / 3600, $recapitulatif[2] / 1];
        }
        $title = 'Temps d\'utilisation et nombre de sessions sur une semaine';

        return ChartBuilder::createBarChart($title, $dataTable);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function displaySites()
    {
        $sites = $this->getDoctrine()->getRepository(Site::class)->findAll();

        return $this->render('shared/header.html.twig', [
            'liste_sites' => $sites,
        ]);
    }
}
