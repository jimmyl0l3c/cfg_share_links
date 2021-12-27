<?php

namespace OCA\CfgShareLinks\Sections;

use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\Settings\IIconSection;

class LinksAdmin implements IIconSection {
	/** @var IL10N */
	private $l;

	/** @var IURLGenerator */
	private $urlGenerator;

	public function __construct(
		IL10N $l,
		IURLGenerator $urlGenerator
	) {
		$this->l = $l;
		$this->urlGenerator = $urlGenerator;
	}

	public function getID(): string {
		return 'cfgsharelinks';
	}

	public function getName(): string {
		return $this->l->t('Configurable Share Links');
	}

	public function getPriority(): int {
		return 98;
	}

	public function getIcon(): string {
		return $this->urlGenerator->imagePath('core', 'actions/settings-dark.svg');
	}
}
