<?php
/**
 *
 * @Project: Сървър реклама CS-BG.INFO
 * @Author HybridMind <www.webocean.info>
 * @Version: 0.0.1
 * @File view.php
 * @Created 24.1.2021 г.
 * @License: MIT
 * @Discord: HybridMind#6095
 *
 */

require_once __DIR__ . "/includes/config.php";
require_once __DIR__ . "/common.php";
require_once __DIR__ . "/includes/phpBB.php";

$id = (int)$_GET['id'];

if (!is_numeric($id)) {
    header("Location: index.php");
    die();
}
if (isset($_POST['del'])) {
    $id = mysqli_real_escape_string($conn, $_POST['servID']);
    mysqli_query($conn, "DELETE FROM servers WHERE id = " . $id);

    message("index.php", "success", "Сървъра е успешно изтрит.");
}
$getServ = mysqli_query($conn, "SELECT * FROM servers WHERE id = {$id}");
?>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <div class="container mt-5 text-center">
        <?php echo showMessage(); ?>
        <div class="d-grid gap-2">
            <a href="index.php" class="btn btn-outline-primary btn-lg">Начало</a>
        </div>
        <?php if ($getServ->num_rows > 0) {
            while ($row = mysqli_fetch_assoc($getServ)) {
                if ($row['vip'] == "1") {
                    $icon = '<span class="badge bg-primary"><i class="fas fa-star"></i></span>';
                } else {
                    $icon = "";
                }
                if ($row['os'] == 'l') {
                    $os = "Linux";
                } elseif ($row['os'] == 'w') {
                    $os = "Windows";
                } elseif ($row['os'] == 'unknown') {
                    $os = "Unknown";
                }
                if ($row['name'] == "N/A") {
                    $status = '<span class="badge bg-danger"><i class="fas fa-times"></i></span>';
                } else {
                    $status = '<span class="badge bg-success"><i class="fas fa-check"></i></span>';
                }
                $cat = catID($row['type']);
                $userinfo = userbyid($row['owner']);
                ?>
                <div class="display-4 mt-5 mb-3"><?php echo $status; ?> <?php echo $row['name']; ?></div>
                <hr/>
                Тип: <?php echo $cat['name']; ?><br/>
                IP: <?php echo $icon; ?> <?php echo $row['ip']; ?><br/>
                Карта: <?php echo $row['map']; ?><br/>
                Играчи: <?php echo $row['players']; ?> / <?php echo $row['maxplayers']; ?><br/>
                OS: <?php echo $os; ?><br/>
                Добавен от: <?php echo $userinfo['username']; ?><br/><br/>
                Информацията може да не е актуална.<br/>
                <?php echo time_elapsed_string('@' . $row['cache']);
                if ($bb_is_admin) { ?>
                    <form method="POST">
                        <input type="hidden" name="servID" value="<?php echo $row['id']; ?>"/>
                        <button type="submit" name="del" class="m-1 btn btn-danger">Изтрий сървъра</button>
                    </form>
                    <?php
                }
            }
        } else { ?>
            <div class="alert alert-danger text-center mt-3">Няма такъв сървър</div>
        <?php } ?>
    </div>
<?php sessionRemove("notifications"); ?>