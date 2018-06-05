<?php

namespace App\Controller;

use App\Entity\ChartBuilder;
use App\Entity\Poste;
use App\Entity\Recapitulatif;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class RecapitulatifController extends Controller
{
    /**
     * @Route("/{_locale}/recap/chart/{codePoste}", defaults={"_locale": "fr"}, name="recap_chart")
     *
     * @param $codePoste
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function recapChartAction($codePoste)
    {
        $poste = $this->getDoctrine()->getRepository(Poste::class)->find($codePoste);
        if (!$poste) {
            throw $this->createNotFoundException('Pas de poste trouvé pour le code'.$codePoste);
        }
    $useRateMessage = '';
        $check = $this->getDoctrine()->getRepository(Recapitulatif::class)->minDureeOuverturePoste($codePoste);
        if(!empty($check[1])) {
            if ($check[1] == 1) {
                $useRateMessage = 'Horaires d\'ouverture dépassées, veuillez les mettre à jour via le script afin de profiter de la fonctionnalité';
            } else {
                $useRate = $this->getDoctrine()->getRepository(Recapitulatif::class)->calculateUseRate($codePoste);
                $useRateMessage = 'Le Poste a été utilisé à ' . $useRate['useRate'] . '% au cours de la dernière semaine';
            }
        }
        if(!$useRateMessage){
            $noDataFound = 'Pas de données pour ce poste';

            return $this->render('recap/recap_poste.html.twig', [
                'poste' => $poste,
                'noDataFound' => $noDataFound,
            ]);
        }

        $pieChart = $this->createWeeklyPieChart($poste);
        $barChart = $this->createWeeklyBarChart($poste);

        $div_piechart = 'div_piechart'.$codePoste;
        $div_barchart = 'div_barchart'.$codePoste;

        return $this->render('recap/recap_poste.html.twig', [
            'piechart' => $pieChart,
            'barchart' => $barChart,
            'div_piechart' => $div_piechart,
            'div_barchart' => $div_barchart,
            'poste' => $poste,
            'useRateMessage' => $useRateMessage,
            ]);
    }

    /**
     * @param $poste
     *
     * @return \CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart
     */
    private function createWeeklyPieChart($poste)
    {
        $recapitulatifs = $this->getDoctrine()
            ->getRepository(Recapitulatif::class)
            ->findByCodePosteAndAWeekBackward($poste);

        setlocale(LC_TIME, 'fr_FR.utf8');
        $dataTable = [['Jour', 'Nombre d\'heures']];
        foreach ($recapitulatifs as $recapitulatif) {
            $jour = strftime('%A', $recapitulatif->getDate()->getTimestamp());
            $dataTable[] = [$jour, $recapitulatif->getDureeCumul() / 3600];
        }
        $title = 'Répartition du temps de connexion quotidien sur une semaine';

        return ChartBuilder::createPieChart($title, $dataTable);
    }

    /**
     * @param $poste
     *
     * @return \CMEN\GoogleChartsBundle\GoogleCharts\Charts\Material\BarChart
     */
    private function createWeeklyBarChart($poste)
    {
        $recapitulatifs = $this->getDoctrine()
            ->getRepository(Recapitulatif::class)
            ->findByCodePosteAndAWeekBackward($poste);

        $dataTable = [['Jour', 'Nombre d\'heures', 'Nombre de sessions']];
        setlocale(LC_TIME, 'fr_FR.utf8');
        foreach ($recapitulatifs as $recapitulatif) {
            $jour = strftime('%A %e %B', $recapitulatif->getDate()->getTimestamp());
            $dataTable[] = [$jour, $recapitulatif->getDureeCumul() / 3600, $recapitulatif->getNbConnexions()];
        }
        $title = 'Temps d\'utilisation et nombre de sessions par jour';
        $series = [['axis' => 'heures'], ['axis' => 'sessions']];        $axes =['x' => [
        'sessions' => ['side' => 'top', 'label' => 'Nombre de sessions'], ],
        'heures' => ['side' => 'top', 'label' => 'Nombre d\'heures'],
    ];

        return ChartBuilder::createBarChart($title, $dataTable, $series, $axes);
    }
}
