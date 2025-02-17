<?php

namespace App\Repository;

use App\Entity\Transacao;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Transacao>
 *
 * @method Transacao|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transacao|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transacao[]    findAll()
 * @method Transacao[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransacaoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transacao::class);
    }

    public function add(Transacao $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Transacao $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function fetchTop5AcoesCompradasMes(): array
    {
        $firstDayOfMonth = date("Y-m-01");
        $lastDayOfMonth = (new DateTime())
            ->modify("last day of this month")
            ->format('Y-m-d');

        $stmt = $this->getEntityManager()->getConnection()->prepare('
            SELECT t.acao_id, SUM(t.quantidade) AS total, a.nome
            FROM acoes_baratas.transacao t JOIN acoes_baratas.acao a ON t.acao_id = a.id
            WHERE t.data BETWEEN "' . $firstDayOfMonth . '" AND "' . $lastDayOfMonth . '" AND t.tipo_id = 1
            GROUP BY t.acao_id 
            ORDER BY total DESC
            LIMIT 0, 5'
        );

        $result = $stmt->executeQuery();

        if ($result->rowCount() == 0) {
            return [];
        }

        return $result->fetchAllAssociative();
    }

    public function fetchTop5AcoesVendidasMes(): array
    {
        $firstDayOfMonth = date("Y-m-01");
        $lastDayOfMonth = (new DateTime())
            ->modify("last day of this month")
            ->format('Y-m-d');

        $stmt = $this->getEntityManager()->getConnection()->prepare('
            SELECT t.acao_id, SUM(t.quantidade) AS total, a.nome
            FROM acoes_baratas.transacao t JOIN acoes_baratas.acao a ON t.acao_id = a.id
            WHERE t.data BETWEEN "' . $firstDayOfMonth . '" AND "' . $lastDayOfMonth . '" AND t.tipo_id = 2
            GROUP BY t.acao_id 
            ORDER BY total DESC
            LIMIT 0, 5'
        );

        $result = $stmt->executeQuery();

        if ($result->rowCount() == 0) {
            return [];
        }

        return $result->fetchAllAssociative();
    }

    public function fetchComprasMesAMes(): array
    {
        $comprasMesAMes = [];
        $year = (new DateTime())->format('Y');

        for ($i = 1; $i <= 12; $i++) { 
            $month = str_pad($i, 2, '0', STR_PAD_LEFT);
            $firstDayOfMonth = date("Y-$month-01");
            $lastDayOfMonth = (new DateTime("$year-$month-01"))
                ->modify("last day of this month")
                ->format('Y-m-d');

            $stmt = $this->getEntityManager()->getConnection()->prepare('
                SELECT SUM(t.quantidade) AS total
                FROM acoes_baratas.transacao t JOIN acoes_baratas.acao a ON t.acao_id = a.id
                WHERE t.data BETWEEN "' . $firstDayOfMonth . '" AND "' . $lastDayOfMonth . '" AND t.tipo_id = 1
                GROUP BY t.tipo_id
                ORDER BY t.data
            ');

            $result = $stmt->executeQuery();

            if ($result->rowCount() == 0) {
                $comprasMesAMes[] = "0";
                continue;
            }

            $comprasMesAMes[] = $result->fetchOne();
        }

        return $comprasMesAMes;
    }

    public function fetchVendasMesAMes(): array
    {
        $vendasMesAMes = [];
        $year = (new DateTime())->format('Y');

        for ($i = 1; $i <= 12; $i++) { 
            $month = str_pad($i, 2, '0', STR_PAD_LEFT);
            $firstDayOfMonth = date("Y-$month-01");
            $lastDayOfMonth = (new DateTime("$year-$month-01"))
                ->modify("last day of this month")
                ->format('Y-m-d');

            $stmt = $this->getEntityManager()->getConnection()->prepare('
                SELECT SUM(t.quantidade) AS total
                FROM acoes_baratas.transacao t JOIN acoes_baratas.acao a ON t.acao_id = a.id
                WHERE t.data BETWEEN "' . $firstDayOfMonth . '" AND "' . $lastDayOfMonth . '" AND t.tipo_id = 2
                GROUP BY t.tipo_id
                ORDER BY t.data
            ');

            $result = $stmt->executeQuery();

            if ($result->rowCount() == 0) {
                $vendasMesAMes[] = "0";
                continue;
            }

            $vendasMesAMes[] = $result->fetchOne();
        }

        return $vendasMesAMes;
    }

//    /**
//     * @return Transacao[] Returns an array of Transacao objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Transacao
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
