<?php

namespace App\Controller;

use App\Entity\Poste;
use App\Entity\Recapitulatif;
use App\Entity\Site;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\Material\BarChart;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\Histogram;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RecapitulatifController extends Controller
{
    /**
     * @Route("/{_locale}/recap/chart/{codePoste}", defaults={"_locale": "fr"}, name="recap_chart")
     */
    public function index($codePoste)
    {

        $poste = $this->getDoctrine()->getRepository(Poste::class)->find($codePoste);

        if(!$poste){
            throw $this->createNotFoundException('Pas de poste trouvé pour le code'. $codePoste);
        }

        $pieChart = $this->createWeeklyPieChart($poste);
        $barChart = $this->createWeeklyBarChart($poste);

        $div_piechart = "div_piechart".$codePoste;
        $div_barchart = "div_barchart".$codePoste;
        return $this->render('recap/recap_poste.twig', [
            'piechart' => $pieChart,
            'barchart' => $barChart,
            'div_piechart' => $div_piechart,
            'div_barchart' => $div_barchart,
            'poste' => $poste
            ]);
    }

    private function createWeeklyPieChart($poste)
    {
        $recapitulatifs = $this->getDoctrine()
            ->getRepository(Recapitulatif::class)
            ->findByCodePosteAndAWeekBackward($poste);

        $pieChart = new PieChart();
        setlocale (LC_TIME, 'fr_FR.utf8');
        $dataTable = [['Jour', 'Nombre d\'heures']];
        foreach($recapitulatifs as $recapitulatif){
            $jour = strftime("%A", $recapitulatif->getDate()->getTimestamp());
            $dataTable[] = [$jour, $recapitulatif->getDureeCumul()/3600];

        }
        $pieChart->getData()->setArrayToDataTable($dataTable);

        $pieChart->getOptions()
            ->setTitle('Répartition du temps de connexion quotidien sur une semaine')
            ->setHeight(450)
            ->setWidth(750);

        $pieChart->getOptions()->getTitleTextStyle()
            ->setBold(true)
            ->setColor('#009900')
            ->setItalic(true)
            ->setFontName('Arial')
            ->setFontSize(20);

        return $pieChart;
    }

    private function createWeeklyBarChart($poste)
    {
        $recapitulatifs = $this->getDoctrine()
            ->getRepository(Recapitulatif::class)
            ->findByCodePosteAndAWeekBackward($poste);

        $barChart = new BarChart();
        $dataTable = [['Jour','Nombre d\'heures', 'Nombre de connexions']];
        setlocale (LC_TIME, 'fr_FR.utf8');
        foreach($recapitulatifs as $recapitulatif){
            $jour = strftime("%A %e %B", $recapitulatif->getDate()->getTimestamp());
            $dataTable[] = [$jour, $recapitulatif->getDureeCumul()/3600,$recapitulatif->getNbConnexions()];
        }
        $barChart->getData()->setArrayToDataTable($dataTable);
        $barChart->getOptions()->getChart()
        ->setTitle('Temps d\'utilisation et nombre de connexions par jour')
        ->setSubtitle('Nombre d\'heures à gauche, Nombre de connexions à droite');

        $barChart->getOptions()->getTitleTextStyle()
            ->setBold(true)
            ->setColor('#009900')
            ->setItalic(true)
            ->setFontName('Arial')
            ->setFontSize(18);

        $barChart->getOptions()
            ->setHeight(450)
            ->setWidth(750)
            ->setOrientation('horizontal')
            ->setSeries([['axis' => 'Nombre d\'heures'], ['axis' => 'Nombre de connexions']])
            ->setAxes(['x' => [
                'Nombre d\'heures' => ['label' => 'heures'],
                'Nombre de connexions' => ['side' => 'top', 'label' => 'connexions']]
            ]);

        return $barChart;
    }
}
