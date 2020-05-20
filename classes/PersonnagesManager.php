<?php
require_once 'config/db.php';

class PersonnagesManager
{
    protected $_pdo;
    public function __construct($pdo)
    {
        $this->setPDO($pdo);
    }

    public function create(Personnage $perso)//creation d un nouveau perso
    {
        $personageStatement = $this->_pdo->prepare('INSERT INTO personnages(nom) VALUES (:nom)');// preparation de la requete insert
        $personageStatement->bindValue(':nom', $perso->nom());//assigne les valeurs pour le nom du personnage //bindValue() = associe une valeur a un parametre
        $personageStatement->execute();

        //hydratation
        $perso->hydrate([
            'id'=>$this->_pdo->lastInsertId(),
            'degats'=>0,
        ]);
    }

    public function count()//execute une requete COUNT() puis retourne le nb de resultat
    {
        return $this->_pdo->query('SELECT COUNT(*) FROM personnages')->fetchColumn();// fetchColumn() = retourne une colonne
    }

    public function delete(Personnage $perso)// execute la requete qui efface un personnage
    {
        $this->_pdo->exec('DELETE FROM personnages WHERE id ='.$perso->id());
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
            $donnees = $selectPersonnageStatement->fetch(PDO::FETCH_ASSOC);
            return new Personnage($donnees);// retourne un objet Personnage

        }else{// pour recup le perso avec son nom
            $selectNamePersonnagePrepare = $this->_pdo->prepare('SELECT * FROM personnages WHERE nom = :nom');
            $selectNamePersonnagePrepare->execute([':nom' => $info]);
            return new Personnage($selectNamePersonnagePrepare->fetch(PDO::FETCH_ASSOC));
        }
    }

    public function getList($nom){
        $persos = [];
        // affiche la liste de persos dont le nom n est pas $nom
        $affichageListPersos = $this->_pdo->prepare('SELECT * FROM personnages WHERE nom <> :nom ORDER BY nom');
        $affichageListPersos->execute([':nom' => $nom]);

        //tableau d instances de Personnage
        while ($donnees = $affichageListPersos->fetch(PDO::FETCH_ASSOC)){
            $persos[] = new Personnage($donnees);
        }
        return $persos;
    }

    public function update(Personnage $perso){
        $updatePersonnageStatement = $this->_pdo->prepare('UPDATE personnages SET degats = :degats WHERE id = :id');
        $updatePersonnageStatement->bindValue(':degats', $perso->degats(), PDO::PARAM_INT);
        $updatePersonnageStatement->bindValue(':id', $perso->id(), PDO::PARAM_INT);
    }

    public function setPdo(PDO $pdo)
    {
        $this->_pdo = $pdo;
    }
}