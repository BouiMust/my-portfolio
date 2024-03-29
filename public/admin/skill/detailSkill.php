<!-- SKILL DETAILS PAGE (BACK OFFICE) -->

<!-- HEAD -->
<?php include '../../assets/components/back/head.php' ?>
<title>Détails de compétence</title>

<?php

use App\Controllers\Authentication;
use App\Controllers\SkillController;

// CHECK AUTH
Authentication::check();

// GET SKILL FROM DB
$skill = (new SkillController())->readOne($_GET['id']) ?>

<!-- HEADER -->
<?php include '../../assets/components/back/header.php' ?>

<!-- MAIN CONTENT -->
<main>
    <div class="mb-2">
        <h4 class="text-center text-light py-2">Détails sur la compétence n°<?= $skill->id_skill ?></h4>
    </div>
    <div class="content" style="border: 2px solid #666;">
        <?php
        if (isset($_SESSION['message'])) {
            echo $_SESSION['message'];
            unset($_SESSION['message']);
        };
        ?>
        <div class="row w-100 mx-auto my-2">

            <div class='col-4 ps-4 d-flex justify-content-center align-items-center'><img class='rounded' src='../../assets/images/upload/<?= $skill->getImage() ?>' alt='image de <?= $skill->title ?>' width=80%></div>
            <div class="col-8">
                <table class='table table-striped table-hover text-center border border-secondary'>

                    <tr class='align-middle'>
                        <th class='text-end col-3'>N° :</th>
                        <td><?= $skill->id_skill ?></td>
                    </tr>

                    <tr>
                        <th class='text-end col-3'>Titre :</th>
                        <td class='text-break'><?= $skill->title ?></td>
                    </tr>

                    <tr>
                        <th class='text-end col-3'>Type :</th>
                        <td><span style="color:<?= $skill->getType()['color'] ?>;font-weight:bold;"><?= $skill->getType()['type'] ?></span></td>
                    </tr>

                    <tr>
                        <th class='text-end col-3'>Description :</th>
                        <td class='text-break'><?= $skill->description ?? '&#8211' ?></td>
                    </tr>

                    <tr>
                        <th class='text-end col-3'>Statut :</th>
                        <td><?= $skill->getStatut() ?></td>
                    </tr>

                    <tr>
                        <th></th>
                        <td class='text-center'>
                            <a href='./<?= $skill->id_skill ?>/update' class="text-decoration-none" title='Modifier'>
                                <div class='btn btn-info fs-5 py-1 px-3 border border-dark'>&#128394;</div>
                            </a>
                            <a href='./<?= $skill->id_skill ?>/confirm-delete' class="text-decoration-none" title='Supprimer'>
                                <div class='btn btn-danger fs-5 py-1 px-3 border border-dark'>&#128465;</div>
                            </a>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <a href="./" class="btn btn-success border border-dark w-100">Retour à la liste des compétences</a>
    </div>
</main>

<!-- FOOTER -->
<?php include '../../assets/components/back/footer.php' ?>