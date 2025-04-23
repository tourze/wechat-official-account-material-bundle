<?php

namespace WechatOfficialAccountMaterialBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WechatOfficialAccountMaterialBundle\Entity\MaterialCount;

/**
 * @method MaterialCount|null find($id, $lockMode = null, $lockVersion = null)
 * @method MaterialCount|null findOneBy(array $criteria, array $orderBy = null)
 * @method MaterialCount[]    findAll()
 * @method MaterialCount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MaterialCountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MaterialCount::class);
    }
}
