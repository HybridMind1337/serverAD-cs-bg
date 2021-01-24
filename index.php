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

require_once __DIR__ . "/includes/config.php";
require_once __DIR__ . "/common.php";
require_once __DIR__ . "/includes/phpBB.php";

$cat = (int)$_GET['cat'];

if (!is_numeric($cat)) {
    header("Location: index.php");
    die();
}

if ($cat) {
    $sql = "SELECT * FROM servers WHERE type = {$cat} ORDER by vip DESC";
    $nums = "SELECT COUNT(*) as numservers, SUM(players) as numplayers, SUM(maxplayers) as slots FROM servers WHERE type= {$cat}";
} else {
    $sql = "SELECT * FROM servers ORDER by vip DESC";
    $nums = "SELECT COUNT(*) as numservers, SUM(players) as numplayers, SUM(maxplayers) as slots FROM servers";
}

$getServ = mysqli_query($conn, $sql);
$getCats = mysqli_query($conn, "SELECT * FROM categories");
?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <style>
        a {
            text-decoration: none
        }
    </style>
    <div class="container mt-5">
        <?php echo showMessage(); ?>
        <div class="d-grid gap-2">
            <a href="addserver.php" class="btn btn-outline-primary btn-lg">Добави сървър</a>
            <a href="vip.php" class="btn btn-outline-primary btn-lg">Направи сървър VIP</a>
            <a href="credits.php" class="btn btn-outline-primary btn-lg">Зареди кредити</a>
        </div>
        <?php
        if ($bb_is_anonymous) {
            echo "<div class='alert alert-danger mt-3 text-center'>Моля, влезте в системата за да имате пълен достъп</div>";
        } else {
            ?>
            <div class="alert alert-primary mt-3 text-center">Здравей, <b style="color: #<?php echo $bb_user_color; ?>"><?php echo $bb_username; ?></b>. Имаш <?php echo get_credits($bb_user_id); ?> кредита.</div>
        <?php } ?>
        <div class="display-4 mb-3">Категории</div>
        <hr/>
        <?php if ($getCats->num_rows > 0) {
            while ($row = mysqli_fetch_assoc($getCats)) { ?>
                <a href="index.php?cat=<?php echo $row['id']; ?>" class="btn btn-primary"><?php echo $row['name']; ?></a>
            <?php } ?>
            <a href="index.php" class="btn btn-primary">Всички сървъри</a>
        <?php } else { ?>
            <div class="alert alert-danger text-center">Няма добавени категории</div>
        <?php } ?>
        <div class="display-4 mt-5 mb-3">Сървъри</div>
        <hr/>
        <?php if ($getServ->num_rows > 0) { ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <tbody>
                    <?php while ($row = mysqli_fetch_assoc($getServ)) {
                        $id = $row['id'];
                        $ip = $row['ip'];
                        $cat = $row['type'];
                        $players = $row['players'];
                        $maxplayers = $row['maxplayers'];
                        $map = $row['map'];
                        $os = $row['os'];
                        $name = $row['name'];
                        $update = $row['cache'];
                        $vip = $row['vip'];

                        if ($vip == "1") {
                            $bg = "class='table-info'";
                            $icon = '<span class="badge bg-primary"><i class="fas fa-star"></i></span>';
                        } else {
                            $bg = "";
                            $icon = "";
                        }

                        $catName = catID($cat);
                        if ($os == 'l') {
                            $os = "Linux";
                        } elseif ($os == 'w') {
                            $os = "Windows";
                        } elseif ($os == 'unknown') {
                            $os = "Unknown";
                        }
                        if ($name == "N/A") {
                            $status = '<span class="badge bg-danger"><i class="fas fa-times"></i></span>';
                        } else {
                            $status = '<span class="badge bg-success"><i class="fas fa-check"></i></span>';
                        }

                        ?>
                        <tr <?php echo $bg; ?>>
                            <th class="text-center"><?php echo $status; ?><br/>(<?php echo $id; ?>)</th>
                            <td><a href="view.php?id=<?php echo $id; ?>"><?php echo $icon; ?><?php echo $name; ?></a><br/>
                                <span style="color:#999; font-size:0.9em"><?php echo $catName['name']; ?> | <?php echo time_elapsed_string('@' . $update); ?></span>
                            </td>
                            <td class="text-center">
                                <b onclick="prompt('IP адреса на сървъра: <?php echo $name ?>', '<?php echo $ip; ?>')"><?php echo $ip; ?></b><br/>
                                <span style="color:#999; font-size:0.9em"><?php echo $os; ?></span>
                            </td>
                            <td class="text-center">Играчи:<br/>
                                <?php echo $players; ?> / <?php echo $maxplayers; ?>
                            </td>
                            <td class="text-center">Карта: <br/><?php echo $map; ?></td>
                            <td class="text-center"><img src="https://cs-bg.info/csbgnet/lgsl/lgsl_files/maps/halflife/cstrike/<?php echo $map; ?>.jpg" alt="<?php echo $map; ?>" width="40" height="40"/></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <div class="alert alert-danger text-center">Няма добавени сървъри</div>
        <?php } ?>
        <div class="display-4 mt-5 mb-3">Статистика</div>
        <hr/>
        <?php
        $getNums = mysqli_query($conn, $nums);
        $row = mysqli_fetch_assoc($getNums);
        $getAllServ = $row['numservers'];
        $getPlayers = $row['numplayers'];
        $getAllPlayers = $row['slots'];
        ?>
        <div class="alert alert-primary text-center">Имаме <?php echo $getAllServ; ?> сървъра, <?php echo $getPlayers; ?> играча от свободни <?php echo $getAllPlayers; ?> слота.</div>
        <?php if ($bb_is_admin) { ?>
            <div class="d-grid gap-2 mb-5">
                <a href="acp.php" class="btn btn-outline-danger btn-lg">Админ панел</a>
            </div>
        <?php } ?>
    </div>
<?php sessionRemove("notifications"); ?>
