<?php

namespace App\Controller;

use App\Entity\Recapitulatif;
use App\Entity\Site;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\Material\LineChart;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
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


    private function createGlobalRecapPieChart(){
        $recapitulatifs = $this->getDoctrine()
            ->getRepository(Recapitulatif::class)
            ->findAll1WeekBackward();

        $pieChart = new PieChart();
        setlocale (LC_TIME, 'fr_FR.utf8');
        $dataTable = [['Site', 'Nombre d\'heures']];
        foreach($recapitulatifs as $recapitulatif){
            $site = $this->getDoctrine()->getRepository(Site::class)->find($recapitulatif['idSite']);
            $dataTable[] = [$site->getNomSite(), $recapitulatif[1]/3600];
        }

        $last_week = strftime("%A %e %B", time()-(6*24*3600));
        $today = strftime("%A %e %B");
        $pieChart->getData()->setArrayToDataTable($dataTable);
        $pieChart->getOptions()
            ->setBackgroundColor('#EAEAEA')
            ->setTitle('Répartition du temps de connexion par site du '.$last_week.' au '.$today)
            ->setHeight(450)
            ->setWidth('45%');

        $pieChart->getOptions()->getTitleTextStyle()
            ->setBold(true)
            ->setColor('#009900')
            ->setItalic(true)
            ->setFontName('Arial')
            ->setFontSize(18);

        return $pieChart;
    }

    private function createGlobalRecapLineChart(){
        $recapitulatifs = $this->getDoctrine()
            ->getRepository(Recapitulatif::class)
            ->findXWeeksBackward(4);

        $lineChart = new LineChart();
        setlocale (LC_TIME, 'fr_FR.utf8');
        $dataTable = [['Jour', 'Nombre d\'heures', 'Nombre de connexions']];
        foreach($recapitulatifs as $recapitulatif){
            $jour = strftime("%A %e %B", $recapitulatif['date']->getTimestamp());
            $dataTable[] = [$jour, $recapitulatif[1]/3600, $recapitulatif[2]/1];
        }
        $lineChart->getData()->setArrayToDataTable($dataTable);

        $lineChart->getOptions()->getChart()
            ->setTitle('Evolution du nombre de connexions et du temps de connexion sur 4 semaines' );

        $lineChart->getOptions()
            ->setHeight(450)
            ->setBackgroundColor('#EAEAEA')
            ->setWidth('45%')
            ->setSeries([['axis' => 'heures'], ['axis' => 'connexions']])
            ->setAxes(['y' => ['heures' => ['label' => 'Nombre d\'heures'], 'connexions' => ['label' => 'Nombre de connexions']]]);

        $lineChart->getOptions()->getTitleTextStyle()
            ->setBold(true)
            ->setColor('#009900')
            ->setItalic(true)
            ->setFontName('Arial')
            ->setFontSize(18);

        return $lineChart;
    }
}
