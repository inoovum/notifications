<?php
namespace SteinbauerIT\Notifications\Controller;

/*
 * This file is part of the SteinbauerIT.Notifications package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;
use SteinbauerIT\Notifications\Domain\Model\Notification;
use SteinbauerIT\Notifications\Domain\Repository\NotificationRepository;
use SteinbauerIT\Notifications\Domain\Service\NotificationService;

class NotificationController extends ActionController
{

    /**
     * @Flow\Inject
     * @var NotificationService
     */
    protected $notificationService;


    /**
     * @param Notification $notification
     * @return string
     */
    public function setAsReadAction(Notification $notification): string
    {
        $this->notificationService->setAsRead($notification);
        return json_encode(array('status' => 'done'));
    }

}
