<?php

namespace OCA\CfgShareLinks\Settings;

use OCA\CfgShareLinks\AppInfo\Application;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\IL10N;
use OCP\Settings\ISettings;
use OCP\Util;

class LinksAdmin implements ISettings
{
    /** @var IL10N */
    private $l;

    /** @var IConfig */
    private $config;

    public function __construct(
        IConfig $config,
        IL10N $l
    ) {
        $this->config = $config;
        $this->l = $l;
    }

    public function getForm(): TemplateResponse
    { // TODO: add regular expression settings (validity check)
        $parameters = [
            'defaultLabelMode' => $this->config->getAppValue(Application::APP_ID, 'default_label_mode', 0),
            'defaultLabel' => $this->config->getAppValue(Application::APP_ID, 'default_label', 'Custom link'),
            'minTokenLength' => $this->config->getAppValue(Application::APP_ID, 'min_token_length', 3)
        ];

        Util::addScript(Application::APP_ID, 'cfgsharelinks-settings-admin');
        return new TemplateResponse(Application::APP_ID, 'admin', $parameters, '');
    }

    public function getSection(): string
    {
        return 'cfgsharelinks';
    }

    public function getPriority(): int
    {
        return 10;
    }
}
