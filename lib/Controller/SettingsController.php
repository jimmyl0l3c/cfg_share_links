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

    public function save(string $key, string $value): DataResponse
    {
        switch ($key) {
            case 'default_label_mode':
                if (is_numeric($value) && (int)$value >= 0 && (int)$value <= 2) {
                    $this->config->setAppValue(Application::APP_ID, 'default_label_mode', (int)$value);
                    return new DataResponse(['message' => 'Saved'], Http::STATUS_OK);
                }
                break;
            case 'default_label':
                if (strlen($value) >= 1) {
                    $this->config->setAppValue(Application::APP_ID, 'default_label', $value);
                    return new DataResponse(['message' => 'Saved'], Http::STATUS_OK);
                }
                break;
            case 'min_token_length':
                if (is_numeric($value) && (int)$value >= 1) {
                    $this->config->setAppValue(Application::APP_ID, 'min_token_length', (int)$value);
                    return new DataResponse(['message' => 'Saved'], Http::STATUS_OK);
                }
                break;
        }

        return new DataResponse(['message' => 'Invalid key or value'], Http::STATUS_BAD_REQUEST);
    }

    public function fetch(): DataResponse
    {
        $settings = [
            'defaultLabelMode' => $this->config->getAppValue(Application::APP_ID, 'default_label_mode', 0),
            'defaultLabel' => $this->config->getAppValue(Application::APP_ID, 'default_label', 'Custom link'),
            'minTokenLength' => $this->config->getAppValue(Application::APP_ID, 'min_token_length', 3)
        ];

        return new DataResponse($settings, Http::STATUS_OK);
    }
}