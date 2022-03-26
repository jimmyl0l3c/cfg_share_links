<?php

namespace OCA\CfgShareLinks\Listener;

use OCA\CfgShareLinks\Db\CfgShareMapper;
use OCP\DB\Exception;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\Files\Events\Node\NodeDeletedEvent;
use OCP\Files\InvalidPathException;
use OCP\Files\NotFoundException;
use OCP\Share\Exceptions\ShareNotFound;
use OCP\Share\IManager;
use Psr\Log\LoggerInterface;

class NodeDeletedListener implements IEventListener {
	/** @var LoggerInterface */
	private $logger;
	/** @var CfgShareMapper */
	private $mapper;
	/** @var IManager */
	private $shareManager;

	public function __construct(
		LoggerInterface $logger,
		CfgShareMapper $mapper,
		IManager $shareManager
	) {
		$this->logger = $logger;
		$this->mapper = $mapper;
		$this->shareManager = $shareManager;
	}

	public function handle(Event $event): void {
		if (!($event instanceof NodeDeletedEvent)) {
			return;
		}

		$node = $event->getNode();
		$this->logger->debug($node->getPath());

		try {
			// Get all custom shares by shared node
			$c_shares = $this->mapper->findByNode($node->getId());

			// Delete found custom shares
			foreach ($c_shares as $c_share) {
				try {
					$share = $this->shareManager->getShareById($c_share->getFullId());
					// Delete Share and CfgShare
					$this->shareManager->deleteShare($share);
					$this->mapper->delete($c_share);
					$this->logger->debug('NodeDeletedListener: Successfully deleted custom share');
				} catch (Exception|InvalidPathException|NotFoundException $e) {
					$this->logger->debug('NodeDeletedListener: getShareById Exception: ' . $e->getMessage() . " - " . $e->getTraceAsString());
				} catch (ShareNotFound $e) {
				}
			}
		} catch (Exception|InvalidPathException|NotFoundException $e) {
			$this->logger->debug('NodeDeletedListener: findByNode Exception: ' . $e->getMessage() . " - " . $e->getTraceAsString());
		}
	}
}
