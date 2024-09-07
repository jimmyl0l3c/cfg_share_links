<?php

namespace OCA\CfgShareLinks\Tests\Unit\Service;

use OCA\CfgShareLinks\Db\CfgShareMapper;
use OCA\CfgShareLinks\Service\InvalidTokenException;
use OCA\CfgShareLinks\Service\ShareService;
use OCP\AppFramework\Services\IAppConfig;
use OCP\Files\IRootFolder;
use OCP\IGroupManager;
use OCP\IL10N;
use OCP\Share\IManager;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ShareServiceTest extends TestCase {
	protected ShareService $service;
	protected string $userId = 'user';
	protected $logger;
	protected $shareManager;
	protected $groupManager;
	protected $rootFolder;
	protected $l10n;
	protected $appConfig;
	protected $mapper;

	protected function setUp(): void {
		$this->logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
		$this->shareManager = $this->getMockBuilder(IManager::class)->getMock();
		$this->groupManager = $this->getMockBuilder(IGroupManager::class)->getMock();
		$this->rootFolder = $this->getMockBuilder(IRootFolder::class)->getMock();
		$this->l10n = $this->getMockBuilder(IL10N::class)->getMock();

		$this->appConfig = $this->createStub(IAppConfig::class);
		$this->appConfig->method('getAppValue')->willReturn('3');

		$this->mapper = $this->getMockBuilder(CfgShareMapper::class)
			->disableOriginalConstructor()
			->getMock();
		$this->service = new ShareService(
			$this->logger,
			$this->shareManager,
			$this->groupManager,
			$this->rootFolder,
			$this->l10n,
			$this->appConfig,
			$this->mapper,
			$this->userId,
		);
	}

	public function testUpdate() {
		// TODO: write test
	}

	public function testCreate() {
		// TODO: write test
	}

	public function testTokenValidityCheckThrowsExceptionIfInvalid() {
		$this->expectException(InvalidTokenException::class);
		$this->service->raiseIfTokenIsInvalid('Invalid.token#!');
	}

	public function testTokenValidityCheckDoesNotThrowIfValid() {
		$this->expectNotToPerformAssertions();
		$this->service->raiseIfTokenIsInvalid('some-VALID_token1');
	}
}
