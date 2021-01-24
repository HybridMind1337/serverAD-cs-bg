<?php
/**
 *
 * @Project: Сървър реклама CS-BG.INFO
 * @Author HybridMind <www.webocean.info>
 * @Version: 0.0.1
 * @File vip.php
 * @Created 23.1.2021 г.
 * @License: MIT
 * @Discord: HybridMind#6095
 *
 */

require_once __DIR__ . "/includes/config.php";
require_once __DIR__ . "/common.php";
require_once __DIR__ . "/includes/phpBB.php";
$getServers = mysqli_query($conn, "SELECT * FROM servers WHERE owner = {$bb_user_id}");
if (isset($_POST['vip'])) {

    $serverID = mysqli_real_escape_string($conn, $_POST['serv']);
    $start = time();
    $expire = $start + $vip_expire;

    $check = mysqli_query($conn, "SELECT vip,id FROM servers WHERE id={$serverID} AND vip=1");
    if (empty($_POST['serv'])) {
        message("vip.php", "danger", "Моля, изберете сървър");
    } elseif ($check->num_rows > 0) {
        message("vip.php", "danger", "Избрания сървър вече има VIP статус");
    } elseif (get_credits($bb_user_id) <= $addServCred) {
        message("vip.php", "danger", "Нямате достатъчно кредити за да добавите сървъра.");
    }
    mysqli_query($conn, "UPDATE servers SET vip='1',startvip='{$start}',expirevip='{$expire}' WHERE id='{$serverID}'");
    remove_credits($bb_user_id, $addServCred);
    message("vip.php", "success", "Успешно направихте вашият сървър със статус VIP!");
}
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">
<div class="container mt-5">
    <div class="d-grid gap-2">
        <a href="index.php" class="btn btn-outline-primary btn-lg">Начало</a>
    </div>
    <div class="display-4 mt-5 mb-3">V.I.P Реклама</div>
    <hr/>

    <div class="alert alert-primary text-center">Здравей, <b style="color: #<?php echo $bb_user_color; ?>"><?php echo $bb_username; ?></b>. Имаш <?php echo get_credits($bb_user_id); ?> кредита, за да направиш сървъра VIP е нужно <?php echo $addServCred; ?> кредита.</div>
    <form method="POST">
        <div class="input-group mb-3">
            <label class="input-group-text">Сървър</label>
            <select class="form-select" name="serv">
                <?php while ($row = mysqli_fetch_assoc($getServers)) { ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                <?php } ?>
            </select>
            <button type="submit" name="vip" class="btn btn-success">Направи</button>
        </div>
    </form>
    <?php echo showMessage(); ?>
</div>

<?php sessionRemove("notifications"); ?>