<!-- PROJECT DETAILS PAGE (BACK OFFICE) -->

<!-- HEAD -->
<?php include '../../assets/components/back/head.php' ?>
<title>Détails de réalisation</title>

<?php

use App\Controllers\Authentication;
use App\Controllers\ProjectController;

// CHECK AUTH
Authentication::check();

// GET PROJECT AND ASSOCIATED SKILLS FROM DB
$project = (new ProjectController)->readOne($_GET['id']);
$skills =  (new ProjectController)->loadSkillsFromProject($project) ?>

<!-- HEADER -->
<?php include '../../assets/components/back/header.php' ?>

<!-- MAIN CONTENT -->
<main>
    <div class="mb-2">
        <h4 class="text-center text-light py-2">Détails sur la réalisation n°<?= $project->id_project ?></h4>
    </div>
    <div class="content" style="border: 2px solid #666;">
        <?php
        if (isset($_SESSION['message'])) {
            echo $_SESSION['message'];
            unset($_SESSION['message']);
        };
        ?>
        <div class="row w-100 mx-auto my-2">

            <div class='col-4 ps-4 my-auto text-center'>
                <a href="/assets/images/upload/<?= $project->getImage() ?>">
                    <img class='rounded' src='/assets/images/upload/<?= $project->getImage() ?>' alt='image de <?= $project->title ?>' width=99%>
                </a>
            </div>
            <div class="col-8">
                <table class='table table-striped table-hover text-center border border-secondary'>

                    <tr class='align-middle'>
                        <th class='text-end col-3'>N° :</th>
                        <td><?= $project->id_project ?></td>
                    </tr>

                    <tr>
                        <th class='text-end col-3'>Titre :</th>
                        <td class='text-break'><?= $project->title ?></td>
                    </tr>

                    <tr>
                        <th class='text-end col-3'>Description :</th>
                        <td class='text-break'><?= $project->description ?? '&#8211' ?></td>
                    </tr>

                    <tr>
                        <th class='text-end col-3'>Compétence(s) exploitée(s) :</th>
                        <td class='text-break'>
                            <?php foreach ($skills as $skill) : ?>
                                <img src='/assets/images/upload/<?= $skill->image ?>' alt='image de <?= $skill->title ?>' title="<?= $skill->title ?>" width=40px class='rounded'>
                            <?php endforeach ?>
                        </td>
                    </tr>

                    <tr>
                        <th class='text-end col-3'>Date de début :</th>
                        <td class='text-break'><?= $project->getDateStart() ?></td>
                    </tr>

                    <tr>
                        <th class='text-end col-3'>Date de fin :</th>
                        <td class='text-break'><?= $project->getDateEnd() ?? '&#8211' ?></td>
                    </tr>

                    <tr>
                        <th class='text-end col-3'>Lien web :</th>
                        <td class='text-break'>
                            <?php if ($project->link_web) : ?>
                                <a href='<?= $project->link_web ?>' class='fw-bold' target='_blank'><?= $project->link_web ?></a>
                            <?php else : ?>
                                &#8211
                            <?php endif ?>
                        </td>
                    </tr>

                    <tr>
                        <th class='text-end col-3'>Lien github :</th>
                        <td class='text-break'>
                            <?php if ($project->link_github) : ?>
                                <a href='<?= $project->link_github ?>' class='fw-bold' target='_blank'><?= $project->link_github ?></a>
                            <?php else : ?>
                                &#8211
                            <?php endif ?>
                        </td>
                    </tr>

                    <tr>
                        <th class='text-end col-3'>Statut :</th>
                        <td><?= $project->getStatut() ?></td>
                    </tr>

                    <tr>
                        <th></th>
                        <td class='text-center'>
                            <a href='./<?= $project->id_project ?>/update' class="text-decoration-none" title='Modifier'>
                                <div class='btn btn-info fs-5 py-1 px-3 border border-dark'>&#128394;</div>
                            </a>
                            <a href='./<?= $project->id_project ?>/confirm-delete' class="text-decoration-none" title='Supprimer'>
                                <div class='btn btn-danger fs-5 py-1 px-3 border border-dark'>&#128465;</div>
                            </a>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <a href="./" class="btn btn-success border border-dark w-100">Retour à la liste des réalisations</a>
    </div>
</main>

<!-- FOOTER -->
<?php include '../../assets/components/back/footer.php' ?>