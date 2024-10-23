<?php
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST['info'];
    $sid = $_COOKIE['sid'];
    $domain = $_COOKIE['domain'];
    $packages = packagesInfo($sid, $domain);
    jsonEncode('packages', $packages);
  }

  /*
  * Function for init jsonEncode.
  */
  function jsonEncode($key, $value) {
    header('Content-Type: application/json');
    echo json_encode([
      $key => $value,
    ]);
  }

  /*
  * Function for get packages info.
  */
  function packagesInfo($sid, $domain) {
    $package_url = "http://$domain:5000/webapi/entry.cgi?api=SYNO.Core.Package&version=1&method=list&_sid=$sid";
    $package_response = file_get_contents($package_url);
    $package_data = json_decode($package_response, true);

    $packages = [];
    if ($package_data && isset($package_data['data']['packages'])) {
      foreach ($package_data['data']['packages'] as $package) {
        $packages[] = [
          'id' => $package['id'],
          'name' => $package['name'],
          'version' => $package['version']
        ];
      }
    } else {
      $packages = [
        'error' => 'Error retrieving packages information.'
      ];
    }

    return $packages;
  }
?>