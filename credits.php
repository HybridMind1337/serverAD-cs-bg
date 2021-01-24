<?php
/**
 *
 * @Project: Сървър реклама CS-BG.INFO
 * @Author HybridMind <www.webocean.info>
 * @Version: 0.0.1
 * @File credits.php
 * @Created 24.1.2021 г.
 * @License: MIT
 * @Discord: HybridMind#6095
 *
 */
require_once __DIR__ . "/includes/config.php";
require_once __DIR__ . "/common.php";
require_once __DIR__ . "/includes/phpBB.php";
if (isset($_POST['add'])) {
    $code = mysqli_real_escape_string($conn, $_POST['sms']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);

    if ($price == 240) {
        $servID = $smallID;
    } elseif ($price == 480) {
        $servID = $mediumID;
    } elseif ($price == 600) {
        $servID = $bigID;
    }
    if (mobio_checkcode($servID, $code) == 1) {
        if (empty($_POST['sms'])) {
            message("credits.php", "danger", "Моля, поставете SMS кодът.");
        } elseif (empty($_POST['price'])) {
            message("credits.php", "danger", "Моля, изберете цена.");
        } elseif ($price == 240) {
            set_credits($bb_user_id, $small);
        } elseif ($price == 480) {
            set_credits($bb_user_id, $medium);
        } elseif ($price == 600) {
            set_credits($bb_user_id, $big);
        }
        message("credits.php", "success", "Успешно добавени кредити.");
    } else {
        message("credits.php", "danger", "Грешен SMS код за достъп.");
    }

}
?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <div class="container mt-5">
        <div class="d-grid gap-2 mb-3">
            <a href="index.php" class="btn btn-outline-primary btn-lg">Начало</a>
        </div>
        <?php if ($bb_is_anonymous) { ?>
            <div class="alert alert-danger text-center">Моля, влезте в акаунта си за да имате пълен достъп до системата!</div>
        <?php } else { ?>
            <div class="display-4 mt-5 mb-3">Зареждане на кредити</div>
            <hr/><?php echo showMessage(); ?>
            <div class="240 box alert alert-primary"><?php echo $info; ?></div>
            <div class="480 box alert alert-primary"><?php echo $info2; ?></div>
            <div class="600 box alert alert-primary"><?php echo $info3; ?></div>
            <form method="POST">
                <div class="input-group mt-3 mb-3">
                    <label class="input-group-text">Цена</label>
                    <select class="form-select" name="price">
                        <option selected>Избери...</option>
                        <option value="240">2.40лв (<?php echo $small; ?> кредита)</option>
                        <option value="480">4.80лв (<?php echo $medium; ?> кредита)</option>
                        <option value="600">6.00лв (<?php echo $big ?> кредита)</option>
                    </select>
                </div>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="sms" placeholder="SMS Кодът" required>
                    <button class="btn btn-outline-success" type="submit" name="add">Добави</button>
                </div>
            </form>
        <?php } ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function () {
            $("select").change(function () {
                $(this).find("option:selected").each(function () {
                    var optionValue = $(this).attr("value");
                    if (optionValue) {
                        $(".box").not("." + optionValue).hide();
                        $("." + optionValue).show();
                    } else {
                        $(".box").hide();
                    }
                });
            }).change();
        });
    </script>
<?php sessionRemove("notifications"); ?>