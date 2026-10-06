<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
define('BASE_PATH', '/ibpvn.com');
define('BASE_URL', 'http://localhost/ibpvn.com/');
