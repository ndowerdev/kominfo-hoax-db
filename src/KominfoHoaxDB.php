<?php

namespace KominfoHoaxDB;

include  __DIR__ . "/../vendor/autoload.php";

use DiDom\Document;
use DiDom\Query;


class KominfoHoaxDB
{
  public function __construct()
  {
  }

  public static function getDetails($id, $type = 'json')
  {
    if (!isset($id) || $id === '') {
      return 'Please provide some ID';
    } else {
      $baseUrl = "https://kominfo.go.id/content/detail/$id/$id/0/laporan_isu_hoaks";
      $html = self::kominfoCurl($baseUrl);
      $dom  = new Document($html);
      $dom->first('div.a2a_kit')->remove();
      $dom->first('script')->remove();
      if ($dom->has('div.blog-entry')) {
        $contentArea = $dom->find('div.blog-entry')[0];

        $contentData = [
          'title' => ($contentArea->has('h1.title') ? $contentArea->find('h1.title')[0]->text() : 'No title found'),
          'date' => ($contentArea->has('div.date') ? str_replace(' ', '-', $contentArea->find('div.date')[0]->text()) : 'No date found'),
          'thumbnail' => ($contentArea->has('img.thumbnail-img') ? $contentArea->find('img.thumbnail-img')[0]->src : 'No thumbnail found'),
          'content' => ($contentArea->has('div.typography-block') ? self::formatContent($contentArea->find('div.typography-block')[0]->innerHtml())  : 'No Content found'),
        ];

        if ($type === 'json') {
          return json_encode($contentData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } else {
          return $contentData;
        }
      } else {
        return "Content with ID: $id is not exist";
      }
    }
  }

  public static function formatContent($htmlDomData)
  {
    //  $htmlDomData->find('div')[0]->remove();
    // $htmlDomData->find('script')[0]->remove();
    //  $$htmlDomData =      $htmlDomData->find('div')[0]->setHtml('');
    //  $htmlDomData->first('div')->remove();
    // $htmlx =  $htmlDomData->first('div')->remove();
    $xx = new Document($htmlDomData);
    if ($xx->has('div')) {
      return explode('</div>', $htmlDomData)[1];
    } else {
      return $htmlDomData;
    }
  }
  public static function getData($page = 0, $type = 'json')
  {
    $url  = 'https://kominfo.go.id/content/all/laporan_isu_hoaks?page=' . $page;
    $html = self::kominfoCurl($url);
    $mainArray = [];
    $dom =  new Document($html);
    if ($dom->has('div.artikelnya')) {
      foreach ($dom->find('div.artikelnya') as $content) {

        $dates = $content->find('div.date');
        $titleAndLinks = $content->find('a.title');
        $thumbNails = $content->find('img.thumbnail-img');
        $categories  = $content->find('div.author');

        foreach ($dates as $key => $tanggal) {
          $newTanggal = explode('-', str_replace(' ', '-', $dates[$key]->text()));
          $realTanggal = implode('-', array_reverse($newTanggal));

          $cat = $categories[$key]->text();
          $cat = str_replace('Kategori', '', preg_replace('/\xc2\xa0/', '', $cat));
          $arrayData = [
            'id' => explode('/', $titleAndLinks[$key]->href)[3],
            "date" => $realTanggal,
            "title" => $titleAndLinks[$key]->text(),
            "slug" => self::slugify($titleAndLinks[$key]->text()),
            "source" => $titleAndLinks[$key]->href,
            "content" => null,
            "thumbnail" => $thumbNails[$key]->src,
            "category" => $cat,
          ];
          array_push($mainArray, $arrayData);
        }


        if ($type === 'json') {
          return json_encode($mainArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } else {
          return $mainArray;
        }
      }
    }
  }

  /**
   * slufigy :  https://stackoverflow.com/questions/2955251/php-function-to-make-slug-url-string
   */
  public static function slugify($text)
  {
    // replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);

    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    // trim
    $text = trim($text, '-');

    // remove duplicate -
    $text = preg_replace('~-+~', '-', $text);

    // lowercase
    $text = strtolower($text);

    if (empty($text)) {
      return 'n-a';
    }

    return $text;
  }

  public static function kominfoCurl($url)
  {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

    curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

    $headers = array();
    $headers[] = 'Connection: keep-alive';
    $headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36';
    $headers[] = 'Accept: */*';
    $headers[] = 'Sec-Fetch-Site: same-origin';
    $headers[] = 'Sec-Fetch-Mode: cors';
    $headers[] = 'Sec-Fetch-Dest: empty';
    $headers[] = 'Referer: https://kominfo.go.id/';
    $headers[] = 'Accept-Language: en-US,en;q=0.9';

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
      echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);

    return $result;
  }
}
