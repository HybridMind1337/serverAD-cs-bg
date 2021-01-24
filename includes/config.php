<?php
/**
 *
 * @Project: Сървър реклама CS-BG.INFO
 * @Author HybridMind <www.webocean.info>
 * @Version: 0.0.1
 * @File config.php
 * @Created 22.1.2021 г.
 * @License: MIT
 * @Discord: HybridMind#6095
 *
 */

if (count(get_included_files()) == 1) {
    header("Location: index.php");
    exit;
}

error_reporting(E_ALL);
session_start();

$host = "localhost";
$root = "testing"; // Потребител на базаданни
$pass = "testing"; // Парола
$user = "testing"; // Базаданни

$forumPath = "./forums/"; // Пътя до форума

$addServCred = 20; // Колко кредита са нужни за да се направи сървъра VIP
$ServersUpdate = 300; // През колко време да се ъпдейтват сървърите? По-начало 5 мин (5 min in seconds @ google.bg) - epoch
$vip_expire = 604800; // Колко дни да е активен VIP ? По-начало 7 дни (7 days in seconds @ google.bg) - epoch

$small = 10; // Колко кредита да дава за цена 2.40лв
$medium = 20; // Колко кредита да дава за цена 4.80лв
$big = 30; // Колко кредита да дава за цена 6.00лв

$smallID = 24796; // 2.40лв
$mediumID = 24796; // 4.80лв
$bigID = 24796; // 6.00лв

$info = "Изпрати SMS с текст блабла на номер 1092 (2,40 лв. с ддс)";
$info2 = "Изпрати SMS с текст блабла на номер 1094 (4,80 лв. с ддс)";
$info3 = "Изпрати SMS с текст блабла на номер 1096 (6,00 лв. с ддс)";