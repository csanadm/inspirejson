<?php
  include("printInspirePublist.php");
  $url = "https://inspirehep.net/api/literature?q=".rawurlencode("find a Steven.Weinberg.1")."&size=50";
  $Nmaxauth = 3;
  printInspirePublist($url,$Nmaxauth);
 ?>
