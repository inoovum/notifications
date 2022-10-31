<?php
namespace SteinbauerIT\Notifications\Domain\Repository;

/*
 * This file is part of the SteinbauerIT.Notifications package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\QueryInterface;
use Neos\Flow\Persistence\QueryResultInterface;
use Neos\Flow\Persistence\Repository;
use Neos\Flow\Security\Account;
use SteinbauerIT\Notifications\Domain\Model\Notification;

/**
 * @Flow\Scope("singleton")
 */
class NotificationRepository extends Repository
{

    /**
     * @var array
     */
    protected $defaultOrderings = [
        'createdAt' => QueryInterface::ORDER_DESCENDING
    ];

    /**
     * @param Account $account
     * @param string $distributionMethod
     * @param string $category
     * @param int $limit
     * @param int $offset
     * @param bool $unreadOnly
     * @return QueryResultInterface
     */
    public function findDistributed(Account $account = null, string $distributionMethod = null, string $category = null, int $limit = 0, int $offset = 0, bool $unreadOnly = false): QueryResultInterface
    {
        $query = $this->createQuery();
        $constraints = $this->getDefaultConstraints($query, $account, $distributionMethod, $category);
        $constraints[] = $query->logicalNot($query->equals('wasDistributedAt', null));
        if ($unreadOnly === true) {
            $constraints[] = $query->equals('wasReadAt', null);
        }
        if (count($constraints) > 0) {
            $query = $query->matching(
                $query->logicalAnd($constraints)
            );
        }
        if ($limit > 0) {
            $query->setLimit($limit);
        }
        if ($offset > 0) {
            $query->setOffset($offset);
        }
        return $query->execute();
    }

    /**
     * @param Account $account
     * @param string $distributionMethod
     * @param string $category
     * @return QueryResultInterface
     */
    public function findDistributable(Account $account = null, string $distributionMethod = null, string $category = null): QueryResultInterface
    {
        $now = new \DateTime();
        $query = $this->createQuery();
        $constraints = $this->getDefaultConstraints($query, $account, $distributionMethod, $category);
        $constraints[] = $query->logicalOr(
            $query->greaterThanOrEqual('distributeAt', $now),
            $query->equals('distributeAt', null)
        );
        $constraints[] = $query->equals('wasDistributedAt', null);
        if (count($constraints) > 0) {
            $query = $query->matching(
                $query->logicalAnd($constraints)
            );
        }
        return $query->execute();
    }

    /**
     * @param string $summaryType
     * @param Account $account
     * @param string $distributionMethod
     * @param string $category
     * @return QueryResultInterface
     */
    public function findSummaryDistributable(string $summaryType, Account $account = null, string $distributionMethod = null, string $category = null): QueryResultInterface
    {
        $now = new \DateTime();
        $query = $this->createQuery();
        $constraints = $this->getDefaultConstraints($query, $account, $distributionMethod, $category, $summaryType);
        $constraints[] = $query->logicalOr(
            $query->greaterThanOrEqual('distributeAt', $now),
            $query->equals('distributeAt', null)
        );
        $constraints[] = $query->equals('wasDistributedAt', null);
        $constraints[] = $query->equals('summaryNotification', null);
        if (count($constraints) > 0) {
            $query = $query->matching(
                $query->logicalAnd($constraints)
            );
        }
        return $query->execute();
    }

    /**
     * @param Account $account
     * @param string $distributionMethod
     * @param string $category
     * @return QueryResultInterface
     */
    public function findUndistributed(Account $account = null, string $distributionMethod = null, string $category = null): QueryResultInterface
    {
        $query = $this->createQuery();
        $constraints = $this->getDefaultConstraints($query, $account, $distributionMethod, $category);
        $constraints[] = $query->equals('wasDistributedAt', null);
        if (count($constraints) > 0) {
            $query = $query->matching(
                $query->logicalAnd($constraints)
            );
        }
        return $query->execute();
    }

    /**
     * @param QueryInterface $query
     * @param Account $account
     * @param string $distributionMethod
     * @param string $category
     * @param string $asSummary
     * @return array
     */
    protected function getDefaultConstraints(QueryInterface $query, Account $account = null, string $distributionMethod = null, string $category = null, string $asSummary = Notification::SUMMARY_NONE): array
    {
        $constraints = array();
        $constraints[] = $query->equals('asSummary', $asSummary);
        if ($account !== null) {
            $constraints[] = $query->equals('account', $account);
        }
        if ($distributionMethod !== null) {
            $constraints[] = $query->equals('distributionMethod', $distributionMethod);
        }
        if ($category !== null) {
            $constraints[] = $query->equals('category', $category);
        }
        return $constraints;
    }

}
