<?php

namespace App\Repository;

use App\Entity\Division;
use App\Entity\GameInfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameInfo>
 *
 * @method GameInfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameInfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameInfo[]    findAll()
 * @method GameInfo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameInfoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameInfo::class);
    }

    public function save(GameInfo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(GameInfo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
