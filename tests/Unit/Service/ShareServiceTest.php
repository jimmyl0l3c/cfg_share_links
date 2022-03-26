<?php

namespace OCA\CfgShareLinks\Tests\Unit\Service;

use OCA\CfgShareLinks\Db\CfgShareMapper;
use OCA\CfgShareLinks\Service\ShareService;
use OCP\Files\IRootFolder;
use OCP\IConfig;
use OCP\IGroupManager;
use OCP\IL10N;
use OCP\Share\IManager;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ShareServiceTest extends TestCase {
	protected $service;
	protected $userId = 'user';
	protected $logger;
	protected $shareManager;
	protected $groupManager;
	protected $rootFolder;
	protected $l10n;
	protected $config;
	protected $mapper;

	protected function setUp(): void {
		$this->logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
		$this->shareManager = $this->getMockBuilder(IManager::class)->getMock();
		$this->groupManager = $this->getMockBuilder(IGroupManager::class)->getMock();
		$this->rootFolder = $this->getMockBuilder(IRootFolder::class)->getMock();
		$this->l10n = $this->getMockBuilder(IL10N::class)->getMock();
		$this->config = $this->getMockBuilder(IConfig::class)->getMock();
		$this->mapper = $this->getMockBuilder(CfgShareMapper::class)
			->disableOriginalConstructor()
			->getMock();
		$this->service = new ShareService(
			$this->logger,
			$this->shareManager,
			$this->groupManager,
			$this->rootFolder,
			$this->userId,
			$this->l10n,
			$this->config,
			$this->mapper
		);
	}

	public function testUpdate() {
		// TODO: write test
	}

	public function testCreate() {
		// TODO: write test
	}
}
