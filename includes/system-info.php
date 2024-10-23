<?php
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST['info'];
    $sid = $_COOKIE['sid'];
    $domain = $_COOKIE['domain'];

    if ($data == 'system-info') {
      $system_info = systemInfo($sid, $domain);
      jsonEncode('system', $system_info);
    }

    if ($data == 'ram-info') {
      $ram_info = ramInfo($sid, $domain);
      jsonEncode('ram', $ram_info);
    }

    if ($data == 'hdd-info') {
      $hdd_info = hddInfo($sid, $domain);
      jsonEncode('hdd', $hdd_info);
    }
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
  * Function for get system info.
  */
  function systemInfo($sid, $domain) {
    $system_url = "http://$domain:5000/webapi/entry.cgi?api=SYNO.Core.System&version=1&method=info&_sid=$sid";
    $system_response = file_get_contents($system_url);
    $system_data = json_decode($system_response, true);
    $system_info = [];

    if ($system_data && isset($system_data['data'])) {
      $system_info = [
        'model' => $system_data['data']['model'],
        'processor' => $system_data['data']['cpu_vendor'],
        'cores' => $system_data['data']['cpu_cores'],
        'firmware' => $system_data['data']['firmware_ver'],
        'status' => 'true',
        'error' => 'System information obtained.'
      ];
    } else {
      $system_info[] = [
        'Error' => 'Error getting system information.'
      ];
    }

    return $system_info;
  }

  /*
  * Function for get ram info.
  */
  function ramInfo($sid, $domain) {
    $ram_url = "http://$domain:5000/webapi/entry.cgi?api=SYNO.Core.System.Utilization&version=1&method=get&_sid=$sid";
    $ram_reponse = file_get_contents($ram_url);
    $ram_data = json_decode($ram_reponse, true);
    $ram_info = [];

    // Mostrar la información de RAM usada
    if ($ram_data && isset($ram_data['data']['memory'])) {
      $ram_info = [
        'ramTotal' => $ram_data['data']['memory']['memory_size'] / (1024 * 1024),
        'ramUsed' => $ramUsed = $ram_data['data']['memory']['real_usage'],
        'status' => 'true',
        'error' => 'Ram information obtained.'
      ];
    } else {
      $ram_info = [
        'error' => 'Error getting RAM usage information.'
      ];
    }

    return $ram_info;
  }

  /*
  * Function for get hdd info.
  */
  function hddInfo($sid, $domain) {
    $storage_url = "http://$domain:5000/webapi/entry.cgi?api=SYNO.Storage.CGI.Storage&version=1&method=load_info&_sid=$sid";
    $storage_response = file_get_contents($storage_url);
    $storage_data = json_decode($storage_response, true);
    $hdd_info = [];

    if ($storage_data && isset($storage_data['data'])) {
      foreach ($storage_data['data']['volumes'] as $volume) {
        $total = ($volume['size']['total'] / (1024 ** 4));
        $used = ($volume['size']['used'] / (1024 ** 4));

        $hdd_info = [
          'hddTotal' => number_format($total, 2),
          'hddUsed' => number_format($used, 2),
          'status' => 'true',
          'error' => 'HDD information obtained.'
        ];
      }
    } else {
      $hdd_info = [
        'error' => 'Error getting hard drive information.'
      ];
    }

    return $hdd_info;
  }
?>