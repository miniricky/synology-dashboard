<?php
// Autenticación
// $auth_url = "http://192.168.101.11:5000/webapi/entry.cgi?api=SYNO.API.Auth&version=6&method=login&account=miniricky&passwd=Etniesskate90@&session=FileStation&format=cookie";

// $auth_response = file_get_contents($auth_url);
// $auth_data = json_decode($auth_response, true);

// if ($auth_data['success']) {
//   // Guardar la cookie de la autenticación
//   $sid = $auth_data['data']['sid'];

//   // Obtener información del sistema
//   $system_url = "http://192.168.101.11:5000/webapi/entry.cgi?api=SYNO.Core.System&version=1&method=info&_sid=$sid";

//   // Configurar el contexto HTTP para pasar la cookie
//   $opts = [
//       "http" => [
//           "header" => "Cookie: id=$sid\r\n"
//       ]
//   ];
//   $context = stream_context_create($opts);

//   // Hacer la solicitud con la cookie
//   $system_response = file_get_contents($system_url, false, $context);
//   $system_data = json_decode($system_response, true);

  // Imprimir la respuesta completa para verificar la estructura
  // echo "<pre>";
  // print_r($system_data);
  // echo "</pre>";

  // if ($system_data && isset($system_data['data'])) {
  //     // Mostrar la información
  //     echo "Modelo: " . $system_data['data']['model'] . "<br>";
  //     echo "Procesador: " . $system_data['data']['cpu_vendor'] . "<br>";
  //     echo "Nucleos: " . $system_data['data']['cpu_cores'] . "<br>";
  //     //echo "RAM: " . ($system_data['data']['ram_size'] / 1024 ) . "<br>";
  //     echo "Firmware: " . $system_data['data']['firmware_ver'] . "<br>";
  //     echo "Time: " . $system_data['data']['time'] . "<br>";
  // } else {
  //     echo "Error al obtener la información del sistema o la estructura no contiene los campos esperados.";
  // }

  //========================================================================================================================================================================================= RAM

  // Obtener uso de recursos (incluyendo la RAM usada)
  // $utilization_url = "http://192.168.101.11:5000/webapi/entry.cgi?api=SYNO.Core.System.Utilization&version=1&method=get&_sid=$sid";
  // $utilization_response = file_get_contents($utilization_url, false, $context);
  // $utilization_data = json_decode($utilization_response, true);
  // // echo "<pre>";
  // // print_r($utilization_data['data']['memory']);
  // // echo "</pre>";

  // // Mostrar la información de RAM usada
  // if ($utilization_data && isset($utilization_data['data']['memory'])) {
  //   // Calcular la RAM usada
  //   $ram_usada = $utilization_data['data']['memory']['memory_size'] - $utilization_data['data']['memory']['total_swap'];
  //   $ram_usada_gb = $ram_usada / (1024 * 1024); // Convertir a GB
  //   $total_ram_gb = $utilization_data['data']['memory']['memory_size'] / (1024 * 1024); // Convertir a GB

  //   // Mostrar el resultado
  //   echo "RAM total: " . number_format($total_ram_gb, 2) . " GB<br>";
  //   echo "RAM usada: " . $utilization_data['data']['memory']['real_usage'] . " %<br>";
  // } else {
  //     echo "Error al obtener la información de uso de RAM.";
  // }

  //========================================================================================================================================================================================= Disco Duro

  // Obtener información del almacenamiento
  // $storage_url = "http://192.168.101.11:5000/webapi/entry.cgi?api=SYNO.Storage.CGI.Storage&version=1&method=load_info&_sid=$sid";
  // $storage_response = file_get_contents($storage_url, false, $context);
  // $storage_data = json_decode($storage_response, true);

  // // Mostrar la información del almacenamiento
  // if ($storage_data && isset($storage_data['data'])) {
  //   foreach ($storage_data['data']['volumes'] as $volume) {
  //     // echo "<pre>";
  //     // print_r($volume);
  //     // echo "</pre>";

  //     $total = ($volume['size']['total'] / (1024 ** 4));
  //     $used = ($volume['size']['used'] / (1024 ** 4));

  //     echo "Volumen: " . $volume['id'] . "<br>";
  //     echo "Tamaño total: " . number_format($total, 2) . " TB<br>";
  //     echo "Tamaño usado: " . number_format($used, 2) . " TB<br>";
  //   }

  //   //===================================================================================================================================================================================== Packages
  //   $package_url = "http://192.168.101.11:5000/webapi/entry.cgi?api=SYNO.Core.Package&version=1&method=list&_sid=$sid";

  //   // Hacer la solicitud con la cookie
  //   $package_response = file_get_contents($package_url, false, $context);
  //   $package_data = json_decode($package_response, true);

  //   if ($package_data && isset($package_data['data']['packages'])) {
  //     // Guardar la lista de paquetes instalados
  //     foreach ($package_data['data']['packages'] as $package) {
  //       echo $package['id'];
  //       echo $package['name'];
  //       echo $package['timestamp'];
  //       echo $package['version'];
  //     }
  //   } else {
  //     $error = 'Error retrieving packages information.';
  //   }
  // } else {
  //   echo "Error al obtener la información de los discos duros.";
  // }

  //===================================================================================================================================================================================== Download Station

  // $download_url = "http://192.168.101.11:5000/webapi/DownloadStation/task.cgi?api=SYNO.DownloadStation.Task&version=1&method=create&uri=https://streamtape.com/get_video?id=KX1LpMPQoLF0QJ6&expires=1728946618&ip=FRONKRWPDy9XKxR&token=qetPxVWy_RUO&stream=1&_sid=$sid";
  // $download_response = file_get_contents($download_url);
  // $download_data = json_decode($download_response, true);

  // if ($download_data['success']) {
  //     echo "Descarga iniciada exitosamente.";
  // } else {
  //     echo "Error al iniciar la descarga.";
  // }

  //===================================================================================================================================================================================== Download Station
  // $url = 'https://www3.animeflv.net/browse';

  // // Inicializar cURL
  // $ch = curl_init();
  // curl_setopt($ch, CURLOPT_URL, $url);
  // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

  // // Obtener el contenido de la página
  // $response = curl_exec($ch);
  // curl_close($ch);

  // // Crear un nuevo DOMDocument
  // $dom = new DOMDocument();

  // // Cargar la página HTML en DOMDocument, silenciar errores con @
  // @$dom->loadHTML($response);

  // // Crear un DOMXPath para hacer consultas XPath
  // $xpath = new DOMXPath($dom);

  // // Buscar todos los divs con la clase 'Description'
  // $descriptions = $xpath->query("//div[contains(@class, 'Description')]");

  // // Iterar sobre cada descripción
  // foreach ($descriptions as $description) {
  //   // Buscar el título dentro del div.Description
  //   $titleNode = $xpath->query(".//div[contains(@class, 'Title')]", $description);
  //   // Buscar el tipo dentro del span.Type
  //   $typeNode = $xpath->query(".//p/span[contains(@class, 'Type')]", $description);
  //   // Buscar el tipo dentro del a.Vrnmlk
  //   $linkNode = $xpath->query(".//a[contains(@class, 'Vrnmlk')]/@href", $description);
  //   // Buscar el tipo dentro del article
  //   $imgNode = $xpath->query(".//figure/img/@src");

  //   // Verificar si el título y el tipo existen
  //   if ($titleNode->length > 0 && $typeNode->length > 0 && $linkNode->length > 0 && $imgNode->length > 0) {
  //     $title = trim($titleNode->item(0)->nodeValue);
  //     $type = trim($typeNode->item(0)->nodeValue);
  //     $link = trim($linkNode->item(0)->nodeValue);
  //     $img = trim($imgNode->item(0)->nodeValue);

  //     Mostrar la información obtenida
  //     echo "Título: $title<br>";
  //     echo "Tipo: $type<br><br>";
  //     echo "Link: $link<br><br>";
  //     echo "Image: $img<br><br>";
  //   }
  // }

  //===================================================================================================================================================================================== Download Station
  // $list_url = "http://192.168.101.11:5000/webapi/DownloadStation/task.cgi?api=SYNO.DownloadStation.Task&version=1&method=list&_sid=$sid";

  // $list_response = file_get_contents($list_url);
  // $list_data = json_decode($list_response, true);
  // $id = '';
  // $name = '4076_2.mp4';

  // echo '<pre>';
  // print_r($list_data);
  // echo '<pre>';

  // if ($list_data['success']) {
  //   if (!empty($list_data['data']['tasks'])) {
  //     foreach ($list_data['data']['tasks'] as $task) {
  //       if ($task['status'] == 'waiting') {

  //       }
  //       // if ($task['title'] == $name) {
  //       //   $id = $task['id'];
  //       // }
  //     }
  //   }
  //   else{
  //     echo 'No se encontraron descargas';
  //   }

  //   print_r($id);
  // }

  // $id = 'dbid_195';
  // $list_url = "http://192.168.101.11:5000/webapi/DownloadStation/task.cgi?api=SYNO.DownloadStation.Task&version=1&method=getinfo&id=". $id ."&additional=detail,transfer&_sid=" . $sid;
  // $info = [];

  // $list_response = file_get_contents($list_url);
  // $list_data = json_decode($list_response, true);

  // echo '<pre>';
  // print_r($list_data);
  // echo '<pre>';

  // if ($list_data['success']) {
  //   $task_info = $list_data['data']['tasks'][0];

    // $info = [
    //   $size = $task_info['size'] / (1024 * 1024),
    //   $download = $task_info['additional']['transfer']['size_downloaded'] / (1024 * 1024),
    //   $speed = $task_info['additional']['transfer']['speed_download'],
    // ];

    // $size = $task_info['size'] / (1024 * 1024);
    // $download = $task_info['additional']['transfer']['size_downloaded'] / (1024 * 1024);
    // $speed = $task_info['additional']['transfer']['speed_download'] / (1024 * 1024);

    // echo '<pre>';
    // print_r(number_format($size, 2));
    // echo '<pre>';

    // echo '<pre>';
    // print_r(number_format($download, 2));
    // echo '<pre>';

    // echo '<pre>';
    // print_r(number_format($speed, 2));
    // echo '<pre>';

  // $title = 'Arifureta Shokugyou de Sekai Saikyou Season 3';
  // $name = '3861_15.mp4';
  // $episode = 'Episodio 1.mp4';
  // $sid = $_COOKIE['sid'];
  // $dominio = '192.168.101.11';

  // $path = '/anime/' . $title . '/' . $name;
  // $pathEncode = str_replace('%2F', '/', rawurlencode($path));
  // $episode = rawurlencode($episode);

  // $list_url = "http://$dominio:5000/webapi/entry.cgi?api=SYNO.FileStation.Rename&version=2&method=rename&path=$pathEncode&name=$episode&_sid=$sid";
  // $rename_response = file_get_contents($list_url);
  // $rename_data = json_decode($rename_response, true);

  // echo '<pre>';
  // print_r($rename_data);
  // echo '<pre>';

  // foreach($rename_data['data']['files'] as $data) {
  //   if ($data['path'] == $path) {
  //     echo 'puto';
  //   }
  // }

  // $sid = $_COOKIE['sid'];
  // $domain = $_COOKIE['domain'];
  // $title = 'Blue Lock: Episode Nagi';
  // $title = rawurlencode($title);

  // $create_folder_url = "http://$domain:5000/webapi/entry.cgi?api=SYNO.FileStation.CreateFolder&version=2&method=create&folder_path=/anime&name=$title&_sid=$sid";
  // $create_folder_response = file_get_contents($create_folder_url);
  // $create_folder_data = json_decode($create_folder_response, true);

  // if ($create_folder_data['success']) {
  //   echo 'bien';
  // } else {
  //   echo 'mal';
  // }

// } else {
//   echo "Error en la autenticación: " . $auth_data['error']['code'];
// }

// $title = 'Arifureta Shokugyou de Sekai Saikyou Season 3';
// $domain = '192.168.101.11';
// $username = 'miniricky'; // Reemplaza con tu usuario
// $password = 'Etniesskate90@'; // Reemplaza con tu contraseña
// $sid = $_COOKIE['sid']; // Intenta obtener el SID de la cookie
// $path = '/anime/' . $title;
// $pathEncode = rawurlencode($path);
// $episode = 'Episodio 1.mp4';

// $name = $title . ' - ' . $episode;

// $list_url = "http://$domain:5000/webapi/entry.cgi?api=SYNO.FileStation.List&version=2&method=list&folder_path=$pathEncode&_sid=$sid";
// $list_response = file_get_contents($list_url);
// $list_data = json_decode($list_response, true);

// if ($list_data['success']) {
//   foreach($list_data['data']['files'] as $data) {
//     echo '<pre>';
//     print_r($data['name']);
//     echo '<pre>';

//     if ($data['name'] === $name) {
//       echo 'puto';
//       break;
//     }
//   }
// } else {
//   echo "Error inesperado al listar el contenido del directorio.";
// }
?>