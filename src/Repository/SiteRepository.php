<?php
/**
 * Created by PhpStorm.
 * User: ilbenjel
 * Date: 14/06/18
 * Time: 16:17.
 */

namespace App\Repository;

use App\Entity\Site;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class SiteRepository.
 */
class SiteRepository extends ServiceEntityRepository
{
    /**
     * SiteRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Site::class);
    }

    /**
     * @param $site
     *
     * @return null|array
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countUsedPostes($site): ?array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT count(distinct(p.codePoste)) as used
                  FROM App\Entity\Site s, App\Entity\Poste p, App\Entity\Recapitulatif r
                  WHERE s.idSite = :site
                  AND p.idSite = s.idSite
                  AND p.codePoste = r.codePoste
                  AND r.date BETWEEN :last_week AND :current_date')
            ->setParameter('site', $site)
            ->setParameter('last_week', new \DateTime('-1 Week'))
            ->setParameter('current_date', new \DateTime());

        // returns an array
        return $query->getOneOrNullResult();
    }

    /**
     * @param $site
     * @param $debut
     * @param $fin
     *
     * @return null|array
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countUsedPostesPeriode($site, $debut, $fin): ?array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT count(distinct(p.codePoste)) as used
                  FROM App\Entity\Site s, App\Entity\Poste p, App\Entity\Recapitulatif r
                  WHERE s.idSite = :site
                  AND p.idSite = s.idSite
                  AND p.codePoste = r.codePoste
                  AND r.date BETWEEN :debut AND :fin')
            ->setParameter('site', $site)
            ->setParameter('debut', new \DateTime($debut))
            ->setParameter('fin', new \DateTime($fin));

        // returns an array
        return $query->getOneOrNullResult();
    }

    /**
     * @param $site
     * @param $date
     *
     * @return null|array
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countUsedPostesDate($site, $date): ?array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT count(distinct(p.codePoste)) as used
                  FROM App\Entity\Site s, App\Entity\Poste p, App\Entity\Recapitulatif r
                  WHERE s.idSite = :site
                  AND p.idSite = s.idSite
                  AND p.codePoste = r.codePoste
                  AND r.date = :date')
            ->setParameter('site', $site)
            ->setParameter('date', new \DateTime($date));

        // returns an array
        return $query->getOneOrNullResult();
    }
}
