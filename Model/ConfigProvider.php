<?php
namespace Godogi\InternationalPhoneNumber\Model;
class ConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface
{
    const DEFAULT_COUNTRY_CONFIG = 'general/country/default';
    const ALLOWED_COUNTRIES_CONFIG = 'general/country/allow';
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    /**
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ){
        $this->_scopeConfig = $scopeConfig;
    }
    public function getConfig()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $defaultCountry = $this->_scopeConfig->getValue(self::DEFAULT_COUNTRY_CONFIG, $storeScope);
        $allowedCountries = $this->_scopeConfig->getValue(self::ALLOWED_COUNTRIES_CONFIG, $storeScope);
        $countriesPhonesList = json_encode(\Godogi\InternationalPhoneNumber\Block\Widget\Telephone::COUNTRIES);
        return [
            'default_country_config' => $defaultCountry,
            'allowed_countries_config' => $allowedCountries,
            'countries_phones_list' => $countriesPhonesList
        ];
    }
}
