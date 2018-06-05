<?php
/**
 * Created by PhpStorm.
 * User: ilbenjel
 * Date: 29/05/18
 * Time: 15:38.
 */

namespace App\Entity;

use CMEN\GoogleChartsBundle\GoogleCharts\Charts\Material\BarChart;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\Material\LineChart;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;

class ChartBuilder
{
    /**
     * @param $title
     * @param $dataTable
     * @param $series
     * @param $axes
     *
     * @return BarChart
     */
    public static function createBarChart(String $title, array $dataTable, array $series, array $axes)
    {
        $chart = new BarChart();
        $chart->getData()->setArrayToDataTable($dataTable);

        $chart->getOptions()->getChart()
            ->setTitle($title);

        $chart->getOptions()
            ->setBackgroundColor('#EAEAEA')
            ->setHeight(450)
            ->setWidth('45%')
            ->setOrientation('horizontal')
            ->setSeries($series)
            ->setAxes($axes);

        $chart->getOptions()->getTitleTextStyle()
            ->setBold(true)
            ->setColor('#009900')
            ->setItalic(true)
            ->setFontName('Arial')
            ->setFontSize(18);

        return $chart;
    }

    /**
     * @param $title
     * @param $dataTable
     * @param $series
     * @param $axes
     *
     * @return LineChart
     */
    public static function createLineChart(String $title, array $dataTable, array $series, array $axes)
    {
        $lineChart = new LineChart();

        $lineChart->getData()->setArrayToDataTable($dataTable);

        $lineChart->getOptions()->getChart()
            ->setTitle($title);

        $lineChart->getOptions()->getTitleTextStyle()
            ->setBold(true)
            ->setColor('#009900')
            ->setItalic(true)
            ->setFontName('Arial')
            ->setFontSize(18);

        $lineChart->getOptions()
            ->setBackgroundColor('#EAEAEA')
            ->setHeight(450)
            ->setWidth('45%')
            ->setSeries($series)
            ->setAxes($axes);

        return $lineChart;
    }

    /**
     * @param $title
     * @param $dataTable
     *
     * @return PieChart
     */
    public static function createPieChart(String $title, array $dataTable)
    {
        $pieChart = new PieChart();

        $pieChart->getData()->setArrayToDataTable($dataTable);

        $pieChart->getOptions()
            ->setBackgroundColor('#EAEAEA')
            ->setTitle($title)
            ->setHeight(450)
            ->setWidth('45%');

        $pieChart->getOptions()->getTitleTextStyle()
            ->setBold(true)
            ->setColor('#009900')
            ->setItalic(true)
            ->setFontName('Arial')
            ->setFontSize(18);

        return $pieChart;
    }
}
