<?php

namespace App\Repository;

use App\Entity\IFG_SDPD\InfoToSaisie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method InfoToSaisie|null find($id, $lockMode = null, $lockVersion = null)
 * @method InfoToSaisie|null findOneBy(array $criteria, array $orderBy = null)
 * @method InfoToSaisie[]    findAll()
 * @method InfoToSaisie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InfoToSaisieRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, InfoToSaisie::class);
    }

//    /**
//     * @return InfoToSaisie[] Returns an array of InfoToSaisie objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?InfoToSaisie
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
