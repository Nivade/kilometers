<?php
  include 'database.php';
  $pdo = Database::connect();
  $pin = $_POST['pin'];
  $sqlGetCurrentUser = "SELECT * FROM medewerker WHERE code = '$pin'";
  $sqlGetAllLocations = 'SELECT * FROM locaties';
  $sqlGetCurrentUserClients = "SELECT c.client, c.postcode, c.nummer FROM client_koppeling c WHERE c.medewerker IN ( SELECT m.naam FROM medewerker m WHERE m.code = '$pin' )";
?>


<!doctype html>
<html>
  <head>
    <title>Kilometers</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
    <link rel="stylesheet" href="css/bootstrap-select.min.css">
    <link rel="stylesheet" href="css/style.css" />
  </head>
  <body>
    <div class="km-page">
      <div class="option-panel">
        <form class="option-panel-form">
          <h2>Kilometers</h2>
          <p>
            Stel een vertrekpunt en een bestemming in om de kilometer afstand te bepalen.
          </p>
            <select id="start" class="selectpicker show-tick" data-live-search="true" title="Vertrekpunt" data-width="100%">
              <optgroup label="Uzelf">
                <?php
                $pdo = Database::connect();
                foreach ($pdo->query($sqlGetCurrentUser) as $row) {
                  echo '<option data-tokens="Thuis ik home" value="'.$row['postcode'].'">' .Thuis. '</option>';
                }
                ?>
              </optgroup>
              <optgroup label="Rac">
                <?php
                foreach ($pdo->query($sqlGetAllLocations) as $row) {
                  echo '<option data-tokens="'.$row['display_name'].'" value="'.$row['zip'].'">' .$row['display_name']. '</option>';
                }
                ?>
              </optgroup>
              <optgroup label="Clienten">
                <?php
                foreach ($pdo->query($sqlGetCurrentUserClients) as $row) {
                  echo '<option data-tokens="'.$row['client'].''.$row['postcode'].'" value="'.$row['postcode'].'">' .$row['client']. '</option>';
                }
                ?>
              </optgroup>
            </select>
            <select id="end" class="selectpicker show-tick" data-live-search="true" title="Bestemming" data-width="100%">
              <optgroup label="Uzelf">
                <?php
                $pdo = Database::connect();
                foreach ($pdo->query($sqlGetCurrentUser) as $row) {
                  echo '<option data-tokens="Thuis ik home" value="'.$row['postcode'].'">' .Thuis. '</option>';
                }
                ?>
              </optgroup>
              <optgroup label="Rac">
                <?php
                foreach ($pdo->query($sqlGetAllLocations) as $row) {
                  echo '<option data-tokens="'.$row['display_name'].'" value="'.$row['zip'].'">' .$row['display_name']. '</option>';
                }
                ?>
              </optgroup>
              <optgroup label="Clienten">
                <?php
                foreach ($pdo->query($sqlGetCurrentUserClients) as $row) {
                  echo '<option data-tokens="'.$row['client'].''.$row['postcode'].'" value="'.$row['postcode'].'">' .$row['client']. '</option>';
                }
                ?>
              </optgroup>
            </select>

            <div class="result">
              <i id="kilometers"></i>
            </div>
        </form>
      </div>

    </div>
    <div id="map">

    </div>

    <script>



      function initMap() {
        var directionsService = new google.maps.DirectionsService;
        var directionsDisplay = new google.maps.DirectionsRenderer;
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 7,
          center: {lat: 41.85, lng: -87.65}
        });
        directionsDisplay.setMap(map);

        var onChangeHandler = function() {
          try {
            calculateAndDisplayRoute(directionsService, directionsDisplay);
          }
          catch(e) {
            return;
          }
        };
        document.getElementById('start').addEventListener('change', onChangeHandler);
        document.getElementById('end').addEventListener('change', onChangeHandler);
        document.getElementById('kilometers').addEventListener('change', onChangeHandler);
      }

      function calculateAndDisplayRoute(directionsService, directionsDisplay) {
        directionsService.route({
          origin: document.getElementById('start').value,
          destination: document.getElementById('end').value,
          travelMode: 'DRIVING'
        }, function(response, status) {
          if (status === 'OK') {
            directionsDisplay.setDirections(response);
            document.getElementById('kilometers').innerHTML = Math.ceil(response.routes[0].legs[0].distance.value / 1000) + " km";

          } else {

          }
        });
      }
    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBZbVzR16KMxrhyRIWrRyzeCk1WL1SH11s&callback=initMap">
    </script>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
    <script src="js/bootstrap-select.min.js"></script>

    <script>
      $(document).ready( function () {
        $('.selectpicker').selectpicker();
      });
    </script>
  </body>
</html>
