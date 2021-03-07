<?php

declare(strict_types=1);

namespace ScottSmith\Doctrine\Integration\Testing;

use Exception;

trait DatabaseTransactionTrait
{
    /**
     * Create transaction on Entity Manager before every test
     *
     * @before
     *
     * @throws Exception
     */
    public function setUpTransaction()
    {
        $groups = $this->getGroups();
        if (in_array('Integration', $groups) && method_exists($this, 'getConnection')) {
            $this->getConnection()->beginTransaction();
        }
    }

    /**
     * Roll back transaction on Entity Manager after every test
     *
     * @after
     */
    public function tearDownTransaction()
    {
        $groups = $this->getGroups();
        if (in_array('Integration', $groups) && method_exists($this, 'getConnection')) {
            $this->getConnection()->rollback();
        }
    }
}
