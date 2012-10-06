<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();
$collection->add('ProtalkDevBundle_homepage', new Route('/hello/{name}', array(
    '_controller' => 'ProtalkDevBundle:Default:index',
)));

return $collection;
