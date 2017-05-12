<html>
  <head>
    <style>
      table{
        text-align: center;
        border: #D4D4D4 solid 1px;
        background-color: #FAFAFA;
        margin-left: auto;
        margin-right: auto;
      }

      .table1{
        background-color: #F3F3F3;
      }

      .table1 td, .table1 th{
        padding: 15px 0px 10px 0px;
      }

      th{
        background-color: #F3F3F3;
      }

      .title{
        font-size: 30px;
        border-bottom: #D4D4D4 solid 1px;
      }

      .table1{
        width: 400px;
      }

      .table2{
        width: 70%;
        margin-top: 50px;
        border-collapse: collapse;
      }

      .table2 td, .table2 th{
        border: #D4D4D4 solid 1px;
        text-align: left;
      }

      .stock_table th{
        text-align: left;
      }

      .stock_table td{
        text-align: center;
      }

    </style>
    <script>
      function clearField(){
        document.getElementById('company').value = '';
        document.getElementsByClassName('table2')[0].style.display = 'none';
      }

      function showTable(){
        if($('#company').value == ''){
          document.getElementsByClassName('table2')[0].style.display = 'none';
        }else{
          document.getElementsByClassName('table2')[0].style.display = 'block';
        }
      }
    </script>
  </head>
  <body>
    <table class='table1'>
      <tr>
        <td class='title'><i> Stock Search </i></td>
      </tr>
      <form action='#'>
        <tr>
          <td>
            Company Name or Symbol:
            <input type='text' name='company' id='company' required='required' value="<?php echo isset($_GET['company']) ? $_GET['company'] : '' ?>"></input>
          </td>
        </tr>
        <tr>  
          <td>
            <input type='submit' name='submit' onclick='showTable()'></input>
            <input type='button' name='reset' value='Clear' onclick='clearField()'></input>
          </td>
        </tr>
      </form>
      <tr>
        <td>
          <a href="http://www.markit.com/product/markit-on-demand"> Powered by Markit on Demand </a>
        <td>
      </tr>
    </table>
    <?php
      if(isset($_GET['submit'])){
        
        $name = $_GET['company'];

        $url = "http://dev.markitondemand.com/MODApis/Api/v2/Lookup/xml?input=$name";
      
        $xml = simplexml_load_file($url);
        
        if(!empty($xml)){
          echo "<table class='table2'><tr><th>Name</th><th>Symbol</th><th>Exchange</th><th>Details</th></tr>";
          foreach( $xml->LookupResult as $oEntry ){
            $symbol = $oEntry->Symbol;
            echo "<tr><td>";
            echo $oEntry->Name . PHP_EOL;
            echo "</td><td>";
            echo $symbol . PHP_EOL;
            echo "</td><td>";
            echo $oEntry->Exchange . PHP_EOL;
            echo "</td><td>";
            echo "<a href=\"?company=$name&symbol=$symbol\">More Info</a>";
            echo "</td></tr>";
          }
        } else{
          echo"<table class='table2'><tr><td style='text-align: center;'> No Records has been found.</td></tr>";
        }
          echo "</table>";  
      }

      if(isset($_GET['symbol'])){
        $search_url = "http://dev.markitondemand.com/MODApis/Api/v2/Quote/json?symbol=". $_GET['symbol'];

        $result = json_decode(@file_get_contents($search_url));
        
        if(($result->Status) == 'SUCCESS'){
          echo "<table class='table2 stock_table'><tr><th>Name</th><td>". $result->Name . "</td></tr>";
          echo "<tr><th>Symbol</th><td>". $result->Symbol . "</td></tr>";
          echo "<tr><th>Last Price</th><td>". $result->LastPrice . "</td></tr>";
          
          echo "<tr><th>Change</th><td>". round($result->Change, 2);
          if($result->Change < 0){
            echo "<img src=\"http://cs-server.usc.edu:45678/hw/hw6/images/Red_Arrow_Down.png\" height='10px' width='10px'></img>";
          }
          if($result->Change > 0){
            echo "<img src=\"http://cs-server.usc.edu:45678/hw/hw6/images/Green_Arrow_Up.png\" height='10px' width='10px'></img>";
          }
          echo "</td></tr>";

          echo "<tr><th>Change Percent</th><td>". round($result->ChangePercent, 2) . "%";
          if($result->ChangePercent < 0){
            echo "<img src=\"http://cs-server.usc.edu:45678/hw/hw6/images/Red_Arrow_Down.png\" height='10px' width='10px'></img>";
          }
          if($result->ChangePercent > 0){
            echo "<img src=\"http://cs-server.usc.edu:45678/hw/hw6/images/Green_Arrow_Up.png\" height='10px' width='10px'></img>";
          }
          echo "</td></tr>";
          
          date_default_timezone_set('America/New_York');

          echo "<tr><th>Time Stamp</th><td>".date("Y-m-d  H:i A", strtotime($result->Timestamp)). " EST";
          echo "</td></tr>";

        
          $marketcap = $result->MarketCap;
          echo "<tr><th>Market Cap</th><td>". round(($marketcap/1000000000), 2) . " B";

          echo "</td></tr>";
          
          echo "<tr><th>Volume</th><td>". number_format($result->Volume);
          echo "</td></tr>";

          $changeYTDValue = ($result->LastPrice) - ($result->ChangeYTD);
          
          if($changeYTDValue < 0){
            echo "<tr><th>Change YTD</th><td>(". round(($changeYTDValue), 2). ")";
            echo "<img src=\"http://cs-server.usc.edu:45678/hw/hw6/images/Red_Arrow_Down.png\" height='10px' width='10px'></img>";
          }elseif($changeYTDValue > 0){
            echo "<tr><th>Change YTD</th><td>". round(($changeYTDValue), 2);
            echo "<img src=\"http://cs-server.usc.edu:45678/hw/hw6/images/Green_Arrow_Up.png\" height='10px' width='10px'></img>";
          }else{
            echo "<tr><th>Change YTD</th><td>". round(($changeYTDValue), 2);
          }
          echo "</td></tr>";

          echo "<tr><th>Change Percent YTD</th><td>". round(($result->ChangePercentYTD), 2) . "%";
          if(($result->ChangePercentYTD) < 0){
            echo "<img src=\"http://cs-server.usc.edu:45678/hw/hw6/images/Red_Arrow_Down.png\" height='10px' width='10px'></img>";
          }
          if(($result->ChangePercentYTD) > 0){
            echo "<img src=\"http://cs-server.usc.edu:45678/hw/hw6/images/Green_Arrow_Up.png\" height='10px' width='10px'></img>";
          }
          echo "</td></tr>";

          echo "<tr><th>High</th><td>".$result->High . "</td></tr>";

          echo "<tr><th>Low</th><td>".$result->Low . "</td></tr>";

          echo "<tr><th>Open</th><td>".$result->Open . "</td></tr></table>";
        }
        else{
          echo "<table class='table2'><tr><td style='text-align: center;'>There is no stock information available</td></tr><table>";
        }
      }
    ?>
    <noscript>
  </body>
</html>