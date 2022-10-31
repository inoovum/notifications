<?php
namespace SteinbauerIT\Notifications\Domain\Service;

/*
 * This file is part of the SteinbauerIT.Notifications package.
 */

use Doctrine\Common\Collections\ArrayCollection;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\PersistenceManagerInterface;
use Neos\Flow\Persistence\QueryResultInterface;
use Neos\Flow\ResourceManagement\PersistentResource;
use Neos\Flow\Security\Account;
use Neos\Flow\Security\Context;
use SteinbauerIT\Notifications\Domain\Model\AccountSettings;
use SteinbauerIT\Notifications\Domain\Model\Notification;
use SteinbauerIT\Notifications\Domain\Repository\AccountSettingsRepository;
use SteinbauerIT\Notifications\Domain\Repository\NotificationRepository;

class NotificationService
{

    /**
     * @Flow\InjectConfiguration
     * @var array
     */
    protected $settings;

    /**
     * @Flow\Inject
     * @var Context
     */
    protected $securityContext;

    /**
     * @Flow\Inject
     * @var NotificationRepository
     */
    protected $notificationRepository;

    /**
     * @Flow\Inject
     * @var AccountSettingsRepository
     */
    protected $accountSettingsRepository;

    /**
     * @Flow\Inject
     * @var PersistenceManagerInterface
     */
    protected $persistenceManager;


    /**
     * @param string $title
     * @param string $text
     * @param Account $account
     * @param string $category
     * @param string $intro
     * @param string $actionUri
     * @param PersistentResource $image
     * @param array $recipientInfos
     * @param \DateTime $distributeAt
     * @param array $distributionMethods
     * @param string $asSummary
     * @param string $senderName
     * @param PersistentResource $senderImage
     * @return void
     */
    public function createNotification(
        string $title,
        string $text,
        Account $account = null,
        string $category = '',
        string $intro = '',
        string $actionUri = '',
        PersistentResource $image = null,
        array $recipientInfos = array(),
        \DateTime $distributeAt = null,
        array $distributionMethods = null,
        string $asSummary = null,
        string $senderName = '',
        PersistentResource $senderImage = null
    ): void
    {
        if ($distributionMethods === null) {
            $distributionMethods = $this->getEnabledDistributionMethodNames($category);
        }
        if ($asSummary === null) {
            $asSummary = $this->getAsSummary($category);
        }
        foreach ($distributionMethods as $distributionMethod) {
            $notification = new Notification();
            $notification->setTitle($title);
            $notification->setText($text);
            $notification->setAccount($account);
            $notification->setCategory($category);
            $notification->setDistributeAt($distributeAt);
            $notification->setDistributionMethod($distributionMethod);
            $notification->setAsSummary($asSummary);
            $notification->setIntro($intro);
            $notification->setActionUri($actionUri);
            $notification->setImage($image);
            $notification->setRecipientInfos($recipientInfos);
            $notification->setSenderName($senderName);
            $notification->setSenderImage($senderImage);
            $this->persistenceManager->allowObject($notification);
            $this->notificationRepository->add($notification);
        }
    }

    /**
     * @param string $distributionMethod
     * @param string $category
     * @param int $limit
     * @param int $offset
     * @param bool $unreadOnly
     * @return QueryResultInterface
     */
    public function findDistributed(string $distributionMethod = null, string $category = null, int $limit = 0, int $offset = 0, bool $unreadOnly = false): QueryResultInterface
    {
        $account = $this->securityContext->getAccount();
        return $this->notificationRepository->findDistributed($account, $distributionMethod, $category, $limit, $offset, $unreadOnly);
    }

    /**
     * @return int
     */
    public function autoDistributeNotifications(): int
    {
        $length = 0;
        foreach ($this->settings['distributionMethods'] as $key => $distributionMethod) {
            if (isset($distributionMethod['autoDistribute']) && $distributionMethod['autoDistribute'] === true) {
                $notifications = $this->notificationRepository->findUndistributed(null, $key);
                /* @var Notification $notification */
                foreach ($notifications as $notification) {
                    $notification->setWasDistributedAt(new \DateTime());
                    $this->notificationRepository->update($notification);
                    $length++;
                }
            }
        }
        return $length;
    }

    /**
     * @return int
     */
    public function generateDailySummaries(): int
    {
        return $this->generateSummaries(Notification::SUMMARY_DAILY, $this->settings['summary']['dailySummaryTitle']);
    }

    /**
     * @return int
     */
    public function generateWeeklySummaries(): int
    {
        return $this->generateSummaries(Notification::SUMMARY_WEEKLY, $this->settings['summary']['weeklySummaryTitle']);
    }

    /**
     * @param string $summaryType
     * @param string $title
     * @return int
     */
    protected function generateSummaries(string $summaryType, string $title): int
    {
        $now = new \DateTime;
        $notifications = $this->notificationRepository->findSummaryDistributable($summaryType);
        $summaryNotifications = array();
        /* @var Notification $notification */
        foreach ($notifications as $notification) {
            // Summaries are only possible if account was defined
            if ($notification->getAccount() === null) {
                continue;
            }
            $found = false;
            /* @var Notification $summaryNotification */
            foreach ($summaryNotifications as $summaryNotification) {
                if ($summaryNotification->getAccount() === $notification->getAccount()) {
                    $summaryNotification->addNotificationOfSummary($notification);
                    $notification->setWasDistributedAt($now);
                    $this->notificationRepository->update($notification);
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $summaryNotification = new Notification();
                $summaryNotification->setTitle($title);
                $summaryNotification->setText('');
                $summaryNotification->setAccount($notification->getAccount());
                $summaryNotification->addNotificationOfSummary($notification);
                $notification->setWasDistributedAt($now);
                $this->notificationRepository->update($notification);
                $summaryNotifications[] = $summaryNotification;
            }
        }
        /* @var Notification $summaryNotification */
        foreach ($summaryNotifications as $summaryNotification) {
            $this->notificationRepository->add($summaryNotification);
        }
        return count($summaryNotifications);
    }

    /**
     * @return array
     */
    public function getCurrentAccountSettings(): array
    {
        $settings = array();
        $settings['default']['distributionMethods'] = $this->getDistributionMethods();
        $settings['default']['asSummary'] = $this->getAsSummary();
        foreach ($this->settings['categories'] as $name => $category) {
            $settings[$name]['distributionMethods'] = $this->getDistributionMethods($name);
            $settings[$name]['asSummary'] = $this->getAsSummary($name);
        }
        return $settings;
    }

    /**
     * @param array $accountSettingsArray
     * @return void
     */
    public function updateCurrentAccountSettings(array $accountSettingsArray = array()): void
    {
        $accountSettings = $this->accountSettingsRepository->findOneByAccount($this->securityContext->getAccount());
        if ($accountSettings === null) {
            $accountSettings = new AccountSettings();
            $accountSettings->setAccount($this->securityContext->getAccount());
            $accountSettings->setSettings($accountSettingsArray);
            $this->accountSettingsRepository->add($accountSettings);
        } else {
            $accountSettings->setSettings($accountSettingsArray);
            $this->accountSettingsRepository->update($accountSettings);
        }
    }

    /**
     * @param string $category
     * @return array
     */
    protected function getEnabledDistributionMethodNames(string $category = ''): array
    {
        $distributionMethods = array();
        $distributionMethodsFromSettings = $this->getValueFromSettings('distributionMethods', $category);
        foreach ($distributionMethodsFromSettings as $name => $distributionMethod) {
            if (isset($distributionMethod['enabled']) && $distributionMethod['enabled'] === true) {
                $distributionMethods[] = $name;
            }
        }
        return $distributionMethods;
    }

    /**
     * @param string $category
     * @return array
     */
    protected function getDistributionMethods(string $category = ''): array
    {
        $distributionMethods = array();
        $distributionMethodsFromSettings = $this->getValueFromSettings('distributionMethods', $category);
        foreach ($distributionMethodsFromSettings as $name => $distributionMethod) {
            $distributionMethods[$name] = $distributionMethod;
        }
        return $distributionMethods;
    }

    /**
     * @param string $category
     * @return string
     */
    protected function getAsSummary(string $category = ''): string
    {
        return $this->getValueFromSettings('asSummary', $category);
    }

    /**
     * @param string $name
     * @return mixed
     */
    protected function getValueFromSettings(string $name, string $category = ''): mixed
    {
        $distributionMethods = $this->settings['default'][$name];
        if (isset($this->getAccountSettings()['default'][$name])) {
            $distributionMethods = $this->getAccountSettings()['default'][$name];
        }
        if (strlen($category) > 0) {
            if (isset($this->settings['categories'][$category][$name])) {
                $distributionMethods = $this->settings['categories'][$category][$name];
            }
            if (isset($this->getAccountSettings()[$category][$name])) {
                $distributionMethods = $this->getAccountSettings()[$category][$name];
            }
        }
        return $distributionMethods;
    }


    /**
     * @param Notification $notification
     * @return void
     */
    public function setAsRead(Notification $notification): void
    {
        $now = new \DateTime();
        $notification->setWasReadAt($now);
        $this->notificationRepository->update($notification);
    }

    /**
     * @param Notification $notification
     * @return void
     */
    public function setAsDistributedInline(Notification $notification): void
    {
        $now = new \DateTime();
        $notification->setWasDistributedAt($now);
        $this->notificationRepository->update($notification);
    }

    /**
     * @return array
     */
    protected function getAccountSettings(): array
    {
        $account = $this->securityContext->getAccount();
        $accountSettings = null;
        if ($account !== null) {
            $accountSettings = $this->accountSettingsRepository->findOneByAccount($account);
        }
        /* @var AccountSettings $accountSettings */
        if ($accountSettings !== null) {
            return $accountSettings->getSettings();
        }
        return array();
    }

}
