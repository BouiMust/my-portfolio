<!-- UPDATE SKILL PAGE (BACK OFFICE) -->

<!-- HEAD -->
<?php include '../../assets/components/back/head.php' ?>
<title>Modification de compétence</title>

<?php

use App\Controllers\Authentication;
use App\Controllers\SkillController;

// CHECK AUTH
Authentication::check();

// CHECK IF FORM SUBMITTED
if (isset($_POST['submit']) && $_POST['action'] === 'update') (new SkillController)->update($_POST['id']);

// GET SKILL FROM DB
$skill = (new SkillController())->readOne($_GET['id']) ?>

<!-- HEADER -->
<?php include '../../assets/components/back/header.php' ?>

<!-- MAIN CONTENT -->
<main>
    <div class="mb-2">
        <h4 class="text-center text-light py-2">Modifier la compétence n°<?= $skill->id_skill ?></h4>
    </div>
    <div class="content" style="border: 2px solid #666;">
        <div class="col-6 mx-auto py-3">

            <form action='' method='post' enctype='multipart/form-data'>
                <?php
                if (isset($_SESSION['message'])) {
                    echo $_SESSION['message'];
                    unset($_SESSION['message']);
                };
                ?>
                <table class="table table-striped">

                    <input type='hidden' name='action' value='update'>
                    <input type='hidden' name='id' value='<?= $skill->id_skill ?>'>
                    <tr>
                        <th class='text-end align-middle col-3'>Titre :</th>
                        <td><input class='form-control' type='text' name='title' id='title' value='<?= $skill->title ?>'></td>
                    </tr>
                    <tr>
                        <th class='text-end align-middle col-3'>Type :</th>
                        <td>
                            <div class='d-flex mx-auto justify-content-evenly mt-3'>
                                <div class='form-check form-switch'>
                                    <label class='form-check-label' for='front-end'>Front-end</label>
                                    <input class='form-check-input' type='radio' name='type' id='front-end' value='1' <?= $skill->type === 1 ? 'checked' : ''; ?>>
                                </div>
                                <div class='form-check form-switch'>
                                    <label class='form-check-label' for='back-end'>Back-end</label>
                                    <input class='form-check-input' type='radio' name='type' id='back-end' value='2' <?= $skill->type === 2 ? 'checked' : ''; ?>>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th class='text-end'>Description :</th>
                        <td><textarea class='form-control' name='description' id='description' rows='5'><?= $skill->description ?></textarea></td>
                    </tr>
                    <tr>
                        <th class='text-end align-middle col-3'>Image :</th>
                        <td><input class='form-control' type='file' name='image' id='image'></td>
                    </tr>
                    <tr>
                        <th class='text-end align-middle col-3'>Statut :</th>
                        <td>
                            <select class='pointer' style='padding: 10px;' name='isActive' id='isActive'>
                                <option value=1>Activé</option>
                                <option value=0 <?= $skill->active ?: 'selected' ?>>Désactivé</option>
                            </select>
                        </td>
                    </tr>
                </table>
                <div class='text-center'>
                    <button class='btn btn-success py-2 px-4 border border-dark' type='submit' name="submit">Valider</button>
                    <a href='../<?= $skill->id_skill ?>' class='btn btn-danger py-2 px-4 border border-dark'>Retour</a>
                </div>
            </form>
        </div>
    </div>
</main>

<!-- FOOTER -->
<?php include '../../assets/components/back/footer.php' ?>