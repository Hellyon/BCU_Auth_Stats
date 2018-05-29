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
     *
     * @return BarChart
     */
    public static function createBarChart($title, $dataTable)
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
            ->setSeries([['axis' => 'heures'], ['axis' => 'connexions']])
            ->setAxes(['x' => [
                'connexions' => ['side' => 'top', 'label' => 'Nombre de connexions'], ],
                'heures' => ['side' => 'top', 'label' => 'Nombre d\'heures'],
            ]);

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
     *
     * @return LineChart
     */
    public static function createLineChart($title, $dataTable)
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
            ->setSeries([['axis' => 'heures'], ['axis' => 'connexions']])
            ->setAxes(['y' => ['heures' => ['label' => 'Nombre d\'heures'], 'connexions' => ['label' => 'Nombre de connexions']]]);

        return $lineChart;
    }

    /**
     * @param $title
     * @param $dataTable
     *
     * @return PieChart
     */
    public static function createPieChart($title, $dataTable)
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
