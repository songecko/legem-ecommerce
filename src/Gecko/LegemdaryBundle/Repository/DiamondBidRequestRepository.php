<?php

namespace Gecko\LegemdaryBundle\Repository;

use FOS\UserBundle\Model\UserInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;


class DiamondBidRequestRepository extends EntityRepository
{
    public function createByVendorPaginator(UserInterface $vendor, $sorting = array())
    {
        $queryBuilder = $this->getCollectionQueryBuilderByVendor($vendor);

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
    
    protected function getCollectionQueryBuilderByVendor(UserInterface $vendor, array $sorting = array())
    {
    	$queryBuilder = $this->getCollectionQueryBuilder();
    
    	$queryBuilder
	    	->leftJoin('o.diamondBids', 'diamondBid')
	    	/*->andWhere('diamondBid.vendor IS NULL OR diamondBid.vendor != :vendor')
	    	->setParameter('vendor', $vendor)*/
    	;
    
    	if (empty($sorting)) {
    		if (!is_array($sorting)) {
    			$sorting = array();
    		}
    		$sorting['updatedAt'] = 'desc';
    	}
    	
    	$this->applySorting($queryBuilder, $sorting);
    
    	return $queryBuilder;
    }
}
