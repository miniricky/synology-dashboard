<?php
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST['info'];
    $sid = $_COOKIE['sid'];
    $domain = $_COOKIE['domain'];

    if ($data == 'scraping-pagination') {
      $url = 'https://www3.animeflv.net/browse';
      $pagination = scrapingPagination($url);
      jsonEncode('pagination', $pagination);
    }

    if ($data == 'scraping-flv') {
      $page = (int) $_POST['page'];
      $url = 'https://www3.animeflv.net/browse?page=' . $page;
      $scraping = scrapingFLV($url);
      jsonEncode('scraping', $scraping);
    }

    if ($data == 'scraping-episode') {
      $url = $_POST['url'];
      $episode = scrapingEpisode($url);
      jsonEncode('episode', $episode);
    }

    if ($data == 'verify-episode') {
      $title = $_POST['title'];
      $episode = $_POST['episode'];

      $verify = verifyEpisode($sid, $domain, $title, $episode);
      jsonEncode('verify', $verify);
    }

    if ($data == 'download-station') {
      $url = $_POST['url'];
      $title = $_POST['title'];
      $episode = $_POST['episode'];
      $download = verifyFolder($title, $url, $episode, $sid, $domain);
      jsonEncode('download', $download);
    }

    if ($data == 'id-download') {
      $name = $_POST['name'];
      $id = idDownload($sid, $name, $domain);
      jsonEncode('id', $id, $domain);
    }

    if ($data == 'info-download') {
      $id = $_POST['id'];
      $info = infoDownload($sid, $id, $domain);
      jsonEncode('info', $info);
    }

    if ($data == 'rename-file') {
      $title = $_POST['title'];
      $name = $_POST['name'];
      $episode = $_POST['episode'];
      $rename = renameFile($sid, $domain, $title, $name, $episode);

      header('Content-Type: application/json');
      json_encode('rename', $rename);
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
    * Function for scraping Pagination.
    */
  function scrapingPagination($url) {
    $xpath = curl($url);
    $pagination_query = "//ul[contains(@class, 'pagination')]/li[last()-1]/a/@href";
    $pages = $xpath->query($pagination_query);
    $pages = ($pages->length > 0) ? (int) preg_replace('/\D/', '', $pages->item(0)->nodeValue) : 1;

    if ($pages) {
      $pages = [
        'pages' => $pages,
        'status' => 'true'
      ];
    } else {
      $pages = [
        'status' => 'false',
        'error' => 'Error trying to get pages'
      ];
    }

    return $pages;
  }

  /*
    * Function for scraping FLV.
    */
  function scrapingFLV($url) {
    $xpath = curl($url);
    $articles = $xpath->query("//article");

    foreach ($articles as $article) {
      $titleNode = $xpath->query(".//div[contains(@class, 'Title')]", $article);
      $typeNode = $xpath->query(".//p/span[contains(@class, 'Type')]", $article);
      $linkNode = $xpath->query(".//a[contains(@class, 'Vrnmlk')]/@href", $article);
      $urlNode = $xpath->query(".//div/figure/img/@src", $article);

      if ($titleNode->length > 0 && $typeNode->length > 0 && $linkNode->length > 0 && $urlNode->length > 0) {
        $title = trim($titleNode->item(0)->nodeValue);
        $type = trim($typeNode->item(0)->nodeValue);
        $link = trim($linkNode->item(0)->nodeValue);
        $url = trim($urlNode->item(0)->nodeValue);

        $scraping[] = [
          'title' => $title,
          'type' => $type,
          'link' => $link,
          'url' => $url,
          'status' => 'true',
          'error' => 'Getting system info successfully.'
        ];
      }
      else{
        $scraping = [
          'error' => 'Error trying to get system information.'
        ];
      }
    }

    return $scraping;
  }

  /*
    * Function for scraping episodes.
    */
  function scrapingEpisode($url) {
    $xpath = curl($url);
    $episode = [];

    $articles = $xpath->query("//a[contains(@class, 'Button') and contains(@class, 'Sm') and contains(@class, 'fa-download')]/@href");
    if ($articles->length > 0) {
      foreach ($articles as $article) {
        $link = $article->nodeValue;

        if (strpos($link, 'https://streamtape.com/') !== false) {
          $episode = [
            'link' => $link,
            'status' => 'true',
            'error' => 'Getting episode successfully.'
          ];
          break;
        }
        else{
          $episode = [
            'error' => 'Error getting episode.'
          ];
        }
      }
    }

    return $episode;
  }

  /*
    * Function for verify if episode exist in synology folder.
    */
  function verifyEpisode($sid, $domain, $title, $episode) {
    $title = str_replace(':', ' -', $title);
    $path = '/anime/' . $title;
    $pathEncode = rawurlencode($path);
    $name = $title . ' - ' . $episode . '.mp4';

    $list_url = "http://$domain:5000/webapi/entry.cgi?api=SYNO.FileStation.List&version=2&method=list&folder_path=$pathEncode&_sid=$sid";
    $list_response = file_get_contents($list_url);
    $list_data = json_decode($list_response, true);

    if ($list_data['success']) {
      $status = false;
      foreach($list_data['data']['files'] as $data) {
        if ($data['name'] === $name) {
          $status = true;
          break;
        }
      }

      if ($status) {
        $verify = [
          'status' => 'true',
          'error' => 'has already been downloaded to file station'
        ];
      }
      else {
        $verify = [
          'status' => 'false',
          'error' => 'This episode is not in file station'
        ];
      }
    }
    else {
      $verify = [
        'status' => 'false',
        'error' => 'There is no anime on file station'
      ];
    }

    return $verify;
  }

  /*
    * Function for requesting HTTP.
    */
  function curl($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    $response = curl_exec($ch);
    curl_close($ch);

    $dom = new DOMDocument();
    @$dom->loadHTML($response);
    $xpath = new DOMXPath($dom);

    return $xpath;
  }

  /*
    * Function for verify if exist folder.
    */
  function verifyFolder($title, $url, $episode, $sid, $domain) {
    $title = str_replace(':', ' -', $title);
    $path = 'anime/' . $title;
    $title = rawurlencode($title);

    $create_folder_url = "http://$domain:5000/webapi/entry.cgi?api=SYNO.FileStation.CreateFolder&version=2&method=create&folder_path=/anime&name=$title&_sid=$sid";
    $create_folder_response = file_get_contents($create_folder_url);
    $create_folder_data = json_decode($create_folder_response, true);
    $download = [];

    if ($create_folder_data['success']) {
      $download = initDownload($sid, $url, rawurlencode($path), $episode);
    } else {
      $download = [
        'error' => 'Error creating folder: ' . $create_folder_data['error']['code']
      ];
    }

    return $download;
  }

  /*
    * Function for initilizing download.
    */
  function initDownload($sid, $url, $pathEncode, $episode) {
    $download_url = "http://192.168.101.11:5000/webapi/DownloadStation/task.cgi?api=SYNO.DownloadStation.Task&version=1&method=create&uri=$url&destination=$pathEncode&_sid=$sid";
    $download_response = file_get_contents($download_url);
    $download_data = json_decode($download_response, true);
    $download = [];

    if ($download_data['success']) {
      $download = [
        'status' => 'true',
        'error' => $episode . ' download will start soon.'
      ];
    } else {
      $download = [
        'error' => $episode . ' error starting download.'
      ];
    }

    return $download;
  }

  /*
    * Function get download id.
    */
  function idDownload($sid, $name, $domain) {
    $list_url = "http://$domain:5000/webapi/DownloadStation/task.cgi?api=SYNO.DownloadStation.Task&version=1&method=list&_sid=$sid";
    $list_response = file_get_contents($list_url);
    $list_data = json_decode($list_response, true);
    $id = [];

    if ($list_data['success']) {
      if (!empty($list_data['data']['tasks'])) {
        foreach ($list_data['data']['tasks'] as $task) {
          if ($task['status'] == 'waiting') {
            $id = [
              'download' => $task['status'],
              'status' => 'true',
              'error' => 'Trying to get ID.'
            ];
          }

          if ($task['status'] == 'downloading') {
            if ($task['title'] == $name) {
              $id = [
                'id' => $task['id'],
                'download' => $task['status'],
                'status' => 'true',
                'error' => 'Trying to get ID.'
              ];
            }
          }
        }
      }
      else{
        $id = [
          'error' => 'No downloads found.'
        ];
      }
    } else {
      $id = [
        'error' => 'Error trying to get downloads'
      ];
    }

    return $id;
  }

  /*
    * Function for get info download.
    */
  function infoDownload($sid, $id, $domain) {
    $list_url = "http://$domain:5000/webapi/DownloadStation/task.cgi?api=SYNO.DownloadStation.Task&version=1&method=getinfo&id=$id&additional=detail,transfer&_sid=$sid";
    $list_response = file_get_contents($list_url);
    $list_data = json_decode($list_response, true);

    if ($list_data['success']) {
      $task_info = $list_data['data']['tasks'][0];

      $size = $task_info['size'] / (1024 * 1024);
      $download = $task_info['additional']['transfer']['size_downloaded'] / (1024 * 1024);
      $speed = $task_info['additional']['transfer']['speed_download'] / (1024 * 1024);

      $info = [
        'size' => number_format($size, 2),
        'download' => number_format($download, 2),
        'speed' => number_format($speed, 2),
        'status' => $task_info['status']
      ];
    }
    else{
      $info = [
        'error' => 'Error trying to get info download.'
      ];
    }

    return $info;
  }

  function renameFile($sid, $domain, $title, $name, $episode){
    $title = str_replace(':', ' -', $title);
    $path = '/anime/' . $title . '/' . $name;
    $pathEncode = str_replace('%2F', '/', rawurlencode($path));
    $episode = $title . ' - ' . $episode  . '.mp4';
    $episodeEncode = rawurlencode($episode);

    $rename_url = "http://$domain:5000/webapi/entry.cgi?api=SYNO.FileStation.Rename&version=2&method=rename&path=$pathEncode&name=$episodeEncode&_sid=$sid";
    $rename_response = file_get_contents($rename_url);
    $rename_data = json_decode($rename_response, true);

    $rename = [];
    if ($rename_data['success']) {
      $rename = [
        'status' => 'true',
        'error' => 'Name updated successfully.'
      ];
    }
    else{
      $rename = [
        'error' => 'Error trying to rename file.'
      ];
    }

    return $rename;
  }
?>