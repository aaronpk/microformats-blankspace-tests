<?php

function post($url, $params) {
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
  $response = curl_exec($ch);
  $data = json_decode($response, true);
  if($data && isset($data['items'][0]['properties'])) {
    return $data['items'][0]['properties'];
  } elseif($response) {
    if(preg_match('~<pre><code>(.+)</code></pre>~ms', $response, $match)) {
      $data = json_decode(htmlspecialchars_decode($match[1]), true);
      return $data['items'][0]['properties'];
    }
  }
  return false;
}

function get($url) {
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($ch);
  $data = json_decode($response, true);
  if($data && isset($data['items'][0]['properties'])) {
    return $data['items'][0]['properties'];
  }
  return false;
}

function get_parsed_result($parser, $html, $num=false) {
  switch($parser) {
    case 'python':
      $url = 'https://python.microformats.io/';
      $param = 'doc';
      break;
    case 'ruby':
      $url = 'http://localhost:4567/parse';
      $param = 'html';
      break;
    case 'php':
      $url = 'https://pin13.net/mf2/';
      $param = 'html';
      break;
    case 'go':
      $url = 'https://go.microformats.io/';
      $param = 'html';
      break;
    case 'node':
      $url = 'https://sturdy-backbone.glitch.me/mf2/?url=https://raw.githubusercontent.com/aaronpk/microformats-whitespace-tests/master/tests/%.html';
      break;
  }

  if($num) {
    $response = get(str_replace('%', $num, $url));
  } else {
    $response = post($url, [$param => $html, 'format'=>'json']);
  }

  return [
    'name' => $response['name'][0],
    'content.value' => $response['content'][0]['value'],
    'content.html' => $response['content'][0]['html'],
  ];
}

chdir(__DIR__);
$tests = glob('tests/*.html');

$data = [];

foreach($tests as $htmlfile) {
  $num = str_replace('.html', '', basename($htmlfile));
  $html = file_get_contents($htmlfile);
  $jsonfile = str_replace('.html', '.json', $htmlfile);
  $json = json_decode(file_get_contents($jsonfile), true);
  $expected = [
    'name' => $json['items'][0]['properties']['name'][0],
    'content.value' => $json['items'][0]['properties']['content'][0]['value'],
    'content.html' => $json['items'][0]['properties']['content'][0]['html'],
  ];

  $php = get_parsed_result('php', $html);
  $ruby = get_parsed_result('ruby', $html);
  $python = get_parsed_result('python', $html);
  $go = get_parsed_result('go', $html);
  $node = get_parsed_result('node', $html, $num);

  $data[] = [
    'test' => $num,
    'expected' => $expected,
    'php' => $php,
    'ruby' => $ruby,
    'python' => $python,
    'go' => $go,
    'node' => $node,
  ];
}

file_put_contents('results/output.json', json_encode($data, JSON_PRETTY_PRINT+JSON_UNESCAPED_SLASHES));

require('results/results.php');

