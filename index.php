<?php
require_once('vendor/autoload.php');

use lib\Storyboard;
use Symfony\Component\Yaml\Yaml;

$token = isset($_GET['token'])? $_GET['token'] : '';
$storyBoard = new Storyboard(new Yaml(), 'etc/config.yml', $token);

if (isset($_POST['data'])) {
    $storyBoard->writeContent($_POST['data']);
} else {
    $responseBody = $storyBoard->renderTemplate();
    echo $responseBody;
}

