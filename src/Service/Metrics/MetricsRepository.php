<?php

namespace FinnAdvisor\Service\Metrics;

use Exception;
use FinnAdvisor\Model\Metric;
use Logger;
use PDO;
use PDOException;

class MetricsRepository
{
    private PDO $pdo;
    private Logger $logger;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->logger = Logger::getLogger(__CLASS__);
    }

    public function writeMetric(Metric $metric): void
    {
        $type = $metric->getType();
        $value = $metric->getValue();
        $labels = $metric->getLabels();

        try {
            $this->pdo
                ->query(
                    "INSERT INTO metrics VALUES ('$type', '$labels', $value, DEFAULT)"
                );
        } catch (Exception $e) {
            $this->logger->error("Exception during inserting metrics ($type, $labels, $value)", $e);
        }
    }

}
