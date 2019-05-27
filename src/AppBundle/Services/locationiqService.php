<?php

namespace AppBundle\Services;

class locationiqService
{
    /**
     * @var string
     */
    protected $accessKey;

    /**
     * locationiqService constructor.
     * @param string $accessKey
     */
    public function __construct(string $accessKey)
    {
        $this->accessKey = $accessKey;
    }

    /**
     * @return string
     */
    public function getAccessKey()
    {
        return $this->accessKey;
    }

    /**
     * @param string $accessKey
     * @return $this
     */
    public function setAccessKey(string $accessKey)
    {
        $this->accessKey = $accessKey;

        return $this;
    }

    /**
     * @param string $lat
     * @param string $lon
     * @return mixed|string
     */
    public function getDataByCoords(string $lon, string $lat)
    {
        $data = http_build_query([
            'key'    => $this->accessKey,
            'lat'    => $lat,
            'lon'    => $lon,
            'format' => 'json',
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://us1.locationiq.com/v1/reverse.php?{$data}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = json_decode(curl_exec($ch));
        curl_close($ch);

        return $data;
    }
}