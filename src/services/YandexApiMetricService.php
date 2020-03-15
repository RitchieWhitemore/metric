<?php


namespace whitemore\metric\services;


use Yii;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class YandexApiMetricService extends BaseObject implements MetricInterface
{
    /**
     * @var string
     */
    public $apiUrl = 'https://api-metrika.yandex.net';

    /**
     * @var string
     */
    protected $token = '';

    /**
     * @var string
     */
    protected $ids = '';

    protected $client;

    /**
     * @var string
     */
    public $lang = 'ru';

    public $dateBegin = '2019-01-01';

    public $dateEnd = '2019-12-01';

    public function __construct($ids, $token, $client, $config = [])
    {
        parent::__construct($config);

        $this->ids = $ids;
        $this->token = $token;
        $this->client = $client;
    }

    public function getMetricData(string $method, array $options)
    {
        $url = $this->createUrl($method, $options);

        $response = $this->client->createRequest()
            ->setFormat($this->client::FORMAT_JSON)
            ->setMethod('get')
            ->setUrl($url)
            ->setHeaders([
                'Authorization' => 'OAuth ' . $this->token,
                'Content-Type' => 'application/x-yametrika+json'
            ])->send();

        if ($response->headers['http-code'] == 200) {
            return Json::decode($response->content);
        } else {
            return false;
        }
    }

    /**
     * @param array $options
     *
     * @return array
     */
    protected function normalizeOptions(array $options): array
    {
        return array_map(function ($key, $value) {
            return $key . '=' . $value;
        }, array_keys($options), array_values($options));

    }

    /**
     * @param string $method
     * @param array $options
     * @return string
     */
    protected function createUrl(string $method, array $options): string
    {
        $options['ids'] = $this->ids;
        $options['lang'] = $this->lang;
        $line = implode('&', $this->normalizeOptions($options));
        return $this->apiUrl . '/' . $method . '?' . $line;
    }

    /**
     * @param array $options
     * @return MetricData
     * @throws \yii\base\InvalidConfigException
     */
    public function getData(array $options): MetricData
    {
        $data = new MetricData();
        $metricData = $this->getMetricData('stat/v1/data/bytime', $options);

        if ($metricData) {
            $timeIntervals = ArrayHelper::getValue($metricData, 'time_intervals');

            $categories = [];
            foreach ($timeIntervals as $interval) {
                if ($interval[0] == $interval[1]) {
                    $categories[] = Yii::$app->formatter->asDate($interval[0], 'php:d.m.Y l');
                } else {
                    $categories[] = Yii::$app->formatter->asDate($interval[0],
                            'php:d.m.Y') . ' - ' . Yii::$app->formatter->asDate($interval[1], 'php:d.m.Y');
                }
            }

            $data = new MetricData($categories, ArrayHelper::getValue($metricData, 'data.0.metrics.0'));
        }

        return $data;

    }

    /**
     * @return MetricData
     * @throws \yii\base\InvalidConfigException
     */
    public function getVisits(): MetricData
    {
        $options = [
            'group' => 'month',
            'date1' => $this->dateBegin,
            'date2' => $this->dateEnd,
            'metrics' => 'ym:s:users',
        ];

        return $this->getData($options);
    }

    /**
     * @return MetricData
     * @throws \yii\base\InvalidConfigException
     */
    public function getHits(): MetricData
    {
        $options = [
            'group' => 'month',
            'date1' => $this->dateBegin,
            'date2' => $this->dateEnd,
            'metrics' => 'ym:s:hits',
        ];

        return $this->getData($options);
    }
}