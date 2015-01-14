<?php

class VisualizeData
{


    public static function GraphDataPie($graphData,$sliceField, $labelField, $graphTitle )
    {
        $graphSeriesData = array();
        foreach($graphData as $id => $sessionInfo)
        {
            $sessionString = "['".$sessionInfo[$labelField]."', ".($sessionInfo[$sliceField])."]";
            $graphSeriesData[] = $sessionString;
        }

        $graphString = "<script type=\"text/javascript\">\n
                $(function () {\n
                    var chart;\n

                    $(document).ready(function () {\n

                        // Build the chart\n
                        $('#".$labelField."').highcharts({\n
                            chart: {\n
                                plotBackgroundColor: null,\n
                                plotBorderWidth: null,\n
                                plotShadow: false\n
                            },\n
                            title: {\n
                                text: '".$graphTitle."'\n
                            },\n
                            tooltip: {\n
                                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b> <br>Hours: <b>{point.y:.1f} Hrs.</b>'\n
                            },\n
                            plotOptions: {\n
                                pie: {\n
                                    allowPointSelect: true,\n
                                    cursor: 'pointer',\n
                                    dataLabels: {\n
                                        enabled: false\n
                                    },\n
                                    showInLegend: true\n
                                }\n
                            },\n
                            series: [{\n
                                type: 'pie',\n
                                name: 'Usage %',\n
                                data: [\n";
            $graphString .= implode(',',$graphSeriesData);
            $graphString .= "   ]\n
                            }]\n
                        });\n
                    });\n
            \n
                });\n
            </script>\n";
            $graphString .= "<div id=\"".$labelField."\" style=\"min-width: 310px; height: 640px; margin: 0 auto\"></div>";
        return $graphString;
    }

    public static function GraphTimeLine($graphData,$timeField, $yField,$labelField,$graphTitle, $yLabel)
    {
        $devicesSessionArr = array();
        foreach($graphData as $id => $sessionInfo)
        {
            if(!isset($devicesSessionArr[$sessionInfo['device_id']]))
            {
                $devicesSessionArr[$sessionInfo['device_id']]["device_info"] = $sessionInfo[$labelField];
                $devicesSessionArr[$sessionInfo['device_id']]["session_data"] = array();
            }
            $devicesSessionArr[$sessionInfo['device_id']]["session_data"][] = "[".(strtotime($sessionInfo[$timeField])*1000).", ".($sessionInfo[$yField])."]";
        }

        $graphSeriesData = array();
        foreach($devicesSessionArr as $deviceId => $deviceInfo)
        {
            $seriesString = "\n{\n";
            $seriesString .= "name: '".$deviceInfo['device_info']."', \n";
            $seriesString .= "data: [";
            $seriesString .= implode(',',$deviceInfo['session_data']);
            $seriesString .= "\n]}";

            $graphSeriesData[]=$seriesString;
        }

        $graphString ="<script type=\"text/javascript\">\n
                        $(function () {\n
                            $('#".$labelField."_".$yField."_timeline').highcharts({\n
                                chart: {\n
                                    type: 'spline'\n
                                },\n
                                title: {\n
                                    text: '".$graphTitle."'\n
                                },\n
                                xAxis: {\n
                                    type: 'datetime',\n
                                    dateTimeLabelFormats: { // don't display the dummy year\n
                                        month: '%e. %b',\n
                                        year: '%b'\n
                                    },\n
                                    title: {\n
                                        text: 'Date'\n
                                    }\n
                                },\n
                                yAxis: {\n
                                    title: {\n
                                        text: '".$yLabel."'\n
                                    },\n
                                    min: 0\n
                                },\n
                                tooltip: {\n
                                    headerFormat: '<b>{series.name}</b><br>',\n
                                    pointFormat: '{point.x:%e. %b}: {point.y:.2f} ".$yLabel."'\n
                                },\n
                    \n
                                series: [\n";
        $graphString .= implode(',',$graphSeriesData);
        $graphString .= "
                                     ]\n
                            });\n
                        });\n
                    </script>\n ";
        $graphString .= "<div id=\"".$labelField."_".$yField."_timeline\" style=\"min-width: 310px; height: 640px; margin: 0 auto\"></div>";
        return $graphString;
    }

    public static function ListSessionsTable($tableData,$tableHeaders,$dataColumns,$tableNameId,$rowSelected=0)
    {
        $tableString =  "
            <script type=\"text/javascript\" class=\"init\">
            $(document).ready( function () {\n
            var ".$tableNameId." = $('#".$tableNameId."').dataTable({
                \"dom\": 'T<\"clear\">lfrtip',
                \"tableTools\": {
                    \"sSwfPath\": \"swf/copy_csv_xls_pdf.swf\"
                }
                    });\n
             ".$tableNameId.".fnDisplayRow( ".$tableNameId.".fnGetNodes()[".$rowSelected."] );
                } );\n

            </script>";
        $tableString .= "<table id=\"".$tableNameId."\" class=\"display compact\">";
        $tableString .= "<thead><tr>";
        foreach($tableHeaders as $header)
        {
            $tableString .= "<th>".$header."</th>";
        }
        $tableString .= "</tr>
                           </thead>
                            <tbody>";
        foreach($tableData as $id=>$sessionInfo)
        {
            $tableString .= "<tr>";
            foreach($dataColumns as $columnName)
            {
                $tableString.= "<td>".$sessionInfo[$columnName]."</td>";
            }
            $tableString .= "</tr>";
        }
        $tableString .= "</tbody>";
        $tableString .= "</table>";

        return $tableString;
    }

}

?>
