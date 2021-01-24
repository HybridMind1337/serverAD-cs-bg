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

// Странициране
function pagination($results, $properties = [])
{
    $defaultProperties = [
        'get_vars' => [],
        'per_page' => 15,
        'per_side' => 4,
        'get_name' => 'page'
    ];

    foreach ($defaultProperties as $name => $default) $properties[$name] = (isset($properties[$name])) ? $properties[$name] : $default;

    foreach ($properties['get_vars'] as $name => $value) {
        if (isset($_GET[$name]) && ($name != $properties['get_name'])) $GETItems[] = $name . '=' . $value;
    }
    $l = (empty($GETItems)) ? '?' . $properties['get_name'] . '=' : '?' . implode('&', $GETItems) . '&' . $properties['get_name'] . '=';

    $totalPages = ceil($results / $properties['per_page']);
    $currentPage = (isset($_GET[$properties['get_name']]) && ($_GET[$properties['get_name']] > 1)) ? $_GET[$properties['get_name']] : 1;
    $currentPage = ($currentPage > $totalPages) ? $totalPages : $currentPage;

    $previousPage = $currentPage - 1;
    $nextPage = $currentPage + 1;

    // calculate which pages to show
    if ($totalPages > ($properties['per_side'] * 2) + 1) {
        $loopStart = $currentPage - $properties['per_side'];
        $loopRange = $currentPage + $properties['per_side'];

        $loopStart = ($loopStart < 1) ? 1 : $loopStart;
        while ($loopRange - $loopStart < $properties['per_side'] * 2) {
            $loopRange++;
        }

        $loopRange = ($loopRange > $totalPages) ? $totalPages : $loopRange;
        while ($loopRange - $loopStart < $properties['per_side'] * 2) {
            $loopStart--;
        }
    } else {
        $loopStart = 1;
        $loopRange = $totalPages;
    }

    // start placing data to output
    $output = '';
    $output .= '
	<div class="text-center">
	  <ul class="pagination">
	 ';


    // first and previous page
    if ($currentPage != 1) {
        $output .= '<li class="page-item"><a class="page-link" href=\'' . $l . '1\'>&#171;</a></li>';
        $output .= '<li class="page-item"><a class="page-link" href=\'' . $l . $previousPage . '\'>‹</a></li>';
    } else {
        $output .= '<li class="page-item"><span class=\'page-link inactive\'>&#171;</span></li>';
        $output .= '<li class="page-item"><span class=\'page-link inactive\'>‹</span></li>';
    }


    // add the pages
    for ($p = $loopStart; $p <= $loopRange; $p++) if ($p != $currentPage) {
        $output .= '<li class="page-item"><a class="page-link" href=\'' . $l . $p . '\'>' . $p . '</a></li>';
    } else {
        $output .= '<li class="page-item"><a class="page-link" href="#">' . $p . '</a></li>';
    }
    // next and last page
    if ($currentPage != $totalPages) {
        $output .= '<li class="page-item"><a class="page-link" href=\'' . $l . $nextPage . '\' class=\'active\'>›</a></li>';
        $output .= '<li class="page-item"><a class="page-link" href=\'' . $l . $totalPages . '\' class=\'active\'>&#187;</a></li>';
    } else {
        $output .= '<li class="page-item"><span class=\'page-link inactive\'>›</span></li>';
        $output .= '<li class="page-item"><span class=\'page-link inactive\'>&#187;</span></li>';
    }

    $output .= '</ul>
   </div>';
    // end of output

    return [
        'limit' => [
            'first' => $previousPage * $properties['per_page'],
            'second' => $properties['per_page']
        ],

        'output' => $output
    ];
}

function mysqli_result($result, $row, $field = 0)
{
    if ($result === false) return false;
    if ($row >= mysqli_num_rows($result)) return false;
    if (is_string($field) && !(strpos($field, ".") === false)) {
        $t_field = explode(".", $field);
        $field = -1;
        $t_fields = mysqli_fetch_fields($result);
        for ($id = 0; $id < mysqli_num_fields($result); $id++) {
            if ($t_fields[$id]->table == $t_field[0] && $t_fields[$id]->name == $t_field[1]) {
                $field = $id;
                break;
            }
        }
        if ($field == -1) return false;
    }
    mysqli_data_seek($result, $row);
    $line = mysqli_fetch_array($result);
    return isset($line[$field]) ? $line[$field] : false;
}
