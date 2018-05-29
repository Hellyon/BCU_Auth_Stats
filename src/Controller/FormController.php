<?php

namespace App\Controller;

use App\Entity\Recapitulatif;
use App\Entity\Recherche;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\Material\BarChart;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\Material\LineChart;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FormController extends Controller
{
    /**
     * @Route("/{_locale}/recherche", name="recherche_globale")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function new(Request $request)
    {

        $formulaire = $this->createRequestForm();

        $formulaire->handleRequest($request);

        $fail = false;

        if ($formulaire->isSubmitted() && $formulaire->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $recherche = $formulaire->getData();

            if (!$recherche->getFin() || $recherche->getFin() == $recherche->getDebut()) {
                return $this->redirect($this->generateUrl('date', [
                    'date' => $recherche->getDebut()->getTimeStamp()
                ]));
            } else {
                return $this->redirect($this->generateUrl('period', [
                    'fin' => $recherche->getDebut()->getTimeStamp(),
                    'debut' => $recherche->getFin()->getTimeStamp()]));
            }

        }
        if ($formulaire->isSubmitted()) {
            $fail = true;
        }
        return $this->render('form/form.html.twig', [
            'form' => $formulaire->createView(),
            'fail' => $fail,
        ]);
    }

    private function createRequestForm()
    {
        $recherche = new Recherche();
        $recherche->setFin(new \DateTime());
        $recherche->setDebut(new \DateTime('-7 Day'));

        return $this->createFormBuilder($recherche)
            ->setAction($this->generateUrl('recherche_globale'))
            ->add('debut', DateType::class, [
                'label' => "Date Début",
                'widget' => 'single_text',
                // adds a class that can be selected in JavaScript
                'attr' => ['class' => 'js-datepicker'],
            ])
            ->add('fin', DateType::class, [
                'label' => "Date Fin",
                'widget' => 'single_text',
                'required' => false,
                // adds a class that can be selected in JavaScript
                'attr' => ['class' => 'js-datepicker'],
            ])
            ->add('rechercher', SubmitType::class)
            ->getForm();
    }

    /**
     * @Route("/date/{date}", name="date")
     * @param $date
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function recherche_date($date)
    {
        $formulaire = $this->createRequestForm();
        $requestChart = $this->createRequestedDateChart($date);

        return $this->render('form/form.html.twig', [
            'form' => $formulaire->createView(),
            'requestChart' => $requestChart,
        ]);
    }

    private function createRequestedDateChart($date)
    {
        $dateD = strftime("%Y-%m-%d", $date);

        $recapitulatifs = $this->getDoctrine()
            ->getRepository(Recapitulatif::class)
            ->findByDate($dateD);

        $chart = new BarChart();

        setlocale(LC_TIME, 'fr_FR.utf8');
        $date = strftime("%A %e %B", $date);

        $dataTable = [['Site', 'Nombre d\'heures', 'Nombre de connexions']];
        foreach ($recapitulatifs as $recapitulatif) {
            $dataTable[] = [$recapitulatif['nomSite'], $recapitulatif[1] / 3600, $recapitulatif[2] / 1];
        }
        $chart->getData()->setArrayToDataTable($dataTable);

        $chart->getOptions()->getChart()
            ->setTitle('Nombre de connexions et temps de connexion du ' . $date . ' pour chaque site.');

        $chart->getOptions()
            ->setBackgroundColor('#EAEAEA')
            ->setHeight(450)
            ->setWidth('45%')
            ->setOrientation('horizontal')
            ->setSeries([['axis' => 'heures'], ['axis' => 'connexions']])
            ->setAxes(['x' => [
                'connexions' => ['side' => 'top', 'label' => 'Nombre de connexions']],
                'heures' => ['side' => 'top', 'label' => 'Nombre d\'heures']
            ]);

        $chart->getOptions()->getTitleTextStyle()
            ->setBold(true)
            ->setColor('#009900')
            ->setItalic(true)
            ->setFontName('Arial')
            ->setFontSize(18);

        return $chart;
    }

    /**
     * @Route("/period/{debut}/{fin}", name="period")
     * @param $debut
     * @param $fin
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function recherche_period($debut, $fin)
    {
        $formulaire = $this->createRequestForm();
        $requestChart = $this->createRequestedLineChart($debut, $fin);

        return $this->render('form/form.html.twig', [
            'form' => $formulaire->createView(),
            'requestChart' => $requestChart,
        ]);
    }

    private function createRequestedLineChart($debut, $fin)
    {
        $debutD = strftime("%Y-%m-%d", $debut);
        $finD = strftime("%Y-%m-%d", $fin);

        $recapitulatifs = $this->getDoctrine()
            ->getRepository(Recapitulatif::class)
            ->findByPeriod($debutD, $finD);

        $lineChart = new LineChart();

        setlocale(LC_TIME, 'fr_FR.utf8');
        $debut = strftime("%A %e %B", $debut);
        $fin = strftime("%A %e %B", $fin);

        $dataTable = [['Jour', 'Nombre d\'heures', 'Nombre de connexions']];
        foreach ($recapitulatifs as $recapitulatif) {

            $jour = strftime("%A %e %B", $recapitulatif['date']->getTimestamp());
            $dataTable[] = [$jour, $recapitulatif[1] / 3600, $recapitulatif[2] / 1];
        }
        $lineChart->getData()->setArrayToDataTable($dataTable);

        $lineChart->getOptions()->getChart()
            ->setTitle('Evolution du nombre de connexions et du temps de connexion du ' . $fin . ' au ' . $debut . '.');

        $lineChart->getOptions()->getTitleTextStyle()
            ->setBold(true)
            ->setColor('#009900')
            ->setItalic(true)
            ->setFontName('Arial')
            ->setFontSize(18);

        $lineChart->getOptions()
            ->setBackgroundColor('#EAEAEA')
            ->setHeight(450)
            ->setWidth('45%')
            ->setSeries([['axis' => 'heures'], ['axis' => 'connexions']])
            ->setAxes(['y' => ['heures' => ['label' => 'Nombre d\'heures'], 'connexions' => ['label' => 'Nombre de connexions']]]);

        return $lineChart;
    }
}