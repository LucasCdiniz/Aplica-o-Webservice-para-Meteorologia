<?php

namespace Climatempo;

use Climatempo\Forecast\Forecast;
use Climatempo\Weather\Weather;
use Climatempo\Flood\Flood;

class Climatempo
{
    protected $token;

    public function __construct($token) 
    {
        $this->token = $token;
    }

    /****************************
    ****      FORECAST
    /***************************/

    public function fifteenDays($cityId) 
    {
        $url        = 'http://apiadvisor.climatempo.com.br/api/v1/forecast/locale/'.$cityId.'/days/15?token='.$this->token;
        $content    = $this->request($url, null, 'get', null, $httpCode);

        if ($httpCode != 200) {
            throw new \Exception($this->readErrorMessage($content), 1);            
        }

        return new Forecast(json_decode($content), 'Climatempo\Forecast\FifteenDaysWrapper');
    }

    public function seventyTwoHours($cityId) 
    {
        $url        = 'http://apiadvisor.climatempo.com.br/api/v1/forecast/locale/'.$cityId.'/hours/72?token='.$this->token;
        $content    = $this->request($url, null, 'get', null, $httpCode);

        if ($httpCode != 200) {
            throw new \Exception($this->readErrorMessage($content), 1);            
        }

        return new Forecast(json_decode($content), 'Climatempo\Forecast\SeventyTwoHoursWrapper');
    }

    /****************************
    ****      WEATHER
    /***************************/

    public function current($cityId) 
    {
        $url        = 'http://apiadvisor.climatempo.com.br/api/v1/weather/locale/'.$cityId.'/current?token='.$this->token;
        $content    = $this->request($url, null, 'get', null, $httpCode);

        if ($httpCode != 200) {
            throw new \Exception($this->readErrorMessage($content), 1);            
        }

        return new Weather(json_decode($content));
    }

    /****************************
    ****      LOCALE
    /***************************/

    public function getCityById($cityId) 
    {
        $url        = 'http://apiadvisor.climatempo.com.br/api/v1/locale/city/'.$cityId.'?token='.$this->token;
        $content    = $this->request($url, null, 'get', null, $httpCode);

        if ($httpCode != 200) {
            throw new \Exception($this->readErrorMessage($content), 1);            
        }

        return json_decode($content, true);
    }

    /**
     * This will not look up for fragments, if you want to search for a city Id 
     * with this method, you'll need the full gramatic correct name of the city
     */
    public function findCity($name, $state = '') 
    {
        $url = 
        'http://apiadvisor.climatempo.com.br/api/v1/locale/city?name='.$name.
        ($state ? '&state='.$state : '').
        '&token='.$this->token;

        $content = $this->request($url, null, 'get', null, $httpCode);

        if ($httpCode != 200) {
            throw new \Exception($this->readErrorMessage($content), 1);            
        }

        return json_decode($content, true);
    }

    /****************************
    ****      History
    /***************************/

    public function history($cityId, $from, $to = null) 
    {
        $url = 
        'http://apiadvisor.climatempo.com.br/api/v1/history/locale/'.$cityId.'?from='.$from.
        ($to ? '&to='.$to : '').
        '&token='.$this->token;
        
        $content = $this->request($url, null, 'get', null, $httpCode);

        if ($httpCode != 200) {
            throw new \Exception($this->readErrorMessage($content), 1);
        }

        return new Forecast(json_decode($content), 'Climatempo\History\History');
    }

    /****************************
    ****      CLIMATE
    /***************************/

    public function climateRain($cityId, $latitude = null, $longitude = null) 
    {
        $url = 
        'http://apiadvisor.climatempo.com.br/api/v1/climate/rain/locale/'.$cityId.'?'.
        ($latitude ? '&latitude='.$latitude : '').
        ($longitude ? '&longitude='.$longitude : '').
        ($latitude || $longitude ? '&' : '').'token='.$this->token;

        $content = $this->request($url, null, 'get', null, $httpCode);

        if ($httpCode != 200) {
            throw new \Exception($this->readErrorMessage($content), 1);
        }

        return new Forecast(json_decode($content), 'Climatempo\ClimateRain');
    }

    public function climateTemperature($cityId, $latitude = null, $longitude = null) 
    {
        $url = 
        'http://apiadvisor.climatempo.com.br/api/v1/climate/temperature/locale/'.$cityId.'?'.
        ($latitude ? '&latitude='.$latitude : '').
        ($longitude ? '&longitude='.$longitude : '').
        ($latitude || $longitude ? '&' : '').'token='.$this->token;

        $content = $this->request($url, null, 'get', null, $httpCode);

        if ($httpCode != 200) {
            throw new \Exception($this->readErrorMessage($content), 1);
        }

        return new Forecast(json_decode($content), 'Climatempo\ClimateTemperature');
    }

    /****************************
    ****      FLOODING
    /***************************/

    public function floodingRisk($latitude, $longitude) 
    {
        $url = 
        'http://apiadvisor.climatempo.com.br/api/v1/flood/risk?'.
        'latitude='.$latitude.
        '&longitude='.$longitude.
        'token='.$this->token;

        $content = $this->request($url, null, 'get', null, $httpCode);
        
        if ($httpCode != 200) {
            throw new \Exception($this->readErrorMessage($content), 1);
        }

        return new Flood(json_decode($content), 'Climatempo\Flood\FloodRiskWrapper');
    }

    /****************************
    ****      INDEX
    /***************************/

    public function mosquitoProliferation($cityId) 
    {
        $url = 
        'http://apiadvisor.climatempo.com.br/v1/index/mosquito/locale/'.$cityId.'/days/?'.
        'token='.$this->token;

        $content = $this->request($url, null, 'get', null, $httpCode);

        if ($httpCode != 200) {
            throw new \Exception($this->readErrorMessage($content), 1);
        }

        return new Forecast(json_decode($content), 'Climatempo\MosquitoProliferation');
    }

    /*-----------------------------*/

    public function addLocalesToToken($locales) 
    {
        $url = 
        'http://apiadvisor.climatempo.com.br/api-manager/user-token/'.$this->token.'/locales';

        $locales = (array) $locales;

        $content = $this->request($url, array('localeId' => $locales), 'put', array('Content-Type: application/x-www-form-urlencoded'), $httpCode);
        
        if ($httpCode != 200) {
            throw new \Exception($this->readErrorMessage($content), 1);
        }

        $json = json_decode($content, true);

        return $json['locales'];
    }

    /*-----------------------------*/

    /**
     * @param string $url
     * @param array $data
     * @param string $method
     * @param int $httpCode Will return the request code
     * @return string
     */
    protected function request($url, $data = null, $method = 'get', $headers = array(), &$httpCode = null) 
    {
        $method     = strtolower($method);
        $ch         = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        switch ($method) {
            case 'post':
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                break;            
            case 'put':
                //curl_setopt($ch, CURLOPT_PUT, 1);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                break;
        }

        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $content    = curl_exec($ch);
        $httpCode   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $content;
    }

    protected function readErrorMessage($content) 
    {
        $json = json_decode($content, true);
        if (json_last_error() != JSON_ERROR_NONE) {
            return $content;
        }

        return isset($json['detail']) ? $json['detail'] : $content;
    }
}
