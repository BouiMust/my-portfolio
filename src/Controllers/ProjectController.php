<?php
// PROJECT CONTROLLER

namespace App\Controllers;

use PDO;
use App\Models\Project;
use App\Models\Skill;

class ProjectController
{
    // GET ALL PROJECTS FROM DATABASE
    public function readAll(string $statut = null, string $orderByIdDesc = null): array
    {
        // get only active projects if 'active' is in arg, or get all projects
        $sql = $statut === 'active' ? "SELECT * FROM project WHERE `active` = 1" : "SELECT * FROM project";
        !$orderByIdDesc ?: $sql .= " $orderByIdDesc";
        $statement = DatabaseConnection::getConnection()->prepare($sql);
        $statement->execute();
        $projects = $statement->fetchAll(PDO::FETCH_CLASS, Project::class);  // or "App\\Models\\Project" instead of Project::class
        return $projects;

        // foreach ($projects as $project) {
        //     $this->loadSkillsFromProject($project);
        // }

    }

    // GET ONE PROJECT
    public function readOne(int $id): Project
    {
        $sql = "SELECT * FROM project WHERE id_project = :id";

        $statement = DatabaseConnection::getConnection()->prepare($sql);
        $statement->bindParam(":id", $id);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_CLASS, Project::class);
        $project = $statement->fetch();

        if (!$project) GeneralController::redirectWithError('../', 'La ressource que vous recherchez n\'existe pas.');

        return $project;

        // Requête de récupération des compétences pour ce projet
        // $this->loadSkillsFromProject($project);
    }

    // CREATE PROJECT
    public function create(): void
    {
        // check if form valid
        $this->checkForm($_SERVER['SCRIPT_NAME']);

        // check if file uploaded
        if (is_uploaded_file($_FILES['image']['tmp_name'])) {

            // check if the file uploaded is an image
            if (strtolower(explode("/", $_FILES['image']['type'])[0]) !== 'image') {
                GeneralController::redirectWithError($_SERVER['SCRIPT_NAME'], 'Erreur de fichier.');
            } else {
                // create name for the image and save to disk
                $imageName = ImageController::createName();
                ImageController::saveToDisk($imageName);
            }
        }

        // formatting datas
        $title = strip_tags(ucwords(strtolower($_POST['title'])));
        $description = $_POST['description'] ?: null;
        $date_start = $_POST['date-start'];
        $date_end = $_POST['date-end'] ?: null;
        $image = $imageName ?? null;
        $link_web = $_POST['link-web'] ?: null;
        $link_github = $_POST['link-github'] ?: null;
        $active = (int)$_POST['isActive'];

        // save project in DB
        $sql = "
            INSERT INTO project (title, description, date_start, date_end, image, link_web, link_github, active)
            VALUES (:title, :description, :date_start, :date_end, :image, :link_web, :link_github, :active);
            SELECT LAST_INSERT_ID();
        ";

        $statement = DatabaseConnection::getConnection()->prepare($sql);
        $statement->bindParam(":title", $title);
        $statement->bindParam(":description", $description);
        $statement->bindParam(":date_start", $date_start);
        $statement->bindParam(":date_end", $date_end);
        $statement->bindParam(":image", $image);
        $statement->bindParam(":link_web", $link_web);
        $statement->bindParam(":link_github", $link_github);
        $statement->bindParam(":active", $active);
        $statement->execute();

        // ADD SKILLS ASSOCIATED WITH THE PROJECT IN DB
        // GET THE LAST INSERTED PROJECT
        $sql = "
            SELECT LAST_INSERT_ID() as id;
        ";
        $statement = DatabaseConnection::getConnection()->prepare($sql);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_CLASS, Project::class);
        $idProject = $statement->fetch()->id;

        // check if skills selected
        if (!empty($_POST['skills'])) {
            // For each skills, insert a link with the project 
            foreach ($_POST['skills'] as $idSkill) {

                // Insert in project_skill table in DB
                $sql = "
                    INSERT INTO project_skill (id_project, id_skill)
                    VALUES (:id_project, :id_skill)
                ";
                $statement = DatabaseConnection::getConnection()->prepare($sql);
                $statement->bindParam(":id_project", $idProject);
                $statement->bindParam(":id_skill", $idSkill);
                $statement->execute();
            }
        }

        GeneralController::redirectWithSuccess('../project', "La réalisation '$title' a été ajoutée.");
    }

    // UPDATE PROJECT
    public function update(int $id): void
    {

        // check if form valid
        $this->checkForm($_SERVER['REQUEST_URI']);

        // check if file uploaded
        if (is_uploaded_file($_FILES['image']['tmp_name'])) {

            // check if the file uploaded is an image
            if (strtolower(explode("/", $_FILES['image']['type'])[0]) !== 'image') {
                GeneralController::redirectWithError($_SERVER['SCRIPT_NAME'], 'Erreur de fichier.');
            } else {
                // create name for the image and save to disk
                $imageName = ImageController::createName();
                ImageController::saveToDisk($imageName);

                // remove the old image (if there is one)
                ImageController::removeFromDisk($id, 'project');

                // update image in DB
                $sql = "UPDATE project SET image = :image WHERE id_project = :id";
                $statement = DatabaseConnection::getConnection()->prepare($sql);
                $statement->bindParam(":id", $id);
                $statement->bindParam(":image", $imageName);
                $statement->execute();
            }
        }

        // formatting datas
        $title = strip_tags(ucwords(strtolower($_POST['title'])));
        $description = $_POST['description'] ?: null;
        $date_start = $_POST['date-start'];
        $date_end = $_POST['date-end'] ?: null;
        $link_web = $_POST['link-web'] ?: null;
        $link_github = $_POST['link-github'] ?: null;
        $active = (int)$_POST['isActive'];

        // Update project in DB
        $sql = "
            UPDATE project SET
            title = :title,
            description = :description,
            date_start = :date_start,
            date_end = :date_end,
            link_web = :link_web,
            link_github = :link_github,
            active = :active
            WHERE id_project = :id
        ";

        // prepare and execute the query
        $statement = DatabaseConnection::getConnection()->prepare($sql);
        $statement->bindParam(":title", $title);
        $statement->bindParam(":description", $description);
        $statement->bindParam(":date_start", $date_start);
        $statement->bindParam(":date_end", $date_end);
        $statement->bindParam(":link_web", $link_web);
        $statement->bindParam(":link_github", $link_github);
        $statement->bindParam(":active", $active);
        $statement->bindParam(":id", $id);
        $statement->execute();

        // Remove skills linked to the project
        $sql = "
                DELETE FROM project_skill
                WHERE id_project = :id
            ";
        $statement = DatabaseConnection::getConnection()->prepare($sql);
        $statement->bindParam(":id", $id);
        $statement->execute();

        // check if skills selected
        if (!empty($_POST['skills'])) {
            // For each skills, insert a link with the project
            foreach ($_POST['skills'] as $idSkill) {

                // Insert in project_skill table in DB
                $sql = "
                        INSERT INTO project_skill (id_project, id_skill)
                        VALUES (:id_project, :id_skill)
                    ";
                $statement = DatabaseConnection::getConnection()->prepare($sql);
                $statement->bindParam(":id_project", $id);
                $statement->bindParam(":id_skill", $idSkill);
                $statement->execute();
            }
        }

        GeneralController::redirectWithSuccess("../$id", "La réalisation '$title' a été modifiée.");
    }

    // DELETE PROJECT
    public function delete(int $id): void
    {
        // Remove skills linked to the project
        $sql = "
            DELETE FROM project_skill
            WHERE id_project = :id
        ";
        $statement = DatabaseConnection::getConnection()->prepare($sql);
        $statement->bindParam(":id", $id);
        $statement->execute();

        // remove the old image (if there is one)
        ImageController::removeFromDisk($id, 'project');

        // delete project in DB
        $sql = "DELETE FROM project WHERE id_project = :id";
        $statement = DatabaseConnection::getConnection()->prepare($sql);
        $statement->bindParam(":id", $id);
        $statement->execute();

        GeneralController::redirectWithSuccess("../", "La réalisation n°$id a été supprimée.");
    }

    // CHECK FORM
    public function checkForm(string $redirectionPath): void
    {
        // check if required fields are filled
        if (!$_POST['title']) GeneralController::redirectWithError($redirectionPath, 'Le titre est obligatoire.');
        if (!$_POST['date-start']) GeneralController::redirectWithError($redirectionPath, 'La date de début est obligatoire.');

        // check character length
        if (strlen($_POST['title']) > 255) {
            GeneralController::redirectWithError($redirectionPath, 'Le titre ne doit pas dépasser 255 caractères.');
        }
        if ($_POST['description'] && strlen($_POST['description']) > 255) {
            GeneralController::redirectWithError($redirectionPath, 'La description ne doit pas dépasser 255 caractères.');
        }
        if ($_POST['link-web'] && strlen($_POST['link-web']) > 255) {
            GeneralController::redirectWithError($redirectionPath, 'Le lien web ne doit pas dépasser 255 caractères.');
        }
        if ($_POST['link-github'] && strlen($_POST['link-github']) > 255) {
            GeneralController::redirectWithError($redirectionPath, 'Le lien github ne doit pas dépasser 255 caractères.');
        }

        // check if statut is set
        if ($_POST['isActive'] !== '0' && $_POST['isActive'] !== '1') {
            GeneralController::redirectWithError($redirectionPath, 'Le statut n\'est pas défini.');
        }

        // check if date end is set
        if ($_POST['date-end']) {
            // check if date start < date end
            if (strtotime($_POST['date-start']) > strtotime($_POST['date-end'])) {
                GeneralController::redirectWithError($redirectionPath, 'La date de fin ne peut être situé avant la date de début.');
            }
        }
    }

    // GET SKILLS FROM A PROJECT
    public function loadSkillsFromProject(Project $project)
    {
        // $sql = "
        //     SELECT * FROM project
        //     WHERE id_project = :id";
        $sql = "
            SELECT skill.id_skill, skill.title, skill.image
            FROM skill
            INNER JOIN project_skill ON project_skill.id_skill = skill.id_skill
            INNER JOIN project ON project.id_project = project_skill.id_project
            WHERE project.id_project = :id_project
        ";
        $statement = DatabaseConnection::getConnection()->prepare($sql);
        $statement->bindParam(":id_project", $project->id_project, PDO::PARAM_INT);
        $statement->execute();
        $skills = $statement->fetchAll(PDO::FETCH_CLASS, Skill::class);
        return $skills;
    }
}
