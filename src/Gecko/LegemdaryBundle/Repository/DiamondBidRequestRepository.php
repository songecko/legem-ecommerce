<?php

namespace Gecko\LegemdaryBundle\Repository;

use FOS\UserBundle\Model\UserInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;


class DiamondBidRequestRepository extends EntityRepository
{
    /**
     * Create filter paginator.
     *
     * @param array   $criteria
     * @param array   $sorting
     * @param Boolean $deleted
     *
     * @return PagerfantaInterface
     */
    public function createFilterPaginator($criteria = array(), $sorting = array(), $deleted = false)
    {
        $queryBuilder = parent::getCollectionQueryBuilder();

        if ($deleted) {
            $this->_em->getFilters()->disable('softdeleteable');
        }

        if (!empty($criteria['number'])) {
            $queryBuilder
                ->andWhere('o.number = :number')
                ->setParameter('number', $criteria['number'])
            ;
        }
        if (!empty($criteria['totalFrom'])) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->gte('o.total', ':totalFrom'))
                ->setParameter('totalFrom', $criteria['totalFrom'] * 100)
            ;
        }
        if (!empty($criteria['totalTo'])) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->lte('o.total', ':totalTo'))
                ->setParameter('totalTo', $criteria['totalTo'] * 100)
            ;
        }
        if (!empty($criteria['createdAtFrom'])) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->gte('o.createdAt', ':createdAtFrom'))
                ->setParameter('createdAtFrom', $criteria['createdAtFrom'])
            ;
        }
        if (!empty($criteria['createdAtTo'])) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->lte('o.createdAt', ':createdAtTo'))
                ->setParameter('createdAtTo', $criteria['createdAtTo'])
            ;
        }

        if (empty($sorting)) {
            if (!is_array($sorting)) {
                $sorting = array();
            }
            $sorting['updatedAt'] = 'desc';
        }

        $this->applySorting($queryBuilder, $sorting);

        return $this->getPaginator($queryBuilder);
    }
    
    /**
     * Gets bid requests for user.
     *
     * @param  UserInterface $user
     * @param  array         $sorting
     * @return array
     */
    public function findByUser(UserInterface $user, array $sorting = array())
    {
    	$queryBuilder = $this->getCollectionQueryBuilderByUser($user, $sorting);
    
    	return $queryBuilder
    	->getQuery()
    	->getResult()
    	;
    }
    
    protected function getCollectionQueryBuilderByUser(UserInterface $user, array $sorting = array())
    {
    	$queryBuilder = $this->getCollectionQueryBuilder();
    
    	$queryBuilder
    	->innerJoin('o.orderItem', 'item')
    	->innerJoin('item.order', 'order')
    	->innerJoin('order.user', 'user')
    	->andWhere('user = :user')
    	->setParameter('user', $user)
    	;
    
    	$this->applySorting($queryBuilder, $sorting);
    
    	return $queryBuilder;
    }
}
