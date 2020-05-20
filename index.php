<?php
include 'config/autoload.php';
session_start();

if (isset($_GET['deconnexion'])){
    session_destroy();
    header('Location: .');
    exit();
}

if (isset($_SESSION['perso'])){// si la session perso existe
    $perso = $_SESSION['perso']; // retourne l objet
}
include 'config/db.php';
/*$pdo = new PDO('mysql:dbname=jeu_combat;host=127.0.0.1', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);*/

$manager = new PersonnagesManager($pdo);

if (isset($_SESSION['perso'])){
    $perso = $_SESSION['perso'];
}


if (isset($_POST['creer']) && isset($_POST['nom'])){ // si on veut creer un perso
    $perso = new Personnage(['nom' => $_POST['nom']]);

    if (!$perso->nomValide()){// si nom pas valide
        $message = 'Le nom choisi est invalide.';// affiche le message
        unset($perso);// detruit la variable

    }elseif ($manager->exists($perso->nom())){
        $message = 'Le nom du personnage est deja pris';
        unset($perso);

    }else{
        $manager->create($perso);
    }

}
elseif (isset($_POST['utiliser']) && isset($_POST['nom'])){// si on veut utiliser un perso existant
    if ($manager->exists($_POST['nom'])){// s il existe
        $perso = $manager->get($_POST['nom']); // va recup le perso et l affiche via requete dans la function get() dans class PersonnageManager

    }else{
        $message = 'Ce personnage n\'existe pas, nous vous invitons a le créer !';

    }

}
elseif (isset($_GET['frapper'])){// si perso a frapper selectionner
    if (!isset($perso)){
        $message = 'Merci de créer un personnage ou de vous identifier.';
    }else{
        if (!$manager->exists((int) $_GET['frapper'])){
            $message = 'Le personnage que vous voulez frapper n\'existe pas !';
        }else{
            $persoAFrapper = $manager->get((int) $_GET['frapper']);
            $retour = $perso->frapper($persoAFrapper); // On stocke dans $retour les éventuelles erreurs ou messages que renvoie la méthode frapper.

            switch ($retour){
                case Personnage::CEST_MOI :
                    $message = 'Mais... pourquoi voulez-vous vous frapper ???';
                    break;

                case Personnage::PERSONNAGE_FRAPPE :
                    $message = 'Le personnage a bien été frappé !';

                    $manager->update($perso);
                    $manager->update($persoAFrapper);

                    break;

                case Personnage::PERSONNAGE_TUE :
                    $message = 'Vous avez tué ce personnage !';

                    $manager->update($perso);
                    $manager->delete($persoAFrapper);

                    break;
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Jeu Combat</title>
</head>
<body>
<p>Nombre de personnages créés : <?= $manager->count()?></p><!--affiche le resultat du Count() de personnages deja existant-->
<?php
if (isset($message)){//si message a afficher
    echo '<p>', $message, '</p>';// affiche le message
}

if (isset($perso)){
?>
    <p><a href="?deconnexion=1">Déconnexion</a></p>
    <fieldset>
        <legend>Mes informations</legend>
        <p>
            Nom : <?= htmlspecialchars($perso->nom()) ?><br>
            Dégats : <?= $perso->degats() ?>
        </p>
    </fieldset>

    <fiedset>
        <legend>Qui frapper ?</legend>
        <p>
            <?php
            $perso = $manager->getList($perso->nom());

            if (empty($personnages)){
                echo 'Personne a frapper !';

            }else{
                foreach ($personnages as $unPerso) {
                    echo '<a href="?frapper=', $unPerso->id(), '">', htmlspecialchars($unPerso->nom()), '</a> (dégâts : ', $unPerso->degats(), ')<br />';
                }
            }
            ?>
        </p>
    </fiedset>
<?php
}else{
?>
<form action="" method="post">
    <p>
        Nom : <input type="text" name="nom" maxlength="50" />
        <input type="submit" value="Créer ce personnage" name="creer" />
        <input type="submit" value="Utiliser ce personnage" name="utiliser" />
    </p>
</form>
<?php
}
?>
</body>
</html>
<?php
if (isset($perso)){// si perso create on stock dans $_SESSION
    $_SESSION['perso'] = $perso;

}
