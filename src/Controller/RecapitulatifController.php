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
    public function index($codePoste)
    {
        $poste = $this->getDoctrine()->getRepository(Poste::class)->find($codePoste);

        if (!$poste) {
            throw $this->createNotFoundException('Pas de poste trouvé pour le code'.$codePoste);
        }

        $pieChart = $this->createWeeklyPieChart($poste);
        $barChart = $this->createWeeklyBarChart($poste);

        $div_piechart = 'div_piechart'.$codePoste;
        $div_barchart = 'div_barchart'.$codePoste;

        return $this->render('recap/recap_poste.twig', [
            'piechart' => $pieChart,
            'barchart' => $barChart,
            'div_piechart' => $div_piechart,
            'div_barchart' => $div_barchart,
            'poste' => $poste,
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

        $dataTable = [['Jour', 'Nombre d\'heures', 'Nombre de connexions']];
        setlocale(LC_TIME, 'fr_FR.utf8');
        foreach ($recapitulatifs as $recapitulatif) {
            $jour = strftime('%A %e %B', $recapitulatif->getDate()->getTimestamp());
            $dataTable[] = [$jour, $recapitulatif->getDureeCumul() / 3600, $recapitulatif->getNbConnexions()];
        }
        $title = 'Temps d\'utilisation et nombre de connexions par jour';

        return ChartBuilder::createBarChart($title, $dataTable);
    }
}
