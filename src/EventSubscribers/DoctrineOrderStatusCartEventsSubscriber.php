<?php


namespace App\EventSubscribers;


use App\Entity\Cart;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;

class DoctrineOrderStatusCartEventsSubscriber implements EventSubscriber
{

	private $logger;

	public function __construct(LoggerInterface $logger)
	{
		$this->logger = $logger;
	}

	/**
	 * @inheritDoc
	 */
	public function getSubscribedEvents(): array
	{
		return [
			Events::preUpdate,
			Events::postUpdate
		];
	}

	public function preUpdate(LifecycleEventArgs $args)
	{
		$test = $args->getObjectManager();
		$object = $args->getObject();

		if (!$object instanceof Cart) {
			return;
		}

		$idOrdreStatus = $object->getOrderStatus()->getId()+1;

		$this->logger->info('OrderStatus cart modif', [
			'cartOrderStatus' => $object,
			'test' => $test,
			'id' => $idOrdreStatus
		]);
	}

	public function postUpdate(LifecycleEventArgs $args)
	{
		$object = $args->getObject();

		if(!$object instanceof Cart){
			return;
		}

		$this->logger->info('OrderStatus cart modif', [
			'cartOrderStatus' => $object,
		]);
	}
}