<?php
/**
 * Created by PhpStorm.
 * User: ilbenjel
 * Date: 16/05/18
 * Time: 14:27.
 */

namespace App\Repository;

use App\Entity\Poste;
use App\Entity\Recapitulatif;
use App\Entity\Site;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class RecapitulatifRepository extends ServiceEntityRepository
{
    /**
     * RecapitulatifRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Recapitulatif::class);
    }

    /**
     * @param Poste $poste
     *
     * @return Recapitulatif[]
     */
    public function findByCodePosteAndAWeekBackward(Poste $poste): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT r 
                  FROM App\Entity\Recapitulatif r
                  WHERE r.codePoste = :poste
                  AND r.date BETWEEN :last_week AND :current_date')
            ->setParameter('poste', $poste)
            ->setParameter('last_week', new \DateTime('-1 Week'))
            ->setParameter('current_date', new \DateTime());

        // returns an array of Recapitulatif objects
        return $query->execute();
    }

    /**
     * @param Site|null $site
     *
     * @return Recapitulatif[]
     */
    public function findBySiteAndAWeekBackward(Site $site): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT SUM(r.dureeCumul), SUM(r.nbConnexions), r.date
            FROM App\Entity\Recapitulatif r, App\Entity\Poste p 
            WHERE r.codePoste = p.codePoste 
            AND p.idSite = :site 
            AND r.date >= :last_week AND r.date <= :current_date 
            GROUP BY r.date 
            ORDER BY r.date ASC')
            ->setParameter('site', $site)
            ->setParameter('last_week', new \DateTime('-1 Week'))
            ->setParameter('current_date', new \DateTime());

        // returns an array of SUM(r.dureeCumul), SUM(r.nbConnexions), r.date
        return $query->execute();
    }

    /**
     * @return array
     */
    public function findAll1WeekBackward(): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT SUM(r.dureeCumul), SUM(r.nbConnexions), s.idSite
            FROM App\Entity\Recapitulatif r, App\Entity\Poste p, App\Entity\Site s
            WHERE r.codePoste = p.codePoste 
            AND p.idSite = s.idSite 
            AND r.date BETWEEN :last_week AND :current_date 
            GROUP BY s.idSite
            ORDER BY r.date ASC, s.nomSite ASC')
            ->setParameter('last_week', new \DateTime('-1 Week'))
            ->setParameter('current_date', new \DateTime());

        // returns an array of SUM(r.dureeCumul), SUM(r.nbConnexions), r.date
        return $query->execute();
    }

    /**
     * @param int $weeks
     *
     * @return mixed
     */
    public function findXWeeksBackward(int $weeks): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT SUM(r.dureeCumul), SUM(r.nbConnexions), r.date
            FROM App\Entity\Recapitulatif r
            WHERE r.date BETWEEN :day_backward AND :current_date 
            GROUP BY r.date 
            ORDER BY r.date ASC')
            ->setParameter('day_backward', new \DateTime('-'.$weeks.' Week'))
            ->setParameter('current_date', new \DateTime());

        // returns an array of SUM(r.dureeCumul), SUM(r.nbConnexions), r.date
        return $query->execute();
    }

    /**
     * @param string $debut
     * @param string $fin
     *
     * @return mixed
     */
    public function findByPeriode(string $debut, string $fin): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT SUM(r.dureeCumul), SUM(r.nbConnexions), r.date
            FROM App\Entity\Recapitulatif r
            WHERE r.date BETWEEN :debut AND :fin 
            GROUP BY r.date 
            ORDER BY r.date ASC')
            ->setParameter('debut', new \DateTime($debut))
            ->setParameter('fin', new \DateTime($fin));

        // returns an array of SUM(r.dureeCumul), SUM(r.nbConnexions), r.date
        return $query->execute();
    }

    /**
     * @param string $debut
     * @param string $fin
     * @param Poste  $poste
     *
     * @return mixed
     */
    public function findByPeriodeAndPoste(string $debut, string $fin, Poste $poste): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT SUM(r.dureeCumul), SUM(r.nbConnexions), r.date
            FROM App\Entity\Recapitulatif r, App\Entity\Poste p
            WHERE r.codePoste = p.codePoste 
            AND p.codePoste = :poste 
            AND r.date BETWEEN :debut AND :fin 
            GROUP BY r.date 
            ORDER BY r.date ASC')
            ->setParameter('debut', new \DateTime($debut))
            ->setParameter('fin', new \DateTime($fin))
            ->setParameter('poste', $poste);

        // returns an array of SUM(r.dureeCumul), SUM(r.nbConnexions), r.date
        return $query->execute();
    }

    /**
     * @param string $debut
     * @param string $fin
     * @param SIte   $site
     *
     * @return mixed
     */
    public function findByPeriodeAndSite(string $debut, string  $fin, Site $site): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT SUM(r.dureeCumul), SUM(r.nbConnexions), r.date
            FROM App\Entity\Recapitulatif r, App\Entity\Site s, App\Entity\Poste p
            WHERE r.date BETWEEN :debut AND :fin 
            AND r.codePoste = p.codePoste
            AND p.idSite = s.idSite
            AND s.idSite = :site
            GROUP BY r.date 
            ORDER BY r.date ASC, s.nomSite ASC')
            ->setParameter('debut', new \DateTime($debut))
            ->setParameter('fin', new \DateTime($fin))
            ->setParameter('site', $site);

        // returns an array of SUM(r.dureeCumul), SUM(r.nbConnexions), r.date
        return $query->execute();
    }

    /**
     * @param string $date
     *
     * @return array
     */
    public function findByDate(string $date): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT SUM(r.dureeCumul), SUM(r.nbConnexions), s.nomSite
            FROM App\Entity\Recapitulatif r, App\Entity\Site s, App\Entity\Poste p
            WHERE r.date = :dateD
            AND p.codePoste = r.codePoste
            AND p.idSite = s.idSite
            GROUP BY s.idSite 
            ORDER BY SUM(r.dureeCumul) ASC, s.nomSite ASC')
            ->setParameter('dateD', new \DateTime($date));

        // returns an array of SUM(r.dureeCumul), SUM(r.nbConnexions), s.nomSite
        return $query->execute();
    }

    /**
     * @param string $date
     * @param Poste  $poste
     *
     * @return mixed
     */
    public function findByDateAndPoste(string $date, Poste $poste): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT SUM(r.dureeCumul), SUM(r.nbConnexions)
            FROM App\Entity\Recapitulatif r, App\Entity\Poste p
            WHERE r.date = :dateD
            AND r.codePoste = p.codePoste
            AND p.codePoste = :poste
            GROUP BY p.codePoste
            ORDER BY SUM(r.dureeCumul) ASC')
            ->setParameter('dateD', new \DateTime($date))
            ->setParameter('poste', $poste);

        // returns an array of SUM(r.dureeCumul), SUM(r.nbConnexions)
        return $query->execute();
    }

    /**
     * @param string $date
     * @param Site   $site
     *
     * @return mixed
     */
    public function findByDateAndSite(string $date, Site $site): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT SUM(r.dureeCumul), SUM(r.nbConnexions)
            FROM App\Entity\Recapitulatif r, App\Entity\Site s, App\Entity\Poste p
            WHERE r.date = :dateD
            AND p.codePoste = r.codePoste
            AND p.idSite = s.idSite
            AND s.idSite = :site
            GROUP BY s.idSite 
            ORDER BY SUM(r.dureeCumul) ASC, s.nomSite ASC')
            ->setParameter('dateD', new \DateTime($date))
            ->setParameter('site', $site);

        // returns an array of SUM(r.dureeCumul), SUM(r.nbConnexions)
        return $query->execute();
    }

    /**
     * @param Poste $poste
     *
     * @return mixed
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function minDureeOuverturePoste(Poste $poste): ?array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT MIN(r.dureeOuverture)
            FROM App\Entity\Recapitulatif r
            WHERE r.codePoste = :poste
            AND r.date BETWEEN :last_week AND :current_date')
            ->setParameter('poste', $poste)
            ->setParameter('last_week', new \DateTime('-1 Week'))
            ->setParameter('current_date', new \DateTime());

        // returns the value of MIN(r.dureeOuverture)
        return $query->getOneOrNullResult();
    }

    /**
     * @param Poste $poste
     *
     * @return mixed
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function calculateUseRate(Poste $poste): ?array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT SUM(r.dureeCumul)/ SUM(r.dureeOuverture)*100 AS useRate
            FROM App\Entity\Recapitulatif r
            WHERE r.codePoste = :poste
            AND r.date BETWEEN :last_week AND :current_date 
            GROUP BY r.codePoste 
            ORDER BY r.date ASC')
            ->setParameter('poste', $poste)
            ->setParameter('last_week', new \DateTime('-1 Week'))
            ->setParameter('current_date', new \DateTime());

        // returns an array of SUM(r.dureeCumul), SUM(r.dureeOuverture)
        return $query->getOneOrNullResult();
    }

    /**
     * @param Poste  $poste
     * @param string $date
     *
     * @return mixed
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function calculateUseRateDate(Poste $poste, string $date): ?array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT SUM(r.dureeCumul)/ SUM(r.dureeOuverture)*100 AS useRate
            FROM App\Entity\Recapitulatif r
            WHERE r.codePoste = :poste
            AND r.date = :date 
            GROUP BY r.codePoste 
            ORDER BY r.date ASC')
            ->setParameter('poste', $poste)
            ->setParameter('date', new \DateTime($date));

        // returns an array of SUM(r.dureeCumul), SUM(r.dureeOuverture)
        return $query->getOneOrNullResult();
    }

    /**
     * @param Poste  $poste
     * @param string $debut
     * @param string $fin
     *
     * @return mixed
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function calculateUseRatePeriode(Poste $poste, string $debut, string $fin): ?array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT SUM(r.dureeCumul)/ SUM(r.dureeOuverture)*100 AS useRate
            FROM App\Entity\Recapitulatif r
            WHERE r.codePoste = :poste
            AND r.date BETWEEN :debut AND :fin 
            GROUP BY r.codePoste 
            ORDER BY r.date ASC')
            ->setParameter('poste', $poste)
            ->setParameter('debut', new \DateTime($debut))
            ->setParameter('fin', new \DateTime($fin));

        // returns an array of SUM(r.dureeCumul), SUM(r.dureeOuverture)
        return $query->getOneOrNullResult();
    }
}
