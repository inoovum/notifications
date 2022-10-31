<?php
namespace SteinbauerIT\Notifications\Domain\Model;

/*
 * This file is part of the SteinbauerIT.Notifications package.
 */

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\ResourceManagement\PersistentResource;
use Neos\Flow\Security\Account;

/**
 * @Flow\Entity
 */
class Notification
{

    const SUMMARY_NONE = 'none';
    const SUMMARY_DAILY = 'daily';
    const SUMMARY_WEEKLY = 'weekly';

    /**
     * @ORM\ManyToOne
     * @var Account
     */
    protected $account = null;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var string
     */
    protected $title;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    protected $intro = '';

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    protected $text;

    /**
     * @var string
     */
    protected $actionUri = '';

    /**
     * @ORM\ManyToOne
     * @var PersistentResource
     */
    protected $image = null;

    /**
     * @var array
     */
    protected $recipientInfos = array();

    /**
     * @var string
     */
    protected $senderName = '';

    /**
     * @ORM\ManyToOne
     * @var PersistentResource
     */
    protected $senderImage = null;

    /**
     * @var string
     */
    protected $distributionMethod;

    /**
     * @var string
     */
    protected $category = '';

    /**
     * @var string
     */
    protected $asSummary = self::SUMMARY_NONE;

    /**
     * @ORM\Column(nullable=true)
     * @var \DateTime
     */
    protected $distributeAt = null;

    /**
     * @ORM\Column(nullable=true)
     * @var \DateTime
     */
    protected $wasDistributedAt = null;

    /**
     * @ORM\Column(nullable=true)
     * @var \DateTime
     */
    protected $wasReadAt = null;

    /**
     * @var string
     */
    protected $distributionLog = '';

    /**
     * @ORM\ManyToOne(inversedBy="notificationsOfSummary")
     * @var Notification
     */
    protected $summaryNotification = null;

    /**
     * @ORM\OneToMany(mappedBy="summaryNotification")
     * @ORM\OrderBy({"distributeAt" = "DESC", "createdAt" = "DESC"})
     * @var Collection<Notification>
     */
    protected $notificationsOfSummary;


    /**
     * Construct
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->notificationsOfSummary = new ArrayCollection();
    }

    /**
     * @return Account
     */
    public function getAccount(): ?Account
    {
        return $this->account;
    }

    /**
     * @param Account $account
     */
    public function setAccount(?Account $account): void
    {
        $this->account = $account;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getIntro(): string
    {
        return $this->intro;
    }

    /**
     * @param string $intro
     */
    public function setIntro(string $intro): void
    {
        $this->intro = $intro;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getActionUri(): string
    {
        return $this->actionUri;
    }

    /**
     * @param string $actionUri
     */
    public function setActionUri(string $actionUri): void
    {
        $this->actionUri = $actionUri;
    }

    /**
     * @return PersistentResource
     */
    public function getImage(): ?PersistentResource
    {
        return $this->image;
    }

    /**
     * @param PersistentResource $image
     */
    public function setImage(?PersistentResource $image): void
    {
        $this->image = $image;
    }

    /**
     * @return array
     */
    public function getRecipientInfos(): array
    {
        return $this->recipientInfos;
    }

    /**
     * @param array $recipientInfos
     */
    public function setRecipientInfos(array $recipientInfos): void
    {
        $this->recipientInfos = $recipientInfos;
    }

    /**
     * @return string
     */
    public function getSenderName(): string
    {
        return $this->senderName;
    }

    /**
     * @param string $senderName
     */
    public function setSenderName(string $senderName): void
    {
        $this->senderName = $senderName;
    }

    /**
     * @return PersistentResource
     */
    public function getSenderImage(): ?PersistentResource
    {
        return $this->senderImage;
    }

    /**
     * @param PersistentResource $senderImage
     */
    public function setSenderImage(?PersistentResource $senderImage): void
    {
        $this->senderImage = $senderImage;
    }

    /**
     * @return string
     */
    public function getDistributionMethod(): string
    {
        return $this->distributionMethod;
    }

    /**
     * @param string $distributionMethod
     */
    public function setDistributionMethod(string $distributionMethod): void
    {
        $this->distributionMethod = $distributionMethod;
    }

    /**
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * @param string $category
     */
    public function setCategory(string $category): void
    {
        $this->category = $category;
    }

    /**
     * @return string
     */
    public function getAsSummary(): string
    {
        return $this->asSummary;
    }

    /**
     * @param string $asSummary
     */
    public function setAsSummary(string $asSummary): void
    {
        $this->asSummary = $asSummary;
    }

    /**
     * @return \DateTime
     */
    public function getDistributeAt(): ?\DateTime
    {
        return $this->distributeAt;
    }

    /**
     * @param \DateTime $distributeAt
     */
    public function setDistributeAt(?\DateTime $distributeAt): void
    {
        $this->distributeAt = $distributeAt;
    }

    /**
     * @return \DateTime
     */
    public function getWasDistributedAt(): ?\DateTime
    {
        return $this->wasDistributedAt;
    }

    /**
     * @param \DateTime $wasDistributedAt
     */
    public function setWasDistributedAt(?\DateTime $wasDistributedAt): void
    {
        $this->wasDistributedAt = $wasDistributedAt;
    }

    /**
     * @return \DateTime
     */
    public function getWasReadAt(): ?\DateTime
    {
        return $this->wasReadAt;
    }

    /**
     * @param \DateTime $wasReadAt
     */
    public function setWasReadAt(?\DateTime $wasReadAt): void
    {
        $this->wasReadAt = $wasReadAt;
    }

    /**
     * @return string
     */
    public function getDistributionLog(): string
    {
        return $this->distributionLog;
    }

    /**
     * @param string $distributionLog
     */
    public function setDistributionLog(string $distributionLog): void
    {
        $this->distributionLog = $distributionLog;
    }

    /**
     * @return Notification
     */
    public function getSummaryNotification(): ?Notification
    {
        return $this->summaryNotification;
    }

    /**
     * @param Notification $summaryNotification
     */
    public function setSummaryNotification(?Notification $summaryNotification): void
    {
        $this->summaryNotification = $summaryNotification;
    }

    /**
     * @return Collection
     */
    public function getNotificationsOfSummary(): Collection
    {
        return $this->notificationsOfSummary;
    }

    /**
     * @param Collection $notificationsOfSummary
     */
    public function setNotificationsOfSummary(Collection $notificationsOfSummary): void
    {
        $this->notificationsOfSummary = $notificationsOfSummary;
    }

    /**
     * @param Notification $notificationOfSummary
     */
    public function addNotificationOfSummary(Notification $notificationOfSummary): void
    {
        $notificationOfSummary->setSummaryNotification($this);
        $this->notificationsOfSummary->add($notificationOfSummary);
    }

}
