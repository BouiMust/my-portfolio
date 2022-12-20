<?php
// Ce fichier permet de :
// - logger l'user en récupérant les champs du formulaire admin/index.php
// - deconnecter l'user en cliquant le bouton 'deconnecter'

session_start();
require './authentificationAdmin.php';

// Aucune action n'est programmée à la base (on initialise)
$action = '';

// Vérifie si la clé 'action' est présente dans $_POST (contient la nature de l'action dans la value du input hidden)
if (isset($_POST['action'])) {
    // Sauvegarde la valeur dans $action
    $action = $_POST['action'];
}

// vérifie l'action envoyé par l'utilisateur
switch ($action) {
        // login correspond au hidden input avec la value 'login'
    case 'login':
        login();
        break;
    case 'logout':
        logout();
        break;
    case 'read':
        readUser();
        break;
    case 'update':
        updateUser();
        break;
    case 'delete':
        deleteUser();
        break;
    default;
        break;
}

// FONCTION LOGIN (quand l'admin se connecte)
function login()
{
    // Si un des champs est vide
    if (!$_POST['email'] || !$_POST['password']) {
        $_SESSION['error'] = 'Veuillez remplir tous les champs.';
        header('Location: ../admin/index.php');
        exit();
    }

    // Fichier databaseConnexion.php requis pour la connexion
    require './databaseConnexion.php';

    // formatage du mail
    $email = trim(strtolower($_POST['email']));

    // Vérifie l'email de l'admin (unique). le champ email possède une clé unique dans la BDD
    // Préparation de la requête : vérifie si l'email est présente 
    $sql = "
    SELECT * FROM user WHERE email = '$email'
    ";

    // Execution de la requête avec les params de connexion et sauvegarde la reponse dans $query
    $query = mysqli_query($connexion, $sql) or exit(mysqli_error($connexion));

    // Traitement des données : vérification de l'email dans la BDD
    // On utilise mysqli_num_row qui compte le nombre de lignes dans la table user

    // S'il n'y a pas d'utilisateur dans la BDD 
    if (mysqli_num_rows($query) < 1) {
        // message alerte
        $_SESSION['error'] = 'No user matches.';
        // redirection
        header('Location: ../admin/index.php');
        exit();
    }

    // Sinon on met sous forme de tableau associatif les données de l'admin récupérés
    $user = mysqli_fetch_assoc($query);

    // Vérification password
    if (!password_verify(trim($_POST['password']), $user['password'])) {
        // message alerte
        $_SESSION['error'] = 'Incorrect password.';
        // redirection
        header('Location: ../admin/index.php');
        exit();
    }

    // Vérification role (1 = Admin)
    if ((int)$user['role'] !== 1) {
        // message alerte
        $_SESSION['error'] = 'Access denied.';
        // redirection
        header('Location: ../index.php');
        exit();
    }

    // Sinon la connexion est réussie
    // on sauvegarde des données dans la session (qui permettent de donner accès au back-office)
    // puis redirige l'admin au tableau de bord
    $_SESSION['first_name'] = $user['first_name'];
    $_SESSION['isLog'] = true;
    $_SESSION['role'] = $user['role'];
    $_SESSION['message'] = "Hello {$user['first_name']} {$user['last_name']}.";
    header('Location: ../admin/dashboardAdmin.php');
    exit();
}


// FONCTION LOGOUT (quand l'admin se déconnecte)
function logout()
{
    // supprime la session courante et toutes les données en session
    session_destroy();
    session_start();
    // message d'alerte
    $_SESSION['message'] = 'You are offline.';
    // redirection
    header('Location: ../index.php');
    exit();
}

// FONCTION READ (Récupérer 1 user)
function readUser()
{
    header('Location: ../admin/detailUser.php');
    exit;
}

// FONCTION UPDATE (Modifier 1 user)
function updateUser()
{
    echo 'update one user';
    exit;
}

// FONCTION DELETE (Supprimer 1 user)
function deleteUser()
{
    echo 'delete one user';
    exit;
}
