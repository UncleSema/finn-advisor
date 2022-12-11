<?php

namespace FinnAdvisor\Service\Metrics;

use FinnAdvisor\Model\Metric;

class MetricsService
{
    private MetricsRepository $metricsRepository;

    public function __construct(MetricsRepository $metricsRepository)
    {
        $this->metricsRepository = $metricsRepository;
    }

    public function newMessage(string $messageType): void
    {
        $metric = new Metric("new_message", $messageType, 1);
        $this->metricsRepository->writeMetric($metric);
    }
}
