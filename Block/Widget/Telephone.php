<?php
namespace Godogi\InternationalPhoneNumber\Block\Widget;


use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Helper\Address as AddressHelper;
use Magento\Customer\Model\Options;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;


class Telephone extends \Magento\Customer\Block\Widget\Telephone {
    const COUNTRIES = [
        "AF" => [ "name" => "Afghanistan", "phone" => ["93"]],
        "AL" => [ "name" => "Albania", "phone" => ["355"]],
        "DZ" => [ "name" => "Algeria", "phone" => ["213"]],
        "AS" => [ "name" => "American Samoa", "phone" => ["1-684"]],
        "AD" => [ "name" => "Andorra", "phone" => ["376"]],
        "AO" => [ "name" => "Angola", "phone" => ["244"]],
        "AI" => [ "name" => "Anguilla", "phone" => ["1-264"]],
        "AQ" => [ "name" => "Antarctica", "phone" => ["672"]],
        "AG" => [ "name" => "Antigua and Barbuda", "phone" => ["1-268"]],
        "AR" => [ "name" => "Argentina", "phone" => ["54"]],
        "AM" => [ "name" => "Armenia", "phone" => ["374"]],
        "AW" => [ "name" => "Aruba", "phone" => ["297"]],
        "AU" => [ "name" => "Australia", "phone" => ["61"]],
        "AT" => [ "name" => "Austria", "phone" => ["43"]],
        "AZ" => [ "name" => "Azerbaijan", "phone" => ["994"]],
        "BS" => [ "name" => "Bahamas", "phone" => ["1-242"]],
        "BH" => [ "name" => "Bahrain", "phone" => ["973"]],
        "BD" => [ "name" => "Bangladesh", "phone" => ["880"]],
        "BB" => [ "name" => "Barbados", "phone" => ["1-246"]],
        "BY" => [ "name" => "Belarus", "phone" => ["375"]],
        "BE" => [ "name" => "Belgium", "phone" => ["32"]],
        "BZ" => [ "name" => "Belize", "phone" => ["501"]],
        "BJ" => [ "name" => "Benin", "phone" => ["229"]],
        "BM" => [ "name" => "Bermuda", "phone" => ["1-441"]],
        "BT" => [ "name" => "Bhutan", "phone" => ["975"]],
        "BO" => [ "name" => "Bolivia", "phone" => ["591"]],
        "BA" => [ "name" => "Bosnia and Herzegovina", "phone" => ["387"]],
        "BW" => [ "name" => "Botswana", "phone" => ["267"]],
        "BR" => [ "name" => "Brazil", "phone" => ["55"]],
        "IO" => [ "name" => "British Indian Ocean Territory", "phone" => ["246"]],
        "VG" => [ "name" => "British Virgin Islands", "phone" => ["1-284"]],
        "BN" => [ "name" => "Brunei", "phone" => ["673"]],
        "BG" => [ "name" => "Bulgaria", "phone" => ["359"]],
        "BF" => [ "name" => "Burkina Faso", "phone" => ["226"]],
        "BI" => [ "name" => "Burundi", "phone" => ["257"]],
        "KH" => [ "name" => "Cambodia", "phone" => ["855"]],
        "CM" => [ "name" => "Cameroon", "phone" => ["237"]],
        "CA" => [ "name" => "Canada", "phone" => ["1"]],
        "CV" => [ "name" => "Cape Verde", "phone" => ["238"]],
        "KY" => [ "name" => "Cayman Islands", "phone" => ["1-345"]],
        "CF" => [ "name" => "Central African Republic", "phone" => ["236"]],
        "TD" => [ "name" => "Chad", "phone" => ["235"]],
        "CL" => [ "name" => "Chile", "phone" => ["56"]],
        "CN" => [ "name" => "China", "phone" => ["86"]],
        "CX" => [ "name" => "Christmas Island", "phone" => ["61"]],
        "CC" => [ "name" => "Cocos Islands", "phone" => ["61"]],
        "CO" => [ "name" => "Colombia", "phone" => ["57"]],
        "KM" => [ "name" => "Comoros", "phone" => ["269"]],
        "CK" => [ "name" => "Cook Islands", "phone" => ["682"]],
        "CR" => [ "name" => "Costa Rica", "phone" => ["506"]],
        "HR" => [ "name" => "Croatia", "phone" => ["385"]],
        "CU" => [ "name" => "Cuba", "phone" => ["53"]],
        "CW" => [ "name" => "Curacao", "phone" => ["599"]],
        "CY" => [ "name" => "Cyprus", "phone" => ["357"]],
        "CZ" => [ "name" => "Czech Republic", "phone" => ["420"]],
        "CD" => [ "name" => "Democratic Republic of the Congo", "phone" => ["243"]],
        "DK" => [ "name" => "Denmark", "phone" => ["45"]],
        "DJ" => [ "name" => "Djibouti", "phone" => ["253"]],
        "DM" => [ "name" => "Dominica", "phone" => ["1-767"]],
        "DO" => [ "name" => "Dominican Republic", "phone" => ["1-809","1-829", "1-849"]],
        "TL" => [ "name" => "East Timor", "phone" => ["670"]],
        "EC" => [ "name" => "Ecuador", "phone" => ["593"]],
        "EG" => [ "name" => "Egypt", "phone" => ["20"]],
        "SV" => [ "name" => "El Salvador", "phone" => ["503"]],
        "GQ" => [ "name" => "Equatorial Guinea", "phone" => ["240"]],
        "ER" => [ "name" => "Eritrea", "phone" => ["291"]],
        "EE" => [ "name" => "Estonia", "phone" => ["372"]],
        "ET" => [ "name" => "Ethiopia", "phone" => ["251"]],
        "FK" => [ "name" => "Falkland Islands", "phone" => ["500"]],
        "FO" => [ "name" => "Faroe Islands", "phone" => ["298"]],
        "FJ" => [ "name" => "Fiji", "phone" => ["679"]],
        "FI" => [ "name" => "Finland", "phone" => ["358"]],
        "FR" => [ "name" => "France", "phone" => ["33"]],
        "PF" => [ "name" => "French Polynesia", "phone" => ["689"]],
        "GA" => [ "name" => "Gabon", "phone" => ["241"]],
        "GM" => [ "name" => "Gambia", "phone" => ["220"]],
        "GE" => [ "name" => "Georgia", "phone" => ["995"]],
        "DE" => [ "name" => "Germany", "phone" => ["49"]],
        "GH" => [ "name" => "Ghana", "phone" => ["233"]],
        "GI" => [ "name" => "Gibraltar", "phone" => ["350"]],
        "GR" => [ "name" => "Greece", "phone" => ["30"]],
        "GL" => [ "name" => "Greenland", "phone" => ["299"]],
        "GD" => [ "name" => "Grenada", "phone" => ["1-473"]],
        "GU" => [ "name" => "Guam", "phone" => ["1-671"]],
        "GT" => [ "name" => "Guatemala", "phone" => ["502"]],
        "GG" => [ "name" => "Guernsey", "phone" => ["44-1481"]],
        "GN" => [ "name" => "Guinea", "phone" => ["224"]],
        "GW" => [ "name" => "Guinea-Bissau", "phone" => ["245"]],
        "GY" => [ "name" => "Guyana", "phone" => ["592"]],
        "HT" => [ "name" => "Haiti", "phone" => ["509"]],
        "HN" => [ "name" => "Honduras", "phone" => ["504"]],
        "HK" => [ "name" => "Hong Kong", "phone" => ["852"]],
        "HU" => [ "name" => "Hungary", "phone" => ["36"]],
        "IS" => [ "name" => "Iceland", "phone" => ["36"]],
        "IN" => [ "name" => "India", "phone" => ["91"]],
        "ID" => [ "name" => "Indonesia", "phone" => ["62"]],
        "IR" => [ "name" => "Iran", "phone" => ["98"]],
        "IQ" => [ "name" => "Iraq", "phone" => ["964"]],
        "IE" => [ "name" => "Ireland", "phone" => ["353"]],
        "IM" => [ "name" => "Isle of Man", "phone" => ["44-1624"]],
        "IL" => [ "name" => "Israel", "phone" => ["972"]],
        "IT" => [ "name" => "Italy", "phone" => ["39"]],
        "CI" => [ "name" => "Ivory Coast", "phone" => ["225"]],
        "JM" => [ "name" => "Jamaica", "phone" => ["1-876"]],
        "JP" => [ "name" => "Japan", "phone" => ["81"]],
        "JE" => [ "name" => "Jersey", "phone" => ["44-1534"]],
        "JO" => [ "name" => "Jordan", "phone" => ["962"]],
        "KZ" => [ "name" => "Kazakhstan", "phone" => ["7"]],
        "KE" => [ "name" => "Kenya", "phone" => ["254"]],
        "KI" => [ "name" => "Kiribati", "phone" => ["686"]],
        "XK" => [ "name" => "Kosovo", "phone" => ["383"]],
        "KW" => [ "name" => "Kuwait", "phone" => ["965"]],
        "KG" => [ "name" => "Kyrgyzstan", "phone" => ["996"]],
        "LA" => [ "name" => "Laos", "phone" => ["856"]],
        "LV" => [ "name" => "Latvia", "phone" => ["371"]],
        "LB" => [ "name" => "Lebanon", "phone" => ["961"]],
        "LS" => [ "name" => "Lesotho", "phone" => ["266"]],
        "LR" => [ "name" => "Liberia", "phone" => ["231"]],
        "LY" => [ "name" => "Libya", "phone" => ["218"]],
        "LI" => [ "name" => "Liechtenstein", "phone" => ["423"]],
        "LT" => [ "name" => "Lithuania", "phone" => ["370"]],
        "LU" => [ "name" => "Luxembourg", "phone" => ["352"]],
        "MO" => [ "name" => "Macau", "phone" => ["853"]],
        "MK" => [ "name" => "Macedonia", "phone" => ["389"]],
        "MG" => [ "name" => "Madagascar", "phone" => ["261"]],
        "MW" => [ "name" => "Malawi", "phone" => ["265"]],
        "MY" => [ "name" => "Malaysia", "phone" => ["60"]],
        "MV" => [ "name" => "Maldives", "phone" => ["960"]],
        "ML" => [ "name" => "Maldives", "phone" => ["223"]],
        "MT" => [ "name" => "Malta", "phone" => ["356"]],
        "MH" => [ "name" => "Marshall Islands", "phone" => ["692"]],
        "MR" => [ "name" => "Mauritania", "phone" => ["222"]],
        "MU" => [ "name" => "Mauritius", "phone" => ["230"]],
        "YT" => [ "name" => "Mayotte", "phone" => ["262"]],
        "MX" => [ "name" => "Mexico", "phone" => ["52"]],
        "FM" => [ "name" => "Micronesia", "phone" => ["691"]],
        "MD" => [ "name" => "Moldova", "phone" => ["373"]],
        "MC" => [ "name" => "Monaco", "phone" => ["377"]],
        "MN" => [ "name" => "Mongolia", "phone" => ["976"]],
        "ME" => [ "name" => "Montenegro", "phone" => ["382"]],
        "MS" => [ "name" => "Montserrat", "phone" => ["1-664"]],
        "MA" => [ "name" => "Morocco", "phone" => ["212"]],
        "MZ" => [ "name" => "Mozambique", "phone" => ["258"]],
        "MM" => [ "name" => "Myanmar", "phone" => ["95"]],
        "NA" => [ "name" => "Namibia", "phone" => ["264"]],
        "NR" => [ "name" => "Nauru", "phone" => ["674"]],
        "NP" => [ "name" => "Nepal", "phone" => ["977"]],
        "NL" => [ "name" => "Netherlands", "phone" => ["31"]],
        "AN" => [ "name" => "Netherlands Antilles", "phone" => ["599"]],
        "NC" => [ "name" => "New Caledonia", "phone" => ["687"]],
        "NZ" => [ "name" => "New Zealand", "phone" => ["64"]],
        "NI" => [ "name" => "Nicaragua", "phone" => ["505"]],
        "NE" => [ "name" => "Niger", "phone" => ["227"]],
        "NG" => [ "name" => "Nigeria", "phone" => ["234"]],
        "NU" => [ "name" => "Niue", "phone" => ["683"]],
        "KP" => [ "name" => "North Korea", "phone" => ["850"]],
        "MP" => [ "name" => "Northern Mariana Islands", "phone" => ["1-670"]],
        "NO" => [ "name" => "Norway", "phone" => ["47"]],
        "OM" => [ "name" => "Oman", "phone" => ["968"]],
        "PK" => [ "name" => "Pakistan", "phone" => ["92"]],
        "PW" => [ "name" => "Palau", "phone" => ["680"]],
        "PS" => [ "name" => "Palestine", "phone" => ["970"]],
        "PA" => [ "name" => "Panama", "phone" => ["507"]],
        "PG" => [ "name" => "Papua New Guinea", "phone" => ["675"]],
        "PY" => [ "name" => "Paraguay", "phone" => ["595"]],
        "PE" => [ "name" => "Peru", "phone" => ["51"]],
        "PH" => [ "name" => "Philippines", "phone" => ["63"]],
        "PN" => [ "name" => "Pitcairn", "phone" => ["64"]],
        "PL" => [ "name" => "Poland", "phone" => ["48"]],
        "PT" => [ "name" => "Portugal", "phone" => ["351"]],
        "PR" => [ "name" => "Puerto Rico", "phone" => ["1-787", "1-939"]],
        "QA" => [ "name" => "Qatar", "phone" => ["974"]],
        "CG" => [ "name" => "Republic of the Congo", "phone" => ["242"]],
        "RE" => [ "name" => "Reunion", "phone" => ["262"]],
        "RO" => [ "name" => "Romania", "phone" => ["40"]],
        "RU" => [ "name" => "Russia", "phone" => ["7"]],
        "RW" => [ "name" => "Rwanda", "phone" => ["250"]],
        "BL" => [ "name" => "Saint Barthelemy", "phone" => ["590"]],
        "SH" => [ "name" => "Saint Helena", "phone" => ["290"]],
        "KN" => [ "name" => "Saint Kitts and Nevis", "phone" => ["1-869"]],
        "LC" => [ "name" => "Saint Lucia", "phone" => ["1-758"]],
        "MF" => [ "name" => "Saint Martin", "phone" => ["590"]],
        "PM" => [ "name" => "Saint Pierre and Miquelon", "phone" => ["508"]],
        "VC" => [ "name" => "Saint Vincent and the Grenadines", "phone" => ["1-784"]],
        "WS" => [ "name" => "Samoa", "phone" => ["685"]],
        "SM" => [ "name" => "San Marino", "phone" => ["378"]],
        "ST" => [ "name" => "Sao Tome and Principe", "phone" => ["239"]],
        "SA" => [ "name" => "Saudi Arabia", "phone" => ["966"]],
        "SN" => [ "name" => "Senegal", "phone" => ["221"]],
        "RS" => [ "name" => "Serbia", "phone" => ["381"]],
        "SC" => [ "name" => "Seychelles", "phone" => ["248"]],
        "SL" => [ "name" => "Sierra Leone", "phone" => ["232"]],
        "SG" => [ "name" => "Singapore", "phone" => ["65"]],
        "SX" => [ "name" => "Sint Maarten", "phone" => ["1-721"]],
        "SK" => [ "name" => "Slovakia", "phone" => ["421"]],
        "SI" => [ "name" => "Slovenia", "phone" => ["386"]],
        "SB" => [ "name" => "Solomon Islands", "phone" => ["677"]],
        "SO" => [ "name" => "Somalia", "phone" => ["252"]],
        "ZA" => [ "name" => "South Africa", "phone" => ["27"]],
        "KR" => [ "name" => "South Korea", "phone" => ["82"]],
        "SS" => [ "name" => "South Sudan", "phone" => ["211"]],
        "ES" => [ "name" => "Spain", "phone" => ["34"]],
        "LK" => [ "name" => "Sri Lanka", "phone" => ["94"]],
        "SD" => [ "name" => "Sudan", "phone" => ["249"]],
        "SR" => [ "name" => "Suriname", "phone" => ["597"]],
        "SJ" => [ "name" => "Svalbard and Jan Mayen", "phone" => ["47"]],
        "SZ" => [ "name" => "Swaziland", "phone" => ["268"]],
        "SE" => [ "name" => "Sweden", "phone" => ["46"]],
        "CH" => [ "name" => "Switzerland", "phone" => ["41"]],
        "SY" => [ "name" => "Syria", "phone" => ["963"]],
        "TW" => [ "name" => "Taiwan", "phone" => ["886"]],
        "TJ" => [ "name" => "Tajikistan", "phone" => ["992"]],
        "TZ" => [ "name" => "Tanzania", "phone" => ["255"]],
        "TH" => [ "name" => "Thailand", "phone" => ["66"]],
        "TG" => [ "name" => "Togo", "phone" => ["228"]],
        "TK" => [ "name" => "Tokelau", "phone" => ["690"]],
        "TO" => [ "name" => "Tonga", "phone" => ["676"]],
        "TT" => [ "name" => "Trinidad and Tobago", "phone" => ["1-868"]],
        "TN" => [ "name" => "Tunisia", "phone" => ["216"]],
        "TR" => [ "name" => "Turkey", "phone" => ["90"]],
        "TM" => [ "name" => "Turkmenistan", "phone" => ["993"]],
        "TC" => [ "name" => "Turks and Caicos Islands", "phone" => ["1-649"]],
        "TV" => [ "name" => "Tuvalu", "phone" => ["688"]],
        "VI" => [ "name" => "U.S. Virgin Islands", "phone" => ["1-340"]],
        "UG" => [ "name" => "Uganda", "phone" => ["256"]],
        "UA" => [ "name" => "Ukraine", "phone" => ["380"]],
        "AE" => [ "name" => "United Arab Emirates", "phone" => ["971"]],
        "GB" => [ "name" => "United Kingdom", "phone" => ["44"]],
        "US" => [ "name" => "United States", "phone" => ["1"]],
        "UY" => [ "name" => "Uruguay", "phone" => ["598"]],
        "UZ" => [ "name" => "Uzbekistan", "phone" => ["998"]],
        "VU" => [ "name" => "Vanuatu", "phone" => ["678"]],
        "VA" => [ "name" => "Vatican", "phone" => ["379"]],
        "VE" => [ "name" => "Venezuela", "phone" => ["58"]],
        "VN" => [ "name" => "Vietnam", "phone" => ["84"]],
        "WF" => [ "name" => "Wallis and Futuna", "phone" => ["681"]],
        "EH" => [ "name" => "Western Sahara", "phone" => ["212"]],
        "YE" => [ "name" => "Yemen", "phone" => ["967"]],
        "ZM" => [ "name" => "Zambia", "phone" => ["260"]],
        "ZW" => [ "name" => "Zimbabwe", "phone" => ["263"]]
    ];
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @param Context $context
     * @param AddressHelper $addressHelper
     * @param CustomerMetadataInterface $customerMetadata
     * @param Options $options
     * @param AddressMetadataInterface $addressMetadata
     * @param ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    /**
     *
     *
     */
    public function __construct(
      Context $context,
      AddressHelper $addressHelper,
      CustomerMetadataInterface $customerMetadata,
      Options $options,
      AddressMetadataInterface $addressMetadata,
      ScopeConfigInterface $scopeConfig,
      array $data = []     ){
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context, $addressHelper, $customerMetadata, $options, $addressMetadata, $data);
    }


    /**
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('Godogi_InternationalPhoneNumber::widget/telephone.phtml');
    }

    public function getDefaultCountry(){
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $defaultCountry = $this->_scopeConfig->getValue('general/country/default', $storeScope);
        return $defaultCountry;
    }

    public function getAllowedCountries(){
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $allowedCountries = $this->_scopeConfig->getValue('general/country/allow', $storeScope);
        return $allowedCountries;
    }

    public function getCountryInformation($countryCode){
        return self::COUNTRIES[$countryCode];
    }

    public function getLocalTelephone(){
        $internationalTelephoneNumber = $this->getTelephone();
        if($internationalTelephoneNumber){
            if (substr($internationalTelephoneNumber, 0, 1) === '+') {
                $internationalTelephoneNumber = substr($internationalTelephoneNumber, 1);
                $countries = self::COUNTRIES;
                foreach ($countries as $countryCode => $countryInfo) {
                    foreach ($countryInfo['phone'] as $phone) {
                        if(substr($internationalTelephoneNumber, 0, strlen($phone)) === $phone){
                            $internationalTelephoneNumber = substr($internationalTelephoneNumber, strlen($phone));
                            return $internationalTelephoneNumber;
                        }
                    }
                }
                return $internationalTelephoneNumber;
            } else {
                return $internationalTelephoneNumber;
            }
        } else {
            return $internationalTelephoneNumber;
        }
    }
}
