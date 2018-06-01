<?php

namespace App\Controller;

use App\Entity\ChartBuilder;
use App\Entity\Recapitulatif;
use App\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends Controller
{
    /**
     * @Route("/{_locale}", name="index")
     */
    public function index()
    {
        $pieChart = $this->createGlobalRecapPieChart();
        $lineChart = $this->createGlobalRecapLineChart();

        $sites = $this->getDoctrine()->getRepository(Site::class)->findAll();

        return $this->render('index.html.twig', [
            'piechart' => $pieChart,
            'linechart' => $lineChart,
            'liste_sites' => $sites,
        ]);
    }

    /**
     * @return \CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart
     */
    private function createGlobalRecapPieChart()
    {
        $recapitulatifs = $this->getDoctrine()
            ->getRepository(Recapitulatif::class)
            ->findAll1WeekBackward();

        setlocale(LC_TIME, 'fr_FR.utf8');
        $dataTable = [['Site', 'Nombre d\'heures']];
        foreach ($recapitulatifs as $recapitulatif) {
            $site = $this->getDoctrine()->getRepository(Site::class)->find($recapitulatif['idSite']);
            $dataTable[] = [$site->getNomSite(), $recapitulatif[1] / 3600];
        }

        $last_week = strftime('%A %e %B', time() - (6 * 24 * 3600));
        $today = strftime('%A %e %B');
        $title = 'Répartition du temps de connexion par site du '.$last_week.' au '.$today;

        return ChartBuilder::createPieChart($title, $dataTable);
    }

    /**
     * @return \CMEN\GoogleChartsBundle\GoogleCharts\Charts\Material\LineChart
     */
    private function createGlobalRecapLineChart()
    {
        $recapitulatifs = $this->getDoctrine()
            ->getRepository(Recapitulatif::class)
            ->findXWeeksBackward(4);

        setlocale(LC_TIME, 'fr_FR.utf8');
        $dataTable = [['Jour', 'Nombre d\'heures', 'Nombre de sessions']];
        foreach ($recapitulatifs as $recapitulatif) {
            $jour = strftime('%A %e %B', $recapitulatif['date']->getTimestamp());
            $dataTable[] = [$jour, $recapitulatif[1] / 3600, $recapitulatif[2] / 1];
        }
        $title = 'Evolution du nombre de sessions et du temps de connexion sur 4 semaines';

        return ChartBuilder::createLineChart($title, $dataTable);
    }
}
