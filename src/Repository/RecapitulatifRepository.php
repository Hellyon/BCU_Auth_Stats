<?php
/**
 * Created by PhpStorm.
 * User: ilbenjel
 * Date: 16/05/18
 * Time: 14:27
 */

namespace App\Repository;


use App\Entity\Poste;
use App\Entity\Recapitulatif;
use App\Entity\Site;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\Types\DateType;
use Symfony\Bridge\Doctrine\RegistryInterface;

class RecapitulatifRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Recapitulatif::class);
    }

    /**
     * @param $poste
     * @return Recapitulatif[]
     */
    public function findByCodePosteAndAWeekBackward(?Poste $poste): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT r FROM App\Entity\Recapitulatif r
                  WHERE r.codePoste = :poste
                  AND r.date BETWEEN :last_week AND :current_date')
            ->setParameter('poste', $poste)
            ->setParameter('last_week', new \DateTime('-6 Day'))
            ->setParameter('current_date', new \DateTime());

        // returns an array of Recapitulatif objects
        return $query->execute();
    }

    /**
     * @param $poste
     * @return Recapitulatif[]
     */
    public function findBySiteAndAWeekBackward(?Site $site): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT SUM(r.dureeCumul), SUM(r.nbConnexions), r.date
            FROM App\Entity\Recapitulatif r, App\Entity\Poste p 
            WHERE r.codePoste = p.codePoste 
            AND p.idSite = :site 
            AND r.date BETWEEN :last_week AND :current_date 
            GROUP BY r.date 
            ORDER BY r.date ASC')
            ->setParameter('site', $site)
            ->setParameter('last_week', new \DateTime('-6 Day'))
            ->setParameter('current_date', new \DateTime());

        // returns an array of SUM(r.dureeCumul), SUM(r.nbConnexions), r.date
        return $query->execute();
    }
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
            ORDER BY r.date ASC')
            ->setParameter('last_week', new \DateTime('-6 Day'))
            ->setParameter('current_date', new \DateTime());

        // returns an array of SUM(r.dureeCumul), SUM(r.nbConnexions), r.date
        return $query->execute();
    }

    public function findXWeeksBackward($weeks){

        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT SUM(r.dureeCumul), SUM(r.nbConnexions), r.date
            FROM App\Entity\Recapitulatif r
            WHERE r.date BETWEEN :day_backward AND :current_date 
            GROUP BY r.date 
            ORDER BY r.date ASC')
            ->setParameter('day_backward', new \DateTime('-'.(7*$weeks-1).' Day'))
            ->setParameter('current_date', new \DateTime());

        // returns an array of SUM(r.dureeCumul), SUM(r.nbConnexions), r.date
        return $query->execute();
    }

    public function findByPeriod($debut, $fin){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT SUM(r.dureeCumul), SUM(r.nbConnexions), r.date
            FROM App\Entity\Recapitulatif r
            WHERE r.date BETWEEN :debut AND :fin 
            GROUP BY r.date 
            ORDER BY r.date ASC')
            ->setParameter('debut', new \DateTime($fin))
            ->setParameter('fin', new \DateTime($debut));

        // returns an array of SUM(r.dureeCumul), SUM(r.nbConnexions), r.date
        return $query->execute();
    }

    public function findByDate($date){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT SUM(r.dureeCumul), SUM(r.nbConnexions), s.nomSite
            FROM App\Entity\Recapitulatif r, App\Entity\Site s, App\Entity\Poste p
            WHERE r.date = :dateD
            AND p.codePoste = r.codePoste
            AND p.idSite = s.idSite
            GROUP BY s.idSite 
            ORDER BY SUM(r.dureeCumul) ASC')
            ->setParameter('dateD', new \DateTime($date));

        // returns an array of SUM(r.dureeCumul), SUM(r.nbConnexions), s.nomSite
        return $query->execute();
    }
}