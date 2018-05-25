<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Recapitulatif;
use App\Entity\Recherche;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\Material\LineChart;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\DateType;
class FormController extends Controller
{
    /**
     * @Route("/{_locale}/recherche", name="recherche_globale")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function new(Request $request){

        $formulaire = $this->createRequestForm();

        $formulaire->handleRequest($request);

        if ($formulaire->isSubmitted() && $formulaire->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $recherche = $formulaire->getData();

            return $this->redirect($this->generateUrl('success', [
                'fin' => $recherche->getDebut()->getTimeStamp(),
                'debut' => $recherche->getFin()->getTimeStamp()]));

        }
        return $this->render('form/form.html.twig', [
            'form' => $formulaire->createView(),
        ]);
    }

    /**
     * @Route("/success/{debut}/{fin}", name="success")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function success($debut, $fin){
        $formulaire = $this->createRequestForm();
        $lineChart = $this->createRequestedLineChart($debut, $fin);

        return $this->render('form/form.html.twig', [
            'form' => $formulaire->createView(),
            'lineChart' => $lineChart,
        ]);
    }

    private function createRequestForm(){
        $recherche = new Recherche();
        $recherche->setFin(new \DateTime());
        $recherche->setDebut(new \DateTime('-7 Day'));

        return $this->createFormBuilder($recherche)
            ->setAction($this->generateUrl('recherche_globale'))
            ->add('debut', DateType::class, [
                'widget' => 'single_text',

                // adds a class that can be selected in JavaScript
                'attr' => ['class' => 'js-datepicker']])
            ->add('fin', DateType::class, [
                'widget' => 'single_text',

                // adds a class that can be selected in JavaScript
                'attr' => ['class' => 'js-datepicker']])
            ->getForm();
    }

    private function createRequestedLineChart($debut, $fin){
        $debutD = strftime("%Y-%m-%d", $debut);
        $finD = strftime("%Y-%m-%d", $fin);

        $recapitulatifs = $this->getDoctrine()
            ->getRepository(Recapitulatif::class)
            ->findByPeriod($debutD, $finD);

        $lineChart = new LineChart();

        setlocale (LC_TIME, 'fr_FR.utf8');
        $debut = strftime("%A %e %B", $debut);
        $fin = strftime("%A %e %B", $fin);

        $dataTable = [['Jour', 'Nombre d\'heures', 'Nombre de connexions']];
        foreach($recapitulatifs as $recapitulatif){

            $jour = strftime("%A %e %B", $recapitulatif['date']->getTimestamp());
            $dataTable[] = [$jour, $recapitulatif[1]/3600, $recapitulatif[2]/1];
        }
        $lineChart->getData()->setArrayToDataTable($dataTable);

        $lineChart->getOptions()->getChart()
            ->setTitle('Evolution du nombre de connexions et du temps de connexion du '.$fin.' au '.$debut.'.');

        $lineChart->getOptions()->getTitleTextStyle()
            ->setBold(true)
            ->setColor('#009900')
            ->setItalic(true)
            ->setFontName('Arial')
            ->setFontSize(18);

        $lineChart->getOptions()
            ->setHeight(450)
            ->setWidth(900)
            ->setSeries([['axis' => 'heures'], ['axis' => 'connexions']])
            ->setAxes(['y' => ['heures' => ['label' => 'Nombre d\'heures'], 'connexions' => ['label' => 'Nombre de connexions']]]);

        return $lineChart;
    }
}