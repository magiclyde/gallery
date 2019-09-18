<?php

//https://stackoverflow.com/questions/55179959/symfony-4-serviceentityrepository-vs-entityrepository

namespace App\Repository;

use App\Entity\Gallery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Gallery|null find($id, $lockMode = null, $lockVersion = null)
 * @method Gallery|null findOneBy(array $criteria, array $orderBy = null)
 * @method Gallery[]    findAll()
 * @method Gallery[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GalleryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Gallery::class);
    }

    public function findNewest($limit = 5)
    {
        return $this->findBy([], ['createdAt' => 'DESC'], $limit);
    }

    public function findRelated(Gallery $gallery, $limit = 5)
    {
        return $this->findBy(['user' => $gallery->getUser()], ['createdAt' => 'DESC'], $limit);
    }
}
