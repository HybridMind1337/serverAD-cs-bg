<?php
/**
 *
 * @Project: Сървър реклама CS-BG.INFO
 * @Author HybridMind <www.webocean.info>
 * @Version: 0.0.1
 * @File acp.php
 * @Created 23.1.2021 г.
 * @License: MIT
 * @Discord: HybridMind#6095
 *
 */
require_once __DIR__ . "/includes/config.php";
require_once __DIR__ . "/common.php";
require_once __DIR__ . "/includes/phpBB.php";

$getCats = mysqli_query($conn, "SELECT * FROM categories");

// Проверяваме дали потребителя е админ
if (!$bb_is_admin) {
    header("Location: index.php");
    die();
}

// Добавяне на категорията
if (isset($_POST['addCat'])) {
    if (empty($_POST['name'])) {
        message("addserver.php", "danger", "Моля, напишете името на категорията");
    }
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    mysqli_query($conn, "INSERT INTO `categories`(`name`) VALUES ('{$name}')");
    message("acp.php", "success", "Успешна добавена категория");
}

// Изтриване на категория
if (isset($_POST['delcat'])) {
    $cat = mysqli_real_escape_string($conn, $_POST['cat']);
    mysqli_query($conn, "DELETE FROM categories WHERE id = {$cat}");
    message("acp.php", "success", "Категорията е успешно премахната");
}
?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">
    <div class="container mt-5">
        <div class="d-grid gap-2">
            <a href="index.php" class="btn btn-outline-primary btn-lg">Начало</a>
        </div>
        <?php echo showMessage(); ?>
        <div class="display-4">Добавяне на категория</div>
        <hr/>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Име</label>
                <input type="text" class="form-control" name="name" placeholder="Име на категорията" required>
            </div>
            <button type="submit" class="btn btn-primary" name="addCat">Добави</button>
        </form>

        <div class="display-4">Премахване на категории</div>
        <hr/>
        <form method="POST">
            <div class="input-group mb-3">
                <label class="input-group-text">Категория</label>
                <select class="form-select" name="cat">
                    <?php while ($row = mysqli_fetch_assoc($getCats)) { ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                    <?php } ?>
                </select>
                <button type="submit" name="delcat" class="btn btn-danger">Изтрий</button>
            </div>
        </form>
    </div>

<?php sessionRemove("notifications"); ?>