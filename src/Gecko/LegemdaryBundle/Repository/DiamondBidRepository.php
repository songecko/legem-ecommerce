<?php

namespace Gecko\LegemdaryBundle\Repository;

use FOS\UserBundle\Model\UserInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;


class DiamondBidRepository extends EntityRepository
{   
    /**
     * Gets bid requests for user.
     *
     * @param  UserInterface $user
     * @param  array         $sorting
     * @return array
     */
    public function findByVendor(UserInterface $user, array $sorting = array())
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
