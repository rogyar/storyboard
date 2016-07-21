<?php
require_once('vendor/autoload.php');

use Rogyar\Storyboard\Storyboard;
use Symfony\Component\Yaml\Yaml;

$token = isset($_GET['token'])? $_GET['token'] : '';
$storyBoard = new Storyboard(new Yaml(), 'etc/config.yml', $token);

if (isset($_POST['data'])) {
    $appendContent = isset($_POST['append'])? true : false;
    $storyBoard->writeContent($_POST['data'], $appendContent);
} else {
    $responseBody = $storyBoard->renderTemplate();
    echo $responseBody;
}

