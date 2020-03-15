<?php


namespace app\extensions\metric\src\actions\frontend;


use whitemore\metric\services\MetricInterface;
use yii\base\Action;
use yii\web\Controller;

class HitAction extends Action
{
    /**
     * @var MetricInterface
     */
    private $metric;

    /**
     * ViewAction constructor.
     *
     * @param string $id
     * @param Controller $controller
     * @param array $config
     */
    public function __construct(string $id, Controller $controller, MetricInterface $metric, array $config = [])
    {
        parent::__construct($id, $controller, $config);

        $this->metric = $metric;
    }

    /**
     * @return string
     */
    public function run()
    {
        $data = $this->metric->getHits();

        $options = [
            "title" => [
                "text" => "Отчет по хитам",
            ],
            "xAxis" => [
                "categories" => $data->getCategoriesMetric()
            ],
            "yAxis" => [
                "title" => ["text" => "Просмотры"]
            ],
            "series" => [
                [
                    "name" => "Хиты",
                    "data" => $data->getValuesMetric()
                ]

            ]
        ];

        return $this->controller->render('index', compact('data', 'options'));
    }
}