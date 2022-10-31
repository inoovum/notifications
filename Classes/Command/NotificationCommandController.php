<?php
namespace SteinbauerIT\Notifications\Command;

/*
 * This file is part of the SteinbauerIT.Notifications package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\CommandController;
use SteinbauerIT\Notifications\Domain\Model\Notification;
use SteinbauerIT\Notifications\Domain\Repository\NotificationRepository;
use SteinbauerIT\Notifications\Domain\Service\NotificationService;

/**
 * @Flow\Scope("singleton")
 */
class NotificationCommandController extends CommandController
{

    /**
     * @Flow\Inject
     * @var NotificationService
     */
    protected $notificationService;


    /**
     * Distribute all currently viable notifications
     *
     * @return void
     */
    public function autoDistributeNotificationsCommand()
    {
        $length = $this->notificationService->autoDistributeNotifications();
        $this->outputLine('%d notifications auto distributed.', array($length));
    }

    /**
     * Generate daily summaries
     *
     * @return void
     */
    public function generateDailySummariesCommand()
    {
        $length = $this->notificationService->generateDailySummaries();
        $this->outputLine('%d daily notification summaries generated.', array($length));
    }

    /**
     * Generate weekly summaries
     *
     * @return void
     */
    public function generateWeeklySummariesCommand()
    {
        $length = $this->notificationService->generateWeeklySummaries();
        $this->outputLine('%d weekly notification summaries generated.', array($length));
    }

}
