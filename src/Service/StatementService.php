<?php

namespace FinnAdvisor\Service;

use Amenadiel\JpGraph\Graph\Graph;
use Amenadiel\JpGraph\Graph\PieGraph;
use Amenadiel\JpGraph\Plot\LinePlot;
use Amenadiel\JpGraph\Plot\PiePlot;
use FinnAdvisor\Model\Operation;
use FinnAdvisor\Model\VK\Button;
use FinnAdvisor\Model\VK\Template;
use FinnAdvisor\Model\VK\TemplateAction;
use FinnAdvisor\Model\VK\TemplateElement;
use FinnAdvisor\VK\VKBotApiClient;
use Logger;
use RuntimeException;

class StatementService
{
    private VKBotApiClient $apiClient;
    private Logger $logger;

    public function __construct(VKBotApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
        $this->logger = Logger::getLogger(__CLASS__);
    }

    public function createStatement(string $userId, array $operations): Template
    {
        $operationsByCategory = [];
        foreach ($operations as $operation) {
            $operationsByCategory[$operation->getCategory()][] = $operation;
        }
        $summaryChart = $this->generateSummary($userId, $operationsByCategory);
        $chartByCategory = $this->generateCharts($userId, $operationsByCategory);

        $summaryId = $this->apiClient->uploadPhotos($summaryChart);
        $idByCategory = $this->uploadCharts($chartByCategory);

        $charts = array_values($chartByCategory);
        $charts[] = $summaryChart;
        $this->cleanupCharts($charts);

        $elements = [];
        $elements[] = $this->generateSummaryTemplateElement($summaryId);
        foreach ($idByCategory as $category => $id) {
            $elements[] = $this->generateTemplateElement($category, $id);
        }
        return new Template("carousel", $elements);
    }

    private function generateSummaryTemplateElement(string $photoId): TemplateElement
    {
        return new TemplateElement(
            "Общее",
            "Общий отчёт по всем категориям",
            $photoId,
            [
                Button::textButton("Операции", "primary")
            ],
            new TemplateAction("open_photo")
        );
    }

    private function generateTemplateElement(string $category, string $photoId): TemplateElement
    {
        return new TemplateElement(
            mb_convert_case($category, MB_CASE_TITLE, 'UTF-8'),
            "Отчёт по заданной категории",
            $photoId,
            [
                Button::textButton("Операции $category", "primary"),
            ],
            new TemplateAction("open_photo")
        );
    }

    private function generateCharts(string $userId, array $operationsByCategory): array
    {
        $chartByCategory = [];
        foreach ($operationsByCategory as $category => $operations) {
            $chartByCategory[$category] = $this->generateChart($userId, $category, $operations);
        }
        return $chartByCategory;
    }

    private function generateSummary(string $userId, array $operationsByCategory): string
    {
        $data = [];
        foreach ($operationsByCategory as $operations) {
            $total = 0;
            foreach ($operations as $op) {
                $total += $op->getSum();
            }
            $data[] = $total;
        }

        $width = 1105;
        $height = 680;
        $pieGraph = $this->generatePie($width, $height, $data, array_keys($operationsByCategory));

        $this->mkdirIfNeeded("./charts");
        $path = "charts/$userId.jpeg";
        $pieGraph->Stroke($path);

        return $path;
    }

    private function generateChart(string $userId, string $category, array $operations): string
    {
        $width = 1105;
        $height = 680;

        $graph = $this->generateLine($width, $height, $category, $operations);

        $this->mkdirIfNeeded("./charts");
        $path = "charts/$userId#$category.jpeg";
        $graph->Stroke($path);

        return $path;
    }

    private function uploadCharts(array $chartByCategory): array
    {
        $idByCategory = [];
        foreach ($chartByCategory as $category => $chart) {
            $idByCategory[$category] = $this->apiClient->uploadPhotos($chart);
        }
        return $idByCategory;
    }

    private function mkdirIfNeeded(string $dir): void
    {
        if (!is_dir($dir)) {
            if (!mkdir($dir, recursive: true)) {
                throw new RuntimeException("Cannot create directory $dir");
            }
        }
    }

    private function cleanupCharts(array $charts): void
    {
        foreach ($charts as $chart) {
            if (!unlink($chart)) {
                $this->logger->error("Impossible to delete a file '$chart'");
            }
        }
    }

    private function generatePie(int $width, int $height, array $data, array $legends): PieGraph
    {
        $graph = new PieGraph($width, $height);
        $graph->SetMargin(60, 20, 40, 60);
        $graph->title->SetFont(FF_ARIAL, FS_BOLD, 12);
        $graph->title->Set("Процентное соотношение категорий");

        $graph->SetBox();

        $p1 = new PiePlot($data);
        $p1->ShowBorder();
        $p1->SetColor('black');
        $p1->SetSliceColors([
            '#3D5A80', '#98C1D9', '#E0FBFC', '#EE6C4D', '#293241', '#2B2B42', '#8D99AE', '#EDF2F4', '#EF233C', '#d90429'
        ]);
        $p1->SetLegends($legends);

        $graph->Add($p1);
        return $graph;
    }

    private function generateLine(int $width, int $height, string $category, array $operations): Graph
    {
        $graph = new Graph($width, $height);
        $graph->SetScale('datlin');
        $graph->SetMargin(60, 20, 40, 60);
        $graph->xaxis->scale->SetDateFormat('d.m.Y');

        $graph->title->SetFont(FF_ARIAL, FS_BOLD, 12);
        $graph->title->Set("Отчёт по категории $category");

        $graph->xaxis->SetFont(FF_ARIAL, FS_NORMAL, 8);
        $graph->xaxis->SetLabelAngle(30);

        $dataX = array_map(fn(Operation $op) => strtotime($op->getDate()), $operations);
        $dataY = [];
        foreach ($operations as $i => $op) {
            $dataY[] = ($i == 0 ? 0 : $dataY[$i - 1]) + $op->getSum();
        }
        $lp1 = new LinePlot($dataY, $dataX);
        $lp1->SetStepStyle();
        $lp1->SetWeight(0);
        $lp1->SetFillColor('orange@0.85');
        $graph->Add($lp1);

        $lp2 = new LinePlot($dataY, $dataX);
        $lp2->SetStepStyle();
        $lp2->SetColor('orange');
        $graph->Add($lp2);
        return $graph;
    }
}
