<?php
$msg_success = '';

$db = new SQLite3('./api/sport.db');
$db->exec('CREATE TABLE IF NOT EXISTS sport(id INTEGER PRIMARY KEY NOT NULL, title VARCHAR(25), url VARCHAR(200), c1 VARCHAR(25), c2 VARCHAR(25), c3 VARCHAR(25))');
$rows = $db->query('SELECT COUNT(*) as count FROM sport');
$row1 = $rows->fetchArray();
$numRows = $row1['count'];

if ($numRows == 0) {
	$db->exec('INSERT INTO sport(id, title, url, c1, c2, c3) VALUES(\'1\', \'Default\', \'https://www.tvsportguide.com/widget/5cc316f797659/?heading=Events&border_color=custom&autoscroll=1&custom_colors=\',\'#1d23dd\',\'#ffffff\',\'#000000\')');
}

$res = $db->query('SELECT * FROM sport WHERE id=\'1\'');
$row = $res->fetchArray();
$id = $row['id'];
$urll = $row['url'];
$c1 = $row['c1'];
$c2 = $row['c2'];
$c3 = $row['c3'];

if (isset($_POST['submit'])) {
	$db->exec('UPDATE sport SET url=\'' . $_POST['urll'] . '\', c1=\'' . $_POST['c1'] . '\', c2=\'' . $_POST['c2'] . '\', c3=\'' . $_POST['c3'] . '\' WHERE id=\'1\'');
	$msg_success = 'Update successful';
	header('Location: sport.php?success');
}

include 'includes/header.php';
?>

<!-- Begin Page Content -->
<div class="container-fluid mt-4">

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            <?= $msg_success; ?>
        </div>
    <?php endif; ?>

    <h1 class="h3 mb-4" style="color: #333;">Atualização esportiva</h1>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white py-3">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fa fa-futbol"></i> Atualizar URL de esportes
                    </h6>
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="form-group">
                            <label for="urll"><strong>LINK</strong></label>
                            <input type="text" class="form-control" name="urll" value="<?= $urll; ?>" placeholder="Enter URL">
                        </div>
                        <div class="form-group">
                            <label for="c1"><strong>Cor da borda</strong></label>
                            <input value="<?= $c1; ?>" name="c1" type="color" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="c2"><strong>Cor de fundo</strong></label>
                            <input value="<?= $c2; ?>" name="c2" type="color" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="c3"><strong>Cor do texto</strong></label>
                            <input value="<?= $c3; ?>" name="c3" type="color" class="form-control">
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary btn-block" name="submit" type="submit">
                                <i class="fas fa-check"></i> Atualizar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include 'includes/footer.php';
require 'includes/egz.php';
?>
</body>
