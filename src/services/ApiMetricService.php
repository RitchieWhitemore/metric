<?php


namespace whitemore\metric\services;

use yii\base\BaseObject;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class ApiMetricService extends BaseObject implements MetricInterface
{
    /**
     * @var string
     */
    protected $apiUrl;

    protected $client;

    public function __construct($apiUrl, $client, $config = [])
    {
        parent::__construct($config);

        $this->apiUrl = $apiUrl;
        $this->client = $client;
    }

    /**
     * @return bool|mixed
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function getMetricData()
    {
        $response = $this->client->createRequest()
            ->setFormat($this->client::FORMAT_JSON)
            ->setMethod('get')
            ->setUrl($this->apiUrl)
            ->send();

        if ($response->headers['http-code'] == 200) {
            return Json::decode($response->content);
        } else {
            return false;
        }
    }

    /**
     * @return MetricData
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function getData(): MetricData
    {
        $data = new MetricData();
        $metricData = $this->getMetricData();

        if ($metricData) {
            $data = new MetricData(ArrayHelper::getValue($metricData, 'categories'),
                ArrayHelper::getValue($metricData, 'values'));
        }

        return $data;
    }

    /**
     * @return MetricData
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function getVisits(): MetricData
    {
        return $this->getData();
    }

    /**
     * @return MetricData
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function getHits(): MetricData
    {
        return $this->getData();
    }
}