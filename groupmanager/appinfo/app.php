<?php

namespace OCA\Groupmanager;

$api = new \OCA\AppFramework\Core\API('groupmanager');

$api->addNavigationEntry(array(

  // the string under which your app will be referenced in owncloud
  'id' => $api->getAppName(),

  // sorting weight for the navigation. The higher the number, the higher
  // will it be listed in the navigation
  'order' => 10,

  // the route that will be shown on startup
  'href' => $api->linkToRoute('groupmanagerIndex'),

  // the icon that will be shown in the navigation
  // this file needs to exist in img/example.png
  'icon' => $api->imagePath('example.png'),

  // the title of your application. This will be used in the
  // navigation or on the settings page of your app
  'name' => $api->getTrans()->t('Groupmanager')

));

