<?php
/**
 * Created by PhpStorm.
 * User: ilbenjel
 * Date: 05/06/18
 * Time: 10:52.
 */

namespace App\Repository;

use App\Entity\Session;
use App\Entity\Site;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class SessionRepository extends ServiceEntityRepository
{
    /**
     * SessionRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Session::class);
    }

    /**
     * @param Site $site
     *
     * @return mixed
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function rushHours(Site $site): ?array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery("SELECT SUM(CASE WHEN s.heureDebut BETWEEN '08:00:00' AND '10:00:00' THEN 1 ELSE 0 END) AS H8,
            SUM(CASE WHEN s.heureDebut BETWEEN '10:00:00' AND '12:00:00' THEN 1 ELSE 0 END) AS H10,
            SUM(CASE WHEN s.heureDebut BETWEEN '12:00:00' AND '14:00:00' THEN 1 ELSE 0 END) AS H12,
            SUM(CASE WHEN s.heureDebut BETWEEN '14:00:00' AND '16:00:00' THEN 1 ELSE 0 END) AS H14,
            SUM(CASE WHEN s.heureDebut BETWEEN '16:00:00' AND '18:00:00' THEN 1 ELSE 0 END) AS H16,
            SUM(CASE WHEN s.heureDebut BETWEEN '18:00:00' AND '20:00:00' THEN 1 ELSE 0 END) AS H18,
            SUM(CASE WHEN s.heureDebut BETWEEN '20:00:00' AND '22:00:00' THEN 1 ELSE 0 END) AS H20,
            SUM(CASE WHEN s.heureDebut BETWEEN '22:00:00' AND '24:00:00' THEN 1 ELSE 0 END) AS H22
            FROM App\Entity\Session s, App\Entity\Poste p, App\Entity\Site si
            WHERE s.codePoste = p.codePoste
            AND p.idSite = si.idSite
            AND si.idSite = :site
            AND s.date BETWEEN :last_week AND :current_date")
            ->setParameter('site', $site)
            ->setParameter('last_week', new \DateTime('-1 Week'))
            ->setParameter('current_date', new \DateTime());

        // returns an array of rush hours
        return $query->getOneOrNullResult();
    }
}
