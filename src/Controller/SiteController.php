<?php

namespace App\Controller;

use App\Entity\Recapitulatif;
use App\Entity\Site;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\Material\BarChart;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SiteController extends Controller
{
    /**
     * @Route("/site", name="site")
     */
    public function index()
    {
        return $this->render('site/index.html.twig', [
            'controller_name' => 'SiteController',
        ]);
    }

    /**
     * @Route("/site/{idSite}", name="main_page")
     */
    public function main_page($idSite){
        $site = $this->getDoctrine()->getRepository(Site::class)->find($idSite);

        if(!$site){
            throw $this->createNotFoundException('Pas de site trouvé pour l\'id'. $idSite);
        }

        $evolutionChart = $this->createWeeklyEvolutionBarChart($site);

        return $this->render('site/main_page.html.twig', [
            'evolutionChart' => $evolutionChart
        ]);
    }

    private function createWeeklyEvolutionBarChart($site){
        $recapitulatifs = $this->getDoctrine()
            ->getRepository(Recapitulatif::class)
            ->findBySiteAndAWeekBackward($site);

        $barChart = new BarChart();
        $dataTable = [['Jour','Nombre d\'Heures', 'Nombre de connexions']];

        setlocale (LC_TIME, 'fr_FR.utf8');

        foreach($recapitulatifs as $recapitulatif){
            $jour = strftime("%A %e %B", $recapitulatif['date']->getTimestamp());
            $dataTable[] = [$jour, $recapitulatif[1]/3600, $recapitulatif[2]/1];
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
            ->setHeight(400)
            ->setWidth(900)
            ->setOrientation('horizontal')
            ->setSeries([['axis' => 'heures'], ['axis' => 'connexions']])
            ->setAxes(['x' => [
                'connexions' => ['side' => 'top', 'label' => 'Nombre de connexions']],
                'heures' => ['side' => 'top', 'label' => 'Nombre d\'heures']
            ]);

        return $barChart;
    }
}
