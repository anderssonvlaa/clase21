<?php
include_once 'conexion.php';

$years = isset($_POST['years']) ? $_POST['years'] : [];
$stringY = '';
if ($years != []) {
    foreach($years as $index=>$y){
        if($index == count($years) - 1){
            $stringY .= "'%$y%'";
            continue;
        }
        $stringY .= "'%$y%' OR dates LIKE ";
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/series-label.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<link rel="stylesheet" href="estilo.css">
<title>Document</title>
</head>

<body>
    <form action="index.php" method="POST" style="text-align:center">
        <label for="">Totales anuales mayores o iguales a </label>
        <br>
        <input type="text" name="totales" id="totales" value="<?php echo isset($_POST['totales']) ? $_POST['totales'] : "" ; ?>">
        <br>
        <?php
        $year = "SELECT DISTINCT YEAR(dates) as año
            FROM bill_head WHERE YEAR(dates) BETWEEN 2013 AND 2022 ORDER BY (dates) ASC;";
        $exe= mysqli_query($conexion, $year);

        while($yearSelection= mysqli_fetch_array($exe)){
            $exists = '';
            if (in_array($yearSelection[0], $years)) {
                $exists = 'checked';
            }
            echo "<label>".$yearSelection[0]."</label>";
            echo "<input name='years[]' $exists  value='$yearSelection[0]' type='checkbox' name='' id=''>";

        }

        ?>
        <br>
        <input type="submit" value="Graficar">
    </form>
<figure class="highcharts-figure">
    <div id="container"></div>
</figure>

</body>
</html>
<script >
    
Highcharts.chart('container', {
    

    title: {
        text: 'Empresa XYZ',
        align: 'center'
    },

    subtitle: {
        text: 'Total de ventas anuales de los ultimos 10 años',
        align: 'center'
    },

    yAxis: {
        title: {
            text: 'Ventas $'
        }
    },

    xAxis: {
        accessibility: {
            rangeDescription: 'Desde: 2013 to 2022'
        }
    },

    legend: {
        layout: 'vertical',
        align: 'right',
        verticalAlign: 'middle'
    },

    plotOptions: {
        series: {
            label: {
                connectorAllowed: false
            },
            pointStart: 2013
        }
    },

    series: [{
        name: 'Ventas anuales',
        data: [
            
           <?php
            $totales = isset($_POST['totales'])? $_POST['totales']: "";

            
            if($years !== [] && $totales !== ""){
                $query = "SELECT sum(sale) as Venta, dates  from bill_details
                inner join bill_head on bill_details.code=bill_head.code
                WHERE dates LIKE $stringY
                GROUP by YEAR(dates) 
                HAVING sum(sale) >= $totales";
                $execute = mysqli_query($conexion, $query);
                while ($data = mysqli_fetch_array($execute)) {
                    $d = number_format($data[0], 2, '.', '');
                    echo $data[0] . ",";
                }

            }else if ($totales !== "") {
                    $query = "SELECT sum(sale) as Venta, dates from bill_details
                    inner join bill_head on bill_details.code=bill_head.code
                    GROUP by YEAR(datess)
                    HAVING sum(sale) >= $totales";
                    $execute = mysqli_query($conexion, $query);
                    while ($data = mysqli_fetch_array($execute)) {
                        $d = number_format($data[0], 2, '.', '');
                        echo $data[0] . ",";
                    }

            } else if ($years !== []) {
                    $query = "SELECT sum(sale) as Venta, dates from bill_details
                    inner join bill_head on bill_details.code=bill_head.code
                    WHERE dates LIKE $stringY
                    GROUP by YEAR(dates)";
                    $execute = mysqli_query($conexion, $query);
                    while ($data = mysqli_fetch_array($execute)) {
                        $d = number_format($data[0], 2, '.', '');
                        echo $data[0] . ",";
                    }

            } else if ($totales === "") {
                    $query = "SELECT sum(sale) as Venta, dates  from bill_details
                    inner join bill_head on bill_details.code=bill_head.code
                    GROUP by YEAR(dates)";
                    $execute = mysqli_query($conexion, $query);
                    while ($data = mysqli_fetch_array($execute)) {
                        $d = number_format($data[0], 2, '.', '');
                        echo $data[0] . ",";
                    }
            }
        ?>
            ]
   
    }],

    responsive: {
        rules: [{
            condition: {
                maxWidth: 500
            },
            chartOptions: {
                legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom'
                }
            }
        }]
    }

});
</script>