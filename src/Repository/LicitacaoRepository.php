<?php

namespace App\Repository;

use App\Entity\Licitacao;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Licitacao>
 */
class LicitacaoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Licitacao::class);
    }

    // Cria uma nova licitação
    public function save(Licitacao $licitacao): void
    {
        $this->getEntityManager()->persist($licitacao);
        $this->getEntityManager()->flush();
    }

    public function remove(Licitacao $licitacao, bool $flush = true): void {
        $this->getEntityManager()->remove($licitacao);
        if($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //Verifica se já existe uma licitação pelo numero do edital
    public function findByEdital(string $numeroEdital): ?Licitacao
    {
        return $this->createQueryBuilder('l')
        ->andWhere('l.numeroEdital = :numeroEdital')
        ->setParameter('numeroEdital', $numeroEdital)
        ->getQuery()
        ->getOneOrNullResult();
    }

    //    /**
    //     * @return Licitacao[] Returns an array of Licitacao objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Licitacao
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
