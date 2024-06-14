<?php
if (!isset($_SESSION['management'])) {
    header('location: authenticate/');
} else {
    header('location: manage/dashboard');
}
