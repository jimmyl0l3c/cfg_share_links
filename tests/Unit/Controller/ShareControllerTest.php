<?php

namespace OCA\CfgShareLinks\Tests\Unit\Controller;

use OCA\CfgShareLinks\Controller\ShareController;
use OCA\CfgShareLinks\Service\ShareService;
use OCP\IRequest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ShareControllerTest extends TestCase {
	protected ShareController $controller;
	protected MockObject|ShareService $service;
	protected string $userId = 'user';
	protected IRequest|MockObject $request;

	protected function setUp(): void {
		$this->request = $this->getMockBuilder(IRequest::class)->getMock();
		$this->service = $this->getMockBuilder(ShareService::class)
			->disableOriginalConstructor()
			->getMock();
		$this->controller = new ShareController('cfg_share_links', $this->request, $this->service, $this->userId);
	}

	public function testCreate() {
		// TODO: write test
	}

	public function testUpdate() {
		// TODO: write test
	}
}
