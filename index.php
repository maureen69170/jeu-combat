<?php
include 'config/autoload.php';
session_start();

if (isset($_GET['deconnexion'])){
    session_destroy();
    header('Location: .');
    exit();
}


include 'config/db.php';
/*$pdo = new PDO('mysql:dbname=jeu_combat;host=127.0.0.1', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);*/

$manager = new PersonnagesManager($pdo);

if (isset($_SESSION['perso'])){// si la session perso existe
    $perso = $_SESSION['perso']; // retourne l objet
}


if (isset($_POST['creer']) && isset($_POST['nom'])){ // si on veut creer un perso
    /*$perso = new Personnage(['nom' => $_POST['nom']]);*/
    switch ($_POST['type']){
        case 'magicien' :
            $perso = new Magicien(['nom' => $_POST['nom']]);
            break;
        case 'guerrier' :
            $perso = new Guerrier(['nom' => $_POST['nom']]);
            break;
        case 'archer' :
            $perso = new Archer(['nom' => $_POST['nom']]);
            break;
        default :
            $message = 'Le type du personnage est invalide';
            break;
    }

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
                    $manager->update($persoAFrapper, $perso->strength());

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
elseif (isset($_GET['ensorceler'])){
    if (!isset($perso)){
        $message = 'Merci de créer un personnage ou de vous identifier.';
    }else{
        //verif que perso est bient typé Maj
        if ($perso->type() != 'magicien'){
            $message = 'Seuls las Magiciens peuvent en lancer un sort sur les personnages';
        }else{
            if (!$manager->exists((int) $_GET['ensorceler'])){
                $message = 'Le personnage que vous voulez frapper n\'existe pas !';
            }else{
                $persoAEnsorceler = $manager->get((int) $_GET['ensorceler']);
                $retour = $perso->lancerUnSort($persoAEnsorceler);

                switch ($retour){
                    case Personnage::CEST_MOI :
                        $message = 'Mais... pourquoi voulez-vous vous ensorceler ???';
                        break;

                    case Personnage::PERSONNAGE_ENSORCELE :
                        $message = 'Le personnage a bien été ensorcelé !';
                        break;

                    case Personnage::PAS_DE_MAGIE :
                        $message = 'Vous n\'avez pas de magie !';
                        break;

                }
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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh"
          crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<p>Nombre de personnages créés : <?= $manager->count()?></p><!--affiche le resultat du Count() de personnages deja existant-->
<?php
if (isset($message)){//si message a afficher
    echo '<p>', $message, '</p>';// affiche le message
}

if (isset($perso)){
?>
    <p><a href="?deconnexion=1" class="btn btn-primary">Déconnexion</a></p>
    <fieldset>
        <legend>Mes informations</legend>
        <p>
            Nom : <?= htmlspecialchars($perso->nom()) ?><br>
            Type : <?= $perso->type() ?><br />
            Dégats : <?= $perso->degats() ?><br>
            Niveau : <?= $perso->niveau() ?><br>
            Experience : <?= $perso->experience() ?><br>
            Force : <?= $perso->strength() ?><br />
            <?php
            switch ($perso->type()){
                case 'magicien' :
                    echo 'Magie : ';
                    break;

                case 'guerrier' :
                    echo 'Protection : ';
                    break;

                case 'archer' :
                    echo 'Fleche : ';
                    break;
            }
            echo $perso->degats();
            ?>

        </p>
    </fieldset>

    <div class="container">
    <fiedset>
        <div class="row">
            <legend>Qui frapper ?</legend>



                            <?php
                            $retourPersos = $manager->getList($perso->nom());
                            /*var_dump($perso);*/
                            if (empty($retourPersos)){
                                echo 'Personne a frapper !';

                            }else{
                                foreach ($retourPersos as $unPerso) {
                                    echo'<div class="col-4">
                                            <div class="card text-white bg-dark mb-3" style="max-width: 18rem;">
                                                <div class="card-header">
                                                    <h5>',htmlspecialchars($unPerso->nom()),'</h5>
                                                </div>
                                                <div class="card-body">
                                                    <p class="card-text">
                                                        Type : ', $unPerso->type(), '<br> 
                                                        Degats: ', $unPerso->degats(), '<br> 
                                                        Niveau : ', $unPerso->niveau(), '<br> 
                                                        Experience : ', $unPerso->experience(), '<br> 
                                                        Force : ', $unPerso->strength(), '<br> 
                                                    </p>';
                                                /*var_dump($unPerso);*/

                                                echo '<button class="btn btn-danger"><a href="?frapper=',$unPerso->id(), '">Frapper</a></button> <br />';
                                                if ($perso->type() == 'magicien'){
                                                    echo ' | <a href="?ensorceler=', $unPerso->id(), '">Lancer un sort</a>';
                                                }
                                        echo '</div>
                                            </div>
                                        </div>';
                                }
                            }
                            ?>

        </div>
    </fiedset>
    </div>
<?php
}else{
?>

<form action="" method="post">
    <p>
        Nom : <input type="text" name="nom" maxlength="50" />
        <input type="submit" value="Créer ce personnage" name="creer" class="btn btn-primary"/>
        <label for="type">Type :</label>
        <select class="custom-select" id="inputGroupSelect01" name="type">
            <option value="magicien">Magicien</option>
            <option value="guerrier">Guerrier</option>
            <option value="archer">Archer</option>
        </select>
        <input type="submit" value="Utiliser ce personnage" name="utiliser" class="btn btn-secondary"/>
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
