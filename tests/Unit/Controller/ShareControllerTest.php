<?php

namespace OCA\CfgShareLinks\Tests\Unit\Controller;

use OCA\CfgShareLinks\Controller\ShareController;
use OCA\CfgShareLinks\Service\ShareService;
use OCP\IRequest;
use PHPUnit\Framework\TestCase;

class ShareControllerTest extends TestCase {
	protected $controller;
	protected $service;
	protected $userId = 'user';
	protected $request;

	protected function setUp(): void {
		$this->request = $this->getMockBuilder(IRequest::class)->getMock();
		$this->service = $this->getMockBuilder(ShareService::class)
			->disableOriginalConstructor()
			->getMock();
		$this->controller = new ShareController($this->request, $this->service, $this->userId);
	}

	public function testCreate() {
		// TODO: write test
	}

	public function testUpdate() {
		// TODO: write test
	}
}
