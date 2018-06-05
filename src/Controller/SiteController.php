<?php

namespace App\Controller;

use App\Entity\ChartBuilder;
use App\Entity\Recapitulatif;
use App\Entity\Session;
use App\Entity\Site;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\Material\BarChart;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SiteController extends Controller
{
    /**
     * @Route("/{_locale}/site/{idSite}", name="recap_site")
     *
     * @param $idSite
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function recapSiteAction(int $idSite)
    {
        $site = $this->getDoctrine()->getRepository(Site::class)->find($idSite);

        if (!$site) {
            throw $this->createNotFoundException('Pas de site trouvé pour l\'id'.$idSite);
        }

        $evolutionChart = $this->createWeeklyEvolutionBarChart($site);
        $rushHoursChart = $this->createWeeklyRushHourBarChart($site);

        return $this->render('site/site.html.twig', [
            'evolutionChart' => $evolutionChart,
            'rushHoursChart' => $rushHoursChart,
            'site' => $site,
        ]);
    }

    /**
     * @param $site
     *
     * @return BarChart
     */
    private function createWeeklyEvolutionBarChart(Site $site)
    {
        $recapitulatifs = $this->getDoctrine()
            ->getRepository(Recapitulatif::class)
            ->findBySiteAndAWeekBackward($site);

        setlocale(LC_TIME, 'fr_FR.utf8');
        $dataTable = [['Jour', 'Nombre d\'Heures', 'Nombre de sessions']];

        foreach ($recapitulatifs as $recapitulatif) {
            $jour = strftime('%A %e %B', $recapitulatif['date']->getTimestamp());
            $dataTable[] = [$jour, $recapitulatif[1] / 3600, $recapitulatif[2] / 1];
        }
        $title = 'Temps d\'utilisation et nombre de sessions sur une semaine';
        $series = [['axis' => 'heures'], ['axis' => 'sessions']];
        $axes = ['x' => [
        'sessions' => ['side' => 'top', 'label' => 'Nombre de sessions'],
        'heures' => ['side' => 'top', 'label' => 'Nombre d\'heures'],
        ],
    ];

        return ChartBuilder::createBarChart($title, $dataTable, $series, $axes);
    }

    private function createWeeklyRushHourBarChart(Site $site){
        $data = $this->getDoctrine()
            ->getRepository(Session::class)
            ->rushHours($site);

        setlocale(LC_TIME, 'fr_FR.utf8');
        $dataTable = [['période', '8h-10h', '10h-12h', '12h-14h', '14h-16h', '16h-18h', '18h-20h', '20h-22h', '22h-00h']];
        $dataTable[] = ['période', $data['H8']/1, $data['H10']/1, $data['H12']/1, $data['H14']/1, $data['H16']/1, $data['H18']/1, $data['H20']/1, $data['H22']/1];

        $title = 'Répartition des heures d\'affluence sur la semaine';
        $series = ['axis' => 'période'];
        $axes = [ 'x' => [
            'période' => ['side' => 'top', 'label' => 'Nombre de sessions'],
        ]];

        return ChartBuilder::createBarChart($title, $dataTable, $series, $axes);
    }
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function displaySitesAction()
    {
        $sites = $this->getDoctrine()->getRepository(Site::class)->findAll();

        return $this->render('header.html.twig', [
            'liste_sites' => $sites,
        ]);
    }
}
