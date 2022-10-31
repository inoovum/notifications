<?php
namespace SteinbauerIT\Notifications\Domain\Model;

/*
 * This file is part of the SteinbauerIT.Notifications package.
 */

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Security\Account;

/**
 * @Flow\Entity
 */
class AccountSettings
{

    /**
     * @ORM\OneToOne
     * @var Account
     */
    protected $account;

    /**
     * @var array
     */
    protected $settings;


    /**
     * Construct
     */
    public function __construct()
    {
    }

    /**
     * @return Account
     */
    public function getAccount(): Account
    {
        return $this->account;
    }

    /**
     * @param Account $account
     * @return void
     */
    public function setAccount(Account $account)
    {
        $this->account = $account;
    }

    /**
     * @return array
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * @param array $settings
     * @return void
     */
    public function setSettings(array $settings)
    {
        $this->settings = $settings;
    }

}
