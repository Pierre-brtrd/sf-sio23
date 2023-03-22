<?php

namespace App\Repository;

use App\Entity\Article;
use App\Search\SearchData;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function save(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Recherche des derniers articles avec limite (non obligatoire)
     *
     * @param integer $max Nombre maximum de résultats (facultatif)
     * @return array
     */
    public function findEnableOrderByDate(?int $max = null): array
    {
        $query = $this->createQueryBuilder('a')
            ->andWhere('a.enabled = true')
            ->orderBy('a.createdAt', 'DESC');

        if ($max) {
            $query->setMaxResults($max);
        }

        return $query
            ->getQuery()
            ->getResult();
    }

    public function findSearchData(SearchData $search): array
    {
        $query = $this->createQueryBuilder('a')
            ->select('a', 'u', 'c', 'i')
            ->join('a.author', 'u')
            ->leftJoin('a.categories', 'c')
            ->leftJoin('a.images', 'i');

        /* On filtre sur le titre de l'article si $search->getQuery() n'est pas vide */
        if (!empty($search->getQuery())) {
            $query->andWhere('a.title LIKE :title')
                ->setParameter('title', "%{$search->getQuery()}%");
        }

        if (!empty($search->getTags())) {
            $query->andWhere('c.id IN (:tags)')
                ->setParameter('tags', $search->getTags());
        }

        if (!empty($search->getAuthors())) {
            $query->andWhere('u.id IN (:authors)')
                ->setParameter('authors', $search->getAuthors());
        }

        return $query
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Article[] Returns an array of Article objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Article
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
