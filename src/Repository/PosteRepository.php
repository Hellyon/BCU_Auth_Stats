<?php
/**
 * Created by PhpStorm.
 * User: ilbenjel
 * Date: 07/06/18
 * Time: 16:22.
 */

namespace App\Repository;

use App\Entity\Poste;
use App\Entity\Site;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class PosteRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Poste::class);
    }

    /**
     * @param Site $site
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findBySite(?Site $site)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.idSite = :site')
            ->setParameter('site', $site);
    }
}
