<?php

namespace Orangecat\CspWhitelist\Helper;

use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\Helper\Context;
use Magento\Customer\Model\Session;

class Data extends \Orangecat\Core\Helper\Data
{
    /**
     * Check to see if module is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return (bool)$this->getYesNo('cspwhitelist/general/enabled');
    }

    /**
     * Get a list of policies
     *
     * @return array
     */
    public function getPolicies(): array
    {
        $data = [];
        $policies = (string)$this->getConfig('cspwhitelist/general/policies');
        if (!$policies) {
            return $data;
        }
        foreach ($this->serialize->unserialize($policies) as $policy) {
            $data[$policy['policy']][] = $policy['value'];
        }
        return $data;
    }

    public function isHeaderSplittingEnabled(): bool
    {
        return (bool) $this->getYesNo('cspwhitelist/header_splitting/enabled');
    }

    /**
     * Get max header size
     *
     * @return int
     */
    public function getMaxHeaderSize(): int
    {
        return (int) $this->getConfig('cspwhitelist/header_splitting/max_header_size');
    }
}
