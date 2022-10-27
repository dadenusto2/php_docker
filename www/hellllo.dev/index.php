<output id="result"></output>
<script type="text/javascript">
    function request(q){
      setTimeout(() => { }, 100);
      const request = new XMLHttpRequest();
      const url = 'http://10.101.16.200/search?q="'+q+'"&format=json&limit=1&addressdetails=1';
      console.log(url);
      request.open("GET", url, true);
      request.send();
      request.onload = (e) => {
          var arr = JSON.parse(request.response);
          arr.forEach(function(elementObject){
              var keys = Object.keys(elementObject);
              keys.forEach(function(key){
                if(key === "address"){
                  var adress = Object.keys(elementObject[key]);
                  adress.forEach(function(adres){
                    document.getElementById("result").innerHTML += "  " + adres + ":"+elementObject[key][adres] + "<br>";
                  });
                }
                else{
                  document.getElementById("result").innerHTML += key + ":"+elementObject[key] + "<br>";
                }
              })
          });
          document.getElementById("result").innerHTML += "<br>";
      }
}
</script>

<?php
$mysqli = new mysqli('10.101.16.19:3306', 'hizhniyeg', 'KLJf$4d2s', 'sitemanager');
$result = $mysqli->query("select DISTINCT date_format(date_discon, '%d.%m.%Y') as date_discon, city, town, company_name, way, Replace(consumer_name, ';', concat(';', '<br>')) as consumer_name, Replace(adress, ';', concat(';', '<br>')) as adress,time_discon, reason from disconnections where city = 'Краснодар' order by date_format(date_discon,'%Y-%m-%d'), time_discon, adress limit 5");
  foreach ($result as $row1)
    {
        $town = "";
        if(strcmp($row1[town], "") == 0){
          $town =  $row1[city];
        }
        else{
          $town = $row1[town];
        }
        $adress1 = str_replace('пер',";пер",$row1[adress]);
        $adress1 = str_replace('пер ',";пер. ",$adress1);
        $adress1 = str_replace('пр',";пр",$adress1);
        $adress1 = str_replace('ул',";ул",$adress1);
        $adress1 = str_replace('туп',";$town туп",$adress1);
        $adreses = explode(';', mb_substr($adress1, 1));
        $adresses_with_table[$j]= $adreses;
        $j++;
      }
      $adreses = explode(';', mb_substr( $allAdress, 1));
      $new_adress_with_tab = array();
      $j=1;
      foreach($adresses_with_table as $adress_with_table){
        $new_adress = array();
        foreach($adress_with_table as $adres){
          $nums = array_values(explode(', ', $adres));
          $street = $nums[0];
          unset($nums[0]);
          $new_nums = array();
          foreach($nums as $num){
            $pos = strpos($num, "-");
            if($pos !== false){
              $nums_sep = array_values(explode('-', $num));
              for($i = (int)$nums_sep[0]; $i<= (int)$nums_sep[1]; $i++){
                array_push($new_nums, "$i");
              }
            }
            else{
              array_push($new_nums, str_replace(",","","$num"));
            }
          }
          $new_adress[$street] = $new_nums;
        }
        $new_adress_with_tab[$j] = $new_adress;
        $j++;
      }
      $j=1;
      $new_adresses = array();
      foreach($new_adress_with_tab as $new_adress){
        $streets = array_keys($new_adress);
        $i = 0;
        $row_adress=array();
        foreach($streets as $street){
          foreach($new_adress[$street] as $adres){
            array_push($row_adress, strtolower("$street $adres"));
            echo '<script type="text/javascript">',
            'request("'. strtolower("$street $adres") .'");',
            '</script>';
            $i++;
          }
        }
        $new_adresses[$j]=$row_adress;
        $j++;
      }
?>
