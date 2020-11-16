<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Config;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Config|null find($id, $lockMode = null, $lockVersion = null)
 * @method Config|null findOneBy(array $criteria, array $orderBy = null)
 * @method Config[]    findAll()
 * @method Config[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConfigRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Config::class);
    }
  
    public function getConfig(string $value): ?string
    {
        $config = $this->createQueryBuilder('c')
            ->andWhere('c.code = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();

        if ($config === null) {
            return null;
        }

        return $config->getValue();
    }

    public function getConfigEntity(string $value): ?Config
    {
        $config = $this->createQueryBuilder('c')
            ->andWhere('c.code = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();

        if ($config === null) {
            return new Config();
        }

        return $config;
    }
}
