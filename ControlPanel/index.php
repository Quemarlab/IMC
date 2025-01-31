<?php
if (!isset($_COOKIE['logintimes'])) {
    setcookie('logintimes',1,time() + 60 * 60 * 24 * 7);
}

if (!isset($_SESSION['management'])) {
    header('location: authenticate/');
} else {
    header('location: manage/dashboard');
}
