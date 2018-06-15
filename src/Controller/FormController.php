<?php

namespace App\Controller;

use App\Entity\ChartBuilder;
use App\Entity\Poste;
use App\Entity\Recapitulatif;
use App\Entity\Recherche;
use App\Entity\Site;
use App\Form\RechercheType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FormController.
 */
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
        $formulaire = $this->createRequestForm(null);
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
                            'debut' => $recherche->getDebut()->getTimeStamp(),
                            'fin' => $recherche->getFin()->getTimeStamp(),
                        ]));
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
                            'debut' => $recherche->getDebut()->getTimeStamp(),
                            'fin' => $recherche->getFin()->getTimeStamp(),
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
                            'debut' => $recherche->getDebut()->getTimeStamp(),
                            'fin' => $recherche->getFin()->getTimeStamp(),
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
        $recherche = new Recherche();
        $jour = new \DateTime();
        $jour->setTimestamp($date);
        $recherche->setDebut($jour);

        if ($site) {
            $recherche->setSite($this->getDoctrine()->getRepository(Site::class)->find($site));
        }
        if ('NOPOSTE' != $poste) {
            $posteD = $this->getDoctrine()->getRepository(Poste::class)->find($poste);
            $recherche->setPoste($posteD);

            $useRate = $this->getDoctrine()->getRepository(Recapitulatif::class)
                ->calculateUseRateDate($posteD, strftime('%Y-%m-%d', $date));
            setlocale(LC_TIME, 'fr_FR.utf8');
            $info = $posteD->getCodePoste().' a été utilisé à '.$useRate['useRate'].'% du temps disponible le '.
                strftime('%A %e %B', $date);
        }
        $formulaire = $this->createRequestForm($recherche);
        $requestChart = $this->createRequestedDateChart($date, $site, $poste);

        if ($requestChart) {
            if ('NOPOSTE' != $poste) {
            }
            if ($site) {
                $usedPostes = $this->getDoctrine()->getRepository(Site::class)->countUsedPostesDate($site, strftime('%Y-%m-%d', $date));
                $totalPostes = $this->getDoctrine()->getRepository(Poste::class)->findBy(['idSite' => $site]);
                $info = $this->getUsedPostesInfo($usedPostes['used'], count($totalPostes));
            }

            return $this->render('form/form.html.twig', [
                'form' => $formulaire->createView(),
                'requestChart' => $requestChart,
                'info' => $info,
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
        $recherche = new Recherche();

        $jour = new \DateTime();
        $jour->setTimestamp($debut);
        $recherche->setDebut($jour);

        $jour = new \DateTime();
        $jour->setTimestamp($fin);
        $recherche->setFin($jour);

        if ($site) {
            $recherche->setSite($this->getDoctrine()->getRepository(Site::class)->find($site));
        }
        if ('NOPOSTE' != $poste) {
            $posteD = $this->getDoctrine()->getRepository(Poste::class)->find($poste);

            $recherche->setPoste($posteD);
        }
        $formulaire = $this->createRequestForm($recherche);
        $requestChart = $this->createRequestedPeriodeChart($debut, $fin, $site, $poste);

        if ($requestChart) {
            if ('NOPOSTE' != $poste) {
                $useRate = $this->getDoctrine()->getRepository(Recapitulatif::class)
                    ->calculateUseRatePeriode($posteD, strftime('%Y-%m-%d', $debut), strftime('%Y-%m-%d', $fin));
                setlocale(LC_TIME, 'fr_FR.utf8');
                $info = $posteD->getCodePoste().' a été utilisé à '.$useRate['useRate'].'% du temps disponible entre le '.
                    strftime('%A %e %B', $debut).' et le '.strftime('%A %e %B', $fin);
            }
            if ($site) {
                $usedPostes = $this->getDoctrine()->getRepository(Site::class)
                    ->countUsedPostesPeriode($site, strftime('%Y-%m-%d', $debut), strftime('%Y-%m-%d', $fin));
                $totalPostes = $this->getDoctrine()->getRepository(Poste::class)->findBy(['idSite' => $site]);
                $info = $this->getUsedPostesInfo($usedPostes['used'], count($totalPostes));
            }

            return $this->render('form/form.html.twig', [
                'form' => $formulaire->createView(),
                'requestChart' => $requestChart,
                'info' => $info,
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
     * @Route("/{_locale}/siteAjax", name="site_ajax_call")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function ajaxAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        $id = $request->query->get('site_id');
        $postesResponse = [];
        $postes = $this->getDoctrine()->getRepository(Poste::class)->findBySite($id)->getQuery()->getResult();

        $postesResponse['Choisir un Poste'] = '';
        foreach ($postes as $poste) {
            $postesResponse[$poste->getCodePoste()] = $poste->getCodePoste();
        }

        return new JsonResponse($postesResponse);
    }

    /**
     * @param Recherche $recherche
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createRequestForm(?Recherche $recherche)
    {
        if (!$recherche) {
            $recherche = new Recherche();
            $recherche->setFin(new \DateTime());
            $recherche->setDebut(new \DateTime('-7 Day'));
        }

        $recherche->setType('Recherche Globale');

        return $this->createForm(RechercheType::class, $recherche);
    }

    /**
     * @param int    $debut
     * @param int    $fin
     * @param int    $site
     * @param string $poste
     *
     * @return \CMEN\GoogleChartsBundle\GoogleCharts\Charts\Material\LineChart
     */
    private function createRequestedPeriodeChart(int $debut, int $fin, ?int $site, ?string $poste)
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

            $title = 'Evolution de l\'utilisation des postes pour la '.$site->getNomSIte().' du '.$debut.' au '.$fin;
        } elseif ('NOPOSTE' != $poste) {
            $poste = $this->getDoctrine()->getRepository(Poste::class)
                ->findOneByCodePoste($poste);

            $recapitulatifs = $this->getDoctrine()
                ->getRepository(Recapitulatif::class)
                ->findByPeriodeAndPoste($debutD, $finD, $poste);

            $title = 'Evolution de l\'utilisation des postes pour le poste '.$poste->getCodePoste().' du '.$debut.' au '.$fin;
        } else {
            $recapitulatifs = $this->getDoctrine()
                ->getRepository(Recapitulatif::class)
                ->findByPeriode($debutD, $finD);

            $title = 'Evolution globale de l\'utilisation des postes du '.$debut.' au '.$fin;
        }

        if (count($recapitulatifs) <= 1) {
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

    /**
     * @param int $usedPostes
     * @param int $totalPostes
     *
     * @return string
     */
    private function getUsedPostesInfo(int $usedPostes, int $totalPostes): string
    {
        if ($usedPostes == $totalPostes) {
            return 'Tous les postes en libre accès ont été utilisés';
        } elseif (0 == $usedPostes) {
            return 'Aucun poste en libre accès n\'a été utilisé';
        } else {
            return $usedPostes.' sur '.$totalPostes.' postes en libre accès ont été utilisés';
        }
    }
}
