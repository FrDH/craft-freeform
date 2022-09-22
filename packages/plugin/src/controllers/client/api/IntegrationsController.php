<?php

namespace Solspace\Freeform\controllers\client\api;

use Solspace\Freeform\Bundles\Integrations\Providers\IntegrationDTOProvider;
use Solspace\Freeform\controllers\BaseApiController;

class IntegrationsController extends BaseApiController
{
    public function __construct($id, $module, $config = [], private IntegrationDTOProvider $integrationDTOProvider)
    {
        parent::__construct($id, $module, $config);
    }

    protected function get(): array
    {
        return $this->integrationDTOProvider->getByCategory();
    }

    protected function getOne(int|string $id): array|object|null
    {
        return $this->integrationDTOProvider->getById($id);
    }
}
