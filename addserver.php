<?php
/**
 *
 * @Project: Сървър реклама CS-BG.INFO
 * @Author HybridMind <www.webocean.info>
 * @Version: 0.0.1
 * @File addserver.php
 * @Created 23.1.2021 г.
 * @License: MIT
 * @Discord: HybridMind#6095
 *
 */

require_once __DIR__ . "/includes/config.php";
require_once __DIR__ . "/common.php";
require_once __DIR__ . "/includes/phpBB.php";

$getCats = mysqli_query($conn, "SELECT * FROM categories ORDER BY id");

if (isset($_POST['addServ'])) {

    $ip = mysqli_real_escape_string($conn, $_POST['ip']);
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    $ServerIP = $ip;
    include("includes/queryCS.php");
    $name = $server['name'];
    $map = $server['map'];
    $players = $server['players'];
    $maxplayers = $server['playersmax'];
    $os = $server['server_os'];
    $game = $_POST['servertype'];
    $date = time();

    $check = mysqli_query($conn, "SELECT * FROM servers WHERE ip = '$ip'");

    if (empty($_POST['ip'])) {
        message("addserver.php", "danger", "Моля, сложете IP адреса и порта на сървъра");
    } elseif (empty($_POST['type'])) {
        message("addserver.php", "danger", "Моля, изберете типа на сървъра.");
    } elseif (!strstr($_POST['ip'], ':')) {
        message("addserver.php", "danger", "Сървърът трябва задължително да има порт.");
    } elseif (mysqli_num_rows($check) > 0) {
        message("addserver.php", "danger", "Вече има такъв сървър в нашата система!");
    } elseif (empty($name)) {
        message("addserver.php", "danger", "Сървъра, които искате да добавите не работи.");
    }
    mysqli_query($conn, sprintf("INSERT INTO servers (`ip`, `type`, `players`, `maxplayers`,`map`, `os`, `name`, `vip`, `added`, `owner`, `cache`) VALUES ('%s','%s','%s','%s','%s','%s','%s',0,'%s','%s','%s')", $ip, $type, $players, $maxplayers, $map, $os, $name, $date, $bb_user_id, $date));
    message("addserver.php", "success", "Успешно добавихте сървъра");
}
?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">

    <div class="container mt-5">
        <div class="d-grid gap-2 mb-3">
            <a href="index.php" class="btn btn-outline-primary btn-lg">Начало</a>
        </div>
        <?php if ($bb_is_anonymous) {
            echo "<div class='alert alert-danger text-center'>Моля, влезте в акаунта си за да може да добавите сървър</div>";
        } else { ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">IP</label>
                    <input type="text" class="form-control" name="ip" placeholder="IP адрес и порт" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Тип</label>
                    <select class="form-select" name="type">
                        <option selected>Избери...</option>
                        <?php while ($row = mysqli_fetch_assoc($getCats)) { ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" name="addServ">Добави</button>
            </form>
            <?php echo showMessage(); ?>
        <?php } ?>
    </div>

<?php sessionRemove("notifications"); ?>