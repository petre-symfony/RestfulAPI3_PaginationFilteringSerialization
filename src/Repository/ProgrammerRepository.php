<?php

namespace App\Repository;

use App\Entity\Programmer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Programmer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Programmer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Programmer[]    findAll()
 * @method Programmer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProgrammerRepository extends ServiceEntityRepository {
  public function __construct(RegistryInterface $registry) {
    parent::__construct($registry, Programmer::class);
  }

	public function findAllQueryBuilder($filter = ''){
		$qb = $this->createQueryBuilder('programmer');

		if ($filter) {
			$qb
				->andWhere('programmer.nickname LIKE :filter OR programmer.tagLine LIKE :filter')
				->setParameter('filter', '%'.$filter.'%');
		}

		return $qb;
	}
//    /**
//     * @return Programmer[] Returns an array of Programmer objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Programmer
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
