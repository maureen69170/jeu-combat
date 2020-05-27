<?php
require_once 'config/db.php';

class PersonnagesManager
{
    private $_pdo;
    public function __construct($pdo)
    {
        $this->setPDO($pdo);
    }

    public function create(Personnage $perso)//creation d un nouveau perso
    {
        $personageStatement = $this->_pdo->prepare('INSERT INTO personnages(nom , niveau, experience, strength, type) VALUES (:nom, :niveau, :experience, :strength, :type)');// preparation de la requete insert
        $personageStatement->bindValue(':nom', $perso->nom());//assigne les valeurs pour le nom du personnage //bindValue() = associe une valeur a un parametre
        $personageStatement->bindValue(':niveau', $perso->niveau());
        $personageStatement->bindValue(':experience', $perso->experience());
        $personageStatement->bindValue(':strength', $perso->strength());
        $personageStatement->bindValue(':type', $perso->type());
        $personageStatement->execute();

        //hydratation
        $perso->hydrate([
            'id' => $this->_pdo->lastInsertId(),
            'degats' => 0,
            'experience' => 0,
            'niveau' => 1,
            'strength' => 0

        ]);
    }

    public function count()//execute une requete COUNT() puis retourne le nb de resultat
    {
        return $this->_pdo->query('SELECT COUNT(*) FROM personnages')->fetchColumn();// fetchColumn() = retourne une colonne
    }

    public function delete(Personnage $perso)// execute la requete qui efface un personnage
    {
        $this->_pdo->exec('DELETE FROM personnages WHERE id = '.$perso->id());
    }

    public function exists($info)
    {
        //is_int () verif si le type de la variable est bien un int.
        if (is_int($info)){// si c est un entier et qu on a un perso avec id $info
            return (bool) $this->_pdo->query('SELECT COUNT(*) FROM personnages WHERE id = '.$info)->fetchColumn(); // alors on execute COUNT() et retourne un boolean
        }

        //verif si le nom existe ou pas
        $selectNamePersoStatement = $this->_pdo->prepare('SELECT COUNT(*) FROM personnages WHERE nom = :nom');
        $selectNamePersoStatement->execute([':nom' => $info]);

        return (bool) $selectNamePersoStatement->fetchColumn();
    }

    public function get($info){
        if (is_int($info)){// si c est un entier on recup le perso avec identifiant
            $selectPersonnageStatement = $this->_pdo->query('SELECT * FROM personnages WHERE  id = '.$info);// on execute le select
            $perso = $selectPersonnageStatement->fetch(PDO::FETCH_ASSOC);
            /*return new Personnage($donnees);*/// retourne un objet Personnage

        }else{// pour recup le perso avec son nom
            $selectNamePersonnagePrepare = $this->_pdo->prepare('SELECT * FROM personnages WHERE nom = :nom');
            $selectNamePersonnagePrepare->execute([':nom' => $info]);
            $perso = $selectNamePersonnagePrepare->fetch(PDO::FETCH_ASSOC);
        }
        switch ($perso['type']){
            case 'magicien' : return new Magicien($perso);
            case 'guerrier' : return new Guerrier($perso);
            case 'archer' : return new Archer($perso);
            default: return null;

        }
    }

    public function getList($nom){
        $persos = [];
        // affiche la liste de persos dont le nom n est pas $nom
        $affichageListPersos = $this->_pdo->prepare('SELECT * FROM personnages WHERE nom <> :nom ORDER BY nom');//<> c est different de
        $affichageListPersos->execute([':nom' => $nom]);

        //tableau d instances de Personnage
        while ($donnees = $affichageListPersos->fetch(PDO::FETCH_ASSOC)){
            /*$persos[] = new Personnage($donnees);*/
            switch ($donnees['type']){
                case 'magicien': $persos[] = new Magicien($donnees); break;
                case 'guerrier': $persos[] = new Guerrier($donnees); break;
                case 'archer': $persos[] = new Archer($donnees); break;
            }
        }
        return $persos;
    }

    public function update(Personnage $perso){
        if ($perso->experience() >= 100){
            $perso->setExperience(0);
            $perso->setNiveau(1);
            $perso->setStrength($perso->niveau());
        }
        $updatePersonnageStatement = $this->_pdo->prepare('UPDATE personnages SET degats = :degats, niveau = :niveau, experience = :experience, strength = :strength  WHERE id = :id');
        $updatePersonnageStatement->bindValue(':degats', $perso->degats(), PDO::PARAM_INT);
        $updatePersonnageStatement->bindValue(':niveau', $perso->niveau(), PDO::PARAM_INT);
        $updatePersonnageStatement->bindValue(':experience', $perso->experience(), PDO::PARAM_INT);
        $updatePersonnageStatement->bindValue(':strength', $perso->strength(), PDO::PARAM_INT);
        $updatePersonnageStatement->bindValue(':id', $perso->id(), PDO::PARAM_INT);
        $updatePersonnageStatement->execute();
    }

    public function setPdo(PDO $pdo)
    {
        $this->_pdo = $pdo;
    }
}