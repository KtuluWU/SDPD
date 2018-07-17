<?php

namespace App\Repository;

use App\Entity\IFG_TEST2\UploadPdf;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UploadPdf|null find($id, $lockMode = null, $lockVersion = null)
 * @method UploadPdf|null findOneBy(array $criteria, array $orderBy = null)
 * @method UploadPdf[]    findAll()
 * @method UploadPdf[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UploadPdfRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UploadPdf::class);
    }

//    /**
//     * @return UploadPdf[] Returns an array of UploadPdf objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UploadPdf
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
