<?php

require_once(__DIR__ . '/_bootstrap.php');

use RingCentral\SDK;
use RingCentral\subscription\events\NotificationEvent;
use RingCentral\subscription\Subscription;

$credentials = require(__DIR__ . '/_credentials.php');

// Create SDK instance

$rcsdk = new SDK($credentials['appKey'], $credentials['appSecret'], $credentials['server']);

$platform = $rcsdk->getPlatform();

// Authorize

$platform->authorize($credentials['username'], $credentials['extension'], $credentials['password'], true);

// Subscription

$subscription = $rcsdk->getSubscription();

$subscription->addEvents(array('/account/~/extension/~/message-store'));

$subscription->setKeepPolling(false);

$subscription->on(Subscription::EVENT_NOTIFICATION, function (NotificationEvent $e) {
    print 'Notification' . print_r($e->getPayload(), true) . PHP_EOL;
});

print 'Subscribing' . PHP_EOL;

$subscription->register();

print 'End' . PHP_EOL;

