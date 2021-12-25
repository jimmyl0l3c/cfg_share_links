<?php

namespace OCA\CfgShareLinks\Controller;

use OCA\CfgShareLinks\AppInfo\Application;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\IConfig;
use OCP\IRequest;
use OCP\IUserSession;

class SettingsController extends Controller
{
    /** @var IConfig */
    private $config;

    public function __construct(
        IConfig $config,
        IRequest $request
    ) {
        parent::__construct(Application::APP_ID, $request);
        $this->config = $config;
    }

    public function admin(bool $defaultLabelEnabled, string $defaultLabel): DataResponse
    {
        $this->config->setAppValue(Application::APP_ID, 'default_label_enabled', $defaultLabelEnabled);

        if ($defaultLabelEnabled) {
            $this->config->setAppValue(Application::APP_ID, 'default_label', $defaultLabel);
        }

        return new DataResponse('Saved', Http::STATUS_OK);
    }
}