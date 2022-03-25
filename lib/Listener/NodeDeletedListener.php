<?php

namespace OCA\CfgShareLinks\Listener;

use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\Files\Events\Node\NodeDeletedEvent;
use Psr\Log\LoggerInterface;

class NodeDeletedListener implements IEventListener {
	/** @var LoggerInterface */
	private $logger;

	public function __construct(
		LoggerInterface $logger
	) {
		$this->logger = $logger;
	}

	public function handle(Event $event): void {
		if (!($event instanceof NodeDeletedEvent)) {
			$this->logger->debug('NodeDeletedListener called');
			return;
		}
		$this->logger->debug('NodeDeletedEvent');

		$node = $event->getNode();
		$this->logger->debug($node->getPath());
	}
}
