<?php

namespace PROCERGS\LoginCidadao\CoreBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use PROCERGS\LoginCidadao\NotificationBundle\NotificationEvents;
use PROCERGS\LoginCidadao\NotificationBundle\Event\NotificationEvent;
use PROCERGS\LoginCidadao\CoreBundle\Mailer\TwigSwiftMailer;
use PROCERGS\LoginCidadao\NotificationBundle\Handler\NotificationHandlerInterface;
use PROCERGS\LoginCidadao\NotificationBundle\Entity\PersonNotificationOption;
use PROCERGS\LoginCidadao\NotificationBundle\Model\NotificationInterface;

class NotificationsSubscriber implements EventSubscriberInterface
{

    /** @var NotificationHandlerInterface */
    protected $notificationHandler;

    /** @var TwigSwiftMailer */
    protected $mailer;

    public function __construct(NotificationHandlerInterface $notificationHandler,
                                TwigSwiftMailer $mailer)
    {
        $this->notificationHandler = $notificationHandler;
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents()
    {
        return array(
            NotificationEvents::NOTIFICATION_INITIALIZE => array('onNotificationInitialize', 0),
            NotificationEvents::NOTIFICATION_SUCCESS => array('onNotificationSuccess', 0),
            NotificationEvents::NOTIFICATION_COMPLETED => array('onNotificationCompleted', 0),
        );
    }

    public function onNotificationInitialize(NotificationEvent $event)
    {
        // Not implemented yet
    }

    public function onNotificationSuccess(NotificationEvent $event)
    {
        // TODO: send email
        $notification = $event->getNotification();
        if ($notification->getCategory()->isEmailable()) {
            $settings = $this->getSettings($notification);
            if ($settings->getSendEmail()) {
                $this->sendNotificationEmail($notification);
            }
        }
    }

    public function onNotificationCompleted(NotificationEvent $event)
    {
        // Not implemented yet
    }

    /**
     * @return PersonNotificationOption
     */
    private function getSettings($notification)
    {
        $person = $notification->getPerson();
        $category = $notification->getCategory();
        $client = $category->getClient();
        ;
        $settings = $this->notificationHandler->getSettings($person, $category,
                                                            $client);

        return reset($settings);
    }

    private function sendNotificationEmail(NotificationInterface $notification)
    {
        $html = $this->notificationHandler->getEmailHtml($notification);
        $this->mailer->sendEmailBasedOnNotification($notification->getId(),
                                                    $notification->getCategory()->getMailSenderAddress(),
                                                    $notification->getPerson()->getEmail(),
                                                    $notification->getTitle(),
                                                    $html);
    }

}
