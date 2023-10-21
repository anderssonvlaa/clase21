<?php
include_once 'conexion.php';
$totales= isset($_POST['totales']) ? $_POST['totales']: "";

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
<form action="index.php" method="post" style="text-align:center">
    <label for=""></label>
    <input type="text" name="totales" id="totales" value="<?php echo isset($_POST['totales']) ? $_POST['totales'] : ""; ?>">
    
    <!-- <div id="yearCheckboxes" style="float: center; margin-right: 20px;">
        <?php
        
        $yearsQuery = "SELECT DISTINCT YEAR(dates) as year FROM bill_details
            INNER JOIN bill_head ON bill_details.code=bill_head.code
            ORDER BY year ASC";
        $yearsResult = mysqli_query($conexion, $yearsQuery);
        while ($rowYear = mysqli_fetch_assoc($yearsResult)) {
            $year = $rowYear['year'];
            $checked = (isset($_POST['años']) && in_array($year, $_POST['años'])) ? 'checked' : '';
            echo '<label for="year_' . $year . '"><input type="checkbox" name="años[]" id="year_' . $year . '" value="' . $year . '" ' . $checked . '> ' . $year . '</label>';
        }
        ?>
    </div> -->
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
                text: 'ventas en $'
            }
        },

        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle'
        },

        xAxis: {
            categories: [
                <?php
                

                if ($totales !== "") {
                    
                    $query = "SELECT DISTINCT YEAR(dates) as year FROM bill_details
                        INNER JOIN bill_head ON bill_details.code=bill_head.code
                        GROUP BY YEAR(dates)
                        HAVING SUM(sale) >= $totales
                        ORDER BY year ASC";
                } else {
                    
                    $query = "SELECT DISTINCT YEAR(dates) as year FROM bill_details
                        INNER JOIN bill_head ON bill_details.code=bill_head.code
                        ORDER BY year ASC";
                }

                $result = mysqli_query($conexion, $query);

                while ($row = mysqli_fetch_assoc($result)) {

                    if (isset($_POST['años']) && is_array($_POST['años'])) {

                        $yearSelection = $_POST['años'];
                        foreach ($yearSelection as $years) {
                            if($years===$row['year']){
                                echo "'" . $row['year'] . "',";
                            }
                           
                        }
                    } else {
                        echo "'" . $row['year'] . "',";
                    }
                    

                }
                ?>
            ]
        },

        series: [{
            name: 'Ventas anuales',
            data: [
                <?php
                  
                
                  $yearSelection = isset($_POST['años']) ? $_POST['años'] : array();
                if($totales ===""){
                    

                    if (!empty($yearSelection)) {
                        $yearSelection_str = implode(',', $yearSelection);
                        $query = "SELECT SUM(sale) as sale, YEAR(dates) as year FROM bill_details
                            INNER JOIN bill_head ON bill_details.code = bill_head.code
                            WHERE YEAR(dates) IN ($yearSelection_str)
                            GROUP BY YEAR(dates)";
                    } else {
                        // Si no se han seleccionado años, query todos los años
                        $query = "SELECT SUM(sale) as sale, YEAR(dates) as year FROM bill_details
                            INNER JOIN bill_head ON bill_details.code = bill_head.code
                            GROUP BY YEAR(dates)";
                    }
                               $executar = mysqli_query($conexion, $query);
                               while ($dato = mysqli_fetch_array($executar)) {
                                   $d=number_format($dato[0],2,'.','');
                                   echo $d.",";
                 }
 
                }
                else {
                

                    if (!empty($yearSelection)) {
                        $yearSelection_str = implode(',', $yearSelection);
                        $query = "SELECT SUM(sale) as sale, YEAR(dates) as year FROM bill_details
                            INNER JOIN bill_head ON bill_details.code = bill_head.code
                            WHERE YEAR(dates) IN ($yearSelection_str)
                            GROUP BY YEAR(dates)
                    HAVING sum(sale) >=$totales";
                    } else {
                        // Si no se han seleccionado años, query todos los años
                        $query = "SELECT SUM(sale) as sale, YEAR(dates) as year FROM bill_details
                            INNER JOIN bill_head ON bill_details.code = bill_head.code
                            GROUP BY YEAR(dates) 
                    HAVING sum(sale) >=$totales";
                    }
                               $executar = mysqli_query($conexion, $query);
                               while ($dato = mysqli_fetch_array($executar)) {
                                   $d=number_format($dato[0],2,'.','');
                                   echo $d.",";
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
    $(document).ready(function () {
    $("#totales").keyup(function () {

        var totales = $(this).val();

        
        var totales = $("#totales").val();
           
        $.ajax({
            type: "POST", 
            url: "index.php", 
            data: { totales: totales
             }, 
            success: function (response) {
             console.log(response);
                $("#container").html(response);
            }
        });
    });
});
</script>