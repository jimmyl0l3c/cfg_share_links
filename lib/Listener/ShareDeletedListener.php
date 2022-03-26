<?php

namespace OCA\CfgShareLinks\Listener;

use OCA\CfgShareLinks\Db\CfgShareMapper;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\DB\Exception;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\Share\Events\ShareDeletedEvent;
use Psr\Log\LoggerInterface;

class ShareDeletedListener implements IEventListener {
	/** @var LoggerInterface */
	private $logger;
	/** @var CfgShareMapper */
	private $mapper;

	public function __construct(
		LoggerInterface $logger,
		CfgShareMapper $mapper
	) {
		$this->logger = $logger;
		$this->mapper = $mapper;
	}

	public function handle(Event $event): void {
		if (!($event instanceof ShareDeletedEvent)) {
			return;
		}

		try {
			$c_share = $this->mapper->findByFullId($event->getShare()->getFullId());
			$this->mapper->delete($c_share);
			$this->logger->debug('ShareDeletedListener: Successfully deleted custom share from cfg_shares');
		} catch (DoesNotExistException $e) {
		} catch (MultipleObjectsReturnedException|Exception $e) {
			$this->logger->debug('ShareDeletedListener: Exception: ' . $e->getMessage() . " - " . $e->getTraceAsString());
		}
	}
}
