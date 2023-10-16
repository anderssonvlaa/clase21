<?php
include_once 'conexion.php';   
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
    <form action="index.php" method="POST" style = "text-align:center">
        <label for="">Totales anuales mayores o iguales a</label>
        <input type="text" name = "totales" id = "totales" value="<?php echo isset($_POST['totales']) ? $_POST['totales']:""; ?>">
        <?php
            $years = "SELECT DISTINCT year(date) AS Años FROM bill_head 
            WHERE year(date) BETWEEN 2013 AND 2022 ORDER BY (date) ASC;
            ";
            $execute= mysqli_query($conexion, $years);

            while($yearsSelection= mysqli_fetch_array($execute))
            {
                echo "<label>".$yearsSelection[0]."</label>";
                echo "<input type= 'checkbox' name= '' id ''>";
            }
        ?>
        <input type="submit" value="Graficar">
    </form>
<figure class="highcharts-figure">
    <div id="container"></div>
   
</figure>

</body>
</html>
<script>
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
        text: 'Ventas en $'
    }
},

xAxis: {
    accessibility: {
        rangeDescription: 'Desde 2013 al 2022'
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
        $totales = isset ($_POST['totales']) ? $_POST['totales']:"";
        if($totales===""){
        $query="SELECT SUM(sale) as Venta, date FROM bill_details 
        INNER JOIN bill_head ON bill_details.code=bill_head.code
        GROUP BY YEAR(date)";
        $execute= mysqli_query($conexion, $query);
        while($data=mysqli_fetch_array($execute))
        {
            $d= number_format($data[0],2,'.','');
            echo $d . ",";
        }
    }else {
        $query= "SELECT SUM(sale) as Venta, date FROM bill_details 
        INNER JOIN bill_head ON bill_details.code=bill_head.code 
        GROUP BY YEAR(date) HAVING SUM(sale) >= $totales";
        $execute= mysqli_query($conexion, $query);
        while($data=mysqli_fetch_array($execute))
        {
            $d= number_format($data[0],2,'.','');
            echo $d . ",";
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