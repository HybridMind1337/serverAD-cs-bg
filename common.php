<?php
/**
 *
 * @Project: Сървър реклама CS-BG.INFO
 * @Author HybridMind <www.webocean.info>
 * @Version: 0.0.1
 * @File common.php
 * @Created 23.1.2021 г.
 * @License: MIT
 * @Discord: HybridMind#6095
 *
 */

$conn = mysqli_connect($host, $root, $pass, $user);

if (!$conn) {
    exit(PHP_EOL);
} else {
    mysqli_set_charset($conn, "UTF8");
}

/**
 * @param $id
 * @return string
 */
function get_credits($id): string
{
    global $conn;
    $get = mysqli_query($conn, "SELECT credits from phpbb_users WHERE user_id='{$id}'");
    $row = mysqli_fetch_assoc($get);
    @mysqli_free_result($get);
    return $row['credits'];
}

/**
 * @param $id
 * @param $amount
 */
function set_credits($id, $amount)
{
    global $conn;
    $get = mysqli_query($conn, "UPDATE phpbb_users SET credits=credits+'{$amount}' WHERE user_id='{$id}'");
    @mysqli_free_result($get);
}

/**
 * @param $id
 * @param $amount
 */
function remove_credits($id, $amount)
{
    global $conn;
    $get = mysqli_query($conn, "UPDATE phpbb_users SET credits=credits-'{$amount}' WHERE user_id='{$id}'");
    @mysqli_free_result($get);
}

/**
 * @param $location
 * @param $alert
 * @param $message
 */
function message($location, $alert, $message)
{
    $_SESSION['notifications'] = [
        'status' => 'OK',
        'message' => $message,
        'alert' => $alert
    ];

    header(sprintf("Location: %s", $location));
    exit();
}

/**
 * @return string
 */
function showMessage(): string
{
    if (isset($_SESSION['notifications'])) {
        return sprintf("<div class=\"alert alert-%s text-center\">%s</div>", $_SESSION['notifications']['alert'], $_SESSION['notifications']['message']);
    }
    return '';
}

/**
 * @param $name
 */
function sessionRemove($name)
{
    if (isset($_SESSION[$name])) {
        unset($_SESSION[$name]);
    }
}

/**
 * @param $id
 * @return array|null
 */
function userbyid($id): ?array
{
    global $conn;
    $userid = mysqli_query($conn, "SELECT * FROM phpbb_users WHERE user_id = {$id}");
    $row = mysqli_fetch_assoc($userid);
    return $row;
}

/**
 * @param $id
 * @return array|null
 */
function catID($id): ?array
{
    global $conn;
    $userid = mysqli_query($conn, "SELECT * FROM categories WHERE id = {$id}");
    $row = mysqli_fetch_assoc($userid);
    return $row;
}


/**
 * @param $datetime
 * @param false $full
 * @return string
 * @throws Exception
 */
function time_elapsed_string($datetime, $full = false): string
{
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = [
        'y' => 'г',
        'm' => 'м',
        'w' => 'сед.',
        'd' => 'ден',
        'h' => 'ч',
        'i' => 'мин',
        's' => 'сек',
    ];
    foreach ($string as $k => &$v) if ($diff->$k) {
        $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? '' : '');
    } else {
        unset($string[$k]);
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? ('Обновено преди ' . implode(', ', $string) . '') : 'преди малко';
}

// Функцията за ъпдейтване на сървърите
$getServers = mysqli_query($conn, "SELECT * FROM servers");
while ($row = mysqli_fetch_assoc($getServers)) {
    $id = $row['id'];
    $getLastUP = $row['cache'];
    $ServerIP = $row['ip'];
    if ($getLastUP < time()) {
        $nextUP = time() + $ServersUpdate;
        include("includes/queryCS.php");
        $names = $server['name'];
        $map = $server['map'];
        $players = $server['players'];
        $maxplayers = $server['playersmax'];
        if (!$names) {
            mysqli_query($conn, "UPDATE servers SET players='0', maxplayers='0', name='N/A' WHERE id='{$id}'");
        } else {
            mysqli_query($conn, "UPDATE servers SET name = '{$names}', map = '{$map}', players = '{$players}', maxplayers = '{$maxplayers}',cache = '{$nextUP}' WHERE id='{$id}'");
        }
    }
}

// Функцията, която следи VIP сървърите
$getVIP = mysqli_query($conn, "SELECT * FROM servers WHERE vip=1 AND expirevip<UNIX_TIMESTAMP()");
while ($row = mysqli_fetch_assoc($getVIP)) {
    $id = $row['id'];
    mysqli_query($conn, "UPDATE servers SET expirevip='0', startvip='0', vip='0' WHERE id='{$id}'");
}

// Функция за проверка на SMS кодовете от Mobio
/**
 * @param $servID
 * @param $code
 * @param int $debug
 * @return int
 */
function mobio_checkcode($servID, $code, $debug = 0): int
{
    $res_lines = file("http://www.mobio.bg/code/checkcode.php?servID={$servID}&code={$code}");
    $ret = 0;
    if ($res_lines) {
        if (strstr("PAYBG=OK", $res_lines[0])) {
            $ret = 1;
        } else {
            if ($debug) echo $line . "\n";
        }
    } else {
        if ($debug) echo "Unable to connect to mobio.bg server.\n";
        $ret = 0;
    }
    return $ret;
}