<?php

namespace App\Controller;

use App\Entity\ChartBuilder;
use App\Entity\Poste;
use App\Entity\Recapitulatif;
use App\Entity\Recherche;
use App\Entity\Site;
use App\Form\RechercheType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FormController extends Controller
{
    /**
     * @Route("/{_locale}/recherche", name="recherche_globale", defaults={"_locale": "fr"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newFormAction(Request $request)
    {
        $formulaire = $this->createRequestForm();
        $formulaire->handleRequest($request);
        $fail = false;

        if ($formulaire->isSubmitted() && $formulaire->isValid()) {
            $recherche = $formulaire->getData();

            switch ($recherche->getType()) {
                case 'global':
                    if (!$recherche->getFin() || $recherche->getFin() == $recherche->getDebut()) {
                        return $this->redirect($this->generateUrl('date', [
                            'date' => $recherche->getDebut()->getTimeStamp(),
                        ]));
                    } else {
                        return $this->redirect($this->generateUrl('periode', [
                            'fin' => $recherche->getDebut()->getTimeStamp(),
                            'debut' => $recherche->getFin()->getTimeStamp(), ]));
                    }
                    break;
                case 'site':
                    if (!$recherche->getFin() || $recherche->getFin() == $recherche->getDebut()) {
                        return $this->redirect($this->generateUrl('date', [
                            'date' => $recherche->getDebut()->getTimeStamp(),
                            'site' => $recherche->getSite()->getIdSite(),
                        ]));
                    } else {
                        return $this->redirect($this->generateUrl('periode', [
                            'fin' => $recherche->getDebut()->getTimeStamp(),
                            'debut' => $recherche->getFin()->getTimeStamp(),
                            'site' => $recherche->getSite()->getIdSite(),
                        ]));
                    }
                    break;
                case 'poste':
                    if (!$recherche->getFin() || $recherche->getFin() == $recherche->getDebut()) {
                        return $this->redirect($this->generateUrl('date', [
                            'date' => $recherche->getDebut()->getTimeStamp(),
                            'poste' => $recherche->getPoste()->getCodePoste(),
                        ]));
                    } else {
                        return $this->redirect($this->generateUrl('periode', [
                            'fin' => $recherche->getDebut()->getTimeStamp(),
                            'debut' => $recherche->getFin()->getTimeStamp(),
                            'poste' => $recherche->getPoste()->getCodePoste(),
                        ]));
                    }
                    break;
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

    /**
     * @Route("/{_locale}/date/{date}/{site}/{poste}", name="date", defaults={"_locale": "fr", "site": "0", "poste": "NOPOSTE"})
     *
     * @param int    $date
     * @param int    $site
     * @param string $poste
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rechercheDate(int $date, ?int $site, ?string $poste)
    {
        $formulaire = $this->createRequestForm();
        $requestChart = $this->createRequestedDateChart($date, $site, $poste);

        if ($requestChart) {
            return $this->render('form/form.html.twig', [
                'form' => $formulaire->createView(),
                'requestChart' => $requestChart,
            ]);
        } else {
            return $this->render('form/form.html.twig', [
                'form' => $formulaire->createView(),
                'requestChart' => $requestChart,
                'noDataFound' => 'Aucune donnée trouvée pour la date indiquée !',
            ]);
        }
    }

    /**
     * @Route("/{_locale}/periode/{debut}/{fin}/{site}/{poste}", name="periode", defaults={"_locale": "fr", "site": "0", "poste": "NOPOSTE"})
     *
     * @param int    $debut
     * @param int    $fin
     * @param int    $site
     * @param string $poste
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function recherchePeriode(int $debut, int $fin, ?int $site, ?string $poste)
    {
        $formulaire = $this->createRequestForm();
        $requestChart = $this->createRequestedLineChart($debut, $fin, $site, $poste);

        if ($requestChart) {
            return $this->render('form/form.html.twig', [
                'form' => $formulaire->createView(),
                'requestChart' => $requestChart,
            ]);
        } else {
            return $this->render('form/form.html.twig', [
                'form' => $formulaire->createView(),
                'requestChart' => $requestChart,
                'noDataFound' => 'Aucune donnée trouvée pour la période indiquée!',
            ]);
        }
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createRequestForm()
    {
        $sites = $this->getDoctrine()->getRepository(Site::class)->findBy([],['nomSite' => 'ASC']);

        $recherche = new Recherche();
        $recherche->setFin(new \DateTime());
        $recherche->setDebut(new \DateTime('-7 Day'));
        $recherche->setType('Recherche Globale');

        return $this->createForm(RechercheType::class, $recherche, [
            'sites' => $sites,
        ]);
    }

    /**
     * @param int    $debut
     * @param int    $fin
     * @param int    $site
     * @param string $poste
     *
     * @return \CMEN\GoogleChartsBundle\GoogleCharts\Charts\Material\LineChart
     */
    private function createRequestedLineChart(int $debut, int $fin, ?int $site, ?string $poste)
    {
        // Recuperation de la date au format YYYY-MM-DD
        $debutD = strftime('%Y-%m-%d', $debut);
        $finD = strftime('%Y-%m-%d', $fin);
        // Recuperation de la date au format local fr_FR
        setlocale(LC_TIME, 'fr_FR.utf8');
        $debut = strftime('%A %e %B', $debut);
        $fin = strftime('%A %e %B', $fin);

        if ($site) {
            $site = $this->getDoctrine()->getRepository(Site::class)
                ->find($site);

            $recapitulatifs = $this->getDoctrine()
                ->getRepository(Recapitulatif::class)
                ->findByPeriodeAndSite($debutD, $finD, $site);

            $title = 'Evolution de l\'utilisation des postes pour la '.$site->getNomSIte().' du '.$fin.' au '.$debut;
        } elseif ('NOPOSTE' != $poste) {
            $poste = $this->getDoctrine()->getRepository(Poste::class)
                ->findOneByCodePoste($poste);

            $recapitulatifs = $this->getDoctrine()
                ->getRepository(Recapitulatif::class)
                ->findByPeriodeAndPoste($debutD, $finD, $poste);

            $title = 'Evolution de l\'utilisation des postes pour le poste '.$poste->getCodePoste().' du '.$fin.' au '.$debut;
        } else {
            $recapitulatifs = $this->getDoctrine()
                ->getRepository(Recapitulatif::class)
                ->findByPeriode($debutD, $finD);

            $title = 'Evolution globale de l\'utilisation des postes du '.$fin.' au '.$debut;
        }

        if (!$recapitulatifs) {
            return null;
        }

        $dataTable = [['Jour', 'Nombre d\'heures', 'Nombre de sessions']];
        foreach ($recapitulatifs as $recapitulatif) {
            $jour = strftime('%A %e %B', $recapitulatif['date']->getTimestamp());
            $dataTable[] = [$jour, $recapitulatif[1] / 3600, $recapitulatif[2] / 1];
        }

        return ChartBuilder::buildLineChart($title, $dataTable);
    }

    /**
     * @param int    $date
     * @param int    $site
     * @param string $poste
     *
     * @return \CMEN\GoogleChartsBundle\GoogleCharts\Charts\Material\BarChart
     */
    private function createRequestedDateChart(int $date, ?int $site, ?string $poste)
    {
        // Recuperation de la date au format YYYY-MM-DD
        $dateD = strftime('%Y-%m-%d', $date);
        // Recuperation de la date au format local fr_FR
        setlocale(LC_TIME, 'fr_FR.utf8');
        $date = strftime('%A %e %B', $date);

        if ($site) {
            $site = $this->getDoctrine()->getRepository(Site::class)
                ->find($site);

            $recapitulatifs = $this->getDoctrine()
                ->getRepository(Recapitulatif::class)
                ->findByDateAndSite($dateD, $site);

            // Si aucune donnee trouvee
            if (!$recapitulatifs) {
                return null;
            }

            $dataTable = [['Site', 'Nombre d\'heures', 'Nombre de sessions']];
            foreach ($recapitulatifs as $recapitulatif) {
                $dataTable[] = [$site->getNomSite(), $recapitulatif[1] / 3600, $recapitulatif[2] / 1];
            }
            $title = 'Nombre de sessions et temps de connexion du '.$date.' pour '.$site->getNomSite();
        } elseif ('NOPOSTE' != $poste) {
            $poste = $this->getDoctrine()->getRepository(Poste::class)
                ->findOneByCodePoste($poste);

            $recapitulatifs = $this->getDoctrine()
                ->getRepository(Recapitulatif::class)
                ->findByDateAndPoste($dateD, $poste);

            if (!$recapitulatifs) {
                return null;
            }

            $dataTable = [['Poste', 'Nombre d\'heures', 'Nombre de sessions']];
            foreach ($recapitulatifs as $recapitulatif) {
                $dataTable[] = [$poste->getCodePoste(), $recapitulatif[1] / 3600, $recapitulatif[2] / 1];
            }
            $title = 'Nombre de sessions et temps de connexion du '.$date.' pour le poste '.$poste->getCodePoste();
        } else {
            $recapitulatifs = $this->getDoctrine()
                ->getRepository(Recapitulatif::class)
                ->findByDate($dateD);

            if (!$recapitulatifs) {
                return null;
            }

            $dataTable = [['Site', 'Nombre d\'heures', 'Nombre de sessions']];
            foreach ($recapitulatifs as $recapitulatif) {
                $dataTable[] = [$recapitulatif['nomSite'], $recapitulatif[1] / 3600, $recapitulatif[2] / 1];
            }
            $title = 'Nombre de sessions et temps de connexion du '.$date;
        }

        return ChartBuilder::buildBarChart($title, $dataTable);
    }
}
