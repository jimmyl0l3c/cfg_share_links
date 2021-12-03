<?php

namespace OCA\CfgShareLinks\Service;

use OC\Share20\Manager;
use OCP\EventDispatcher\IEventDispatcher;
use OCP\Files\IRootFolder;
use OCP\Files\Mount\IMountManager;
use OCP\IConfig;
use OCP\IGroupManager;
use OCP\IL10N;
use OCP\ILogger;
use OCP\IURLGenerator;
use OCP\IUserManager;
use OCP\IUserSession;
use OCP\L10N\IFactory;
use OCP\Mail\IMailer;
use OCP\Security\IHasher;
use OCP\Security\ISecureRandom;
use OCP\Share\IProviderFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ShareManager extends Manager
{
    public function __construct(
        ILogger $logger,
        IConfig $config,
        ISecureRandom $secureRandom,
        IHasher $hasher,
        IMountManager $mountManager,
        IGroupManager $groupManager,
        IL10N $l,
        IFactory $l10nFactory,
        IProviderFactory $factory,
        IUserManager $userManager,
        IRootFolder $rootFolder,
        EventDispatcherInterface $legacyDispatcher,
        IMailer $mailer,
        IURLGenerator $urlGenerator,
        \OC_Defaults $defaults,
        IEventDispatcher $dispatcher,
        IUserSession $userSession)
    {
        parent::__construct($logger, $config, $secureRandom, $hasher, $mountManager, $groupManager, $l, $l10nFactory, $factory, $userManager, $rootFolder, $legacyDispatcher, $mailer, $urlGenerator, $defaults, $dispatcher, $userSession);
    }

    public function test(): string
    {
        return 'This is test';
    }
}