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
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="css/bootstrap-select.min.css">
    <link rel="stylesheet" href="css/style.css" />
  </head>
  <body>
    <div class="kilometer-panel-container">
      <div class="container">
        <div class="row">
          <h2>Kilometers</h2>
          <p>Vul je begin en eindbestemming in om de kilometer afstand te berekenen.</p>
        </div>
        <div class="row">
          <select id="start" class="selectpicker col-md-12" data-live-search="true" data-live-search-placeholder="Zoeken..." title="Vertrekpunt">
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
        </div>
        <div class="row">
          <select id="end" class="selectpicker col-md-12" data-live-search="true" data-live-search-placeholder="Zoeken..." title="Bestemming">
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
        </div>
        <div class="row">
          <div>
            <p id="kilometers"></p>
          </div>
        </div>
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
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCe7gCMUs37XQzvUk_jpk7JS6hWXmDyuXU&callback=initMap">
    </script>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script src="js/bootstrap-select.min.js"></script>

    <script>
      $(document).ready( function () {
        $('.selectpicker').selectpicker();
      });

      $('#start').on('show.bs.select', function (e) {
        $('.dropdown-menu .inner').css('max-height', $('.dropdown-menu .inner').height() + 300);
      });
      $('#end').on('show.bs.select', function (e) {
        $('.dropdown-menu .inner').css('max-height', $('.dropdown-menu .inner').height() + 300);
      });

    </script>
  </body>
</html>
