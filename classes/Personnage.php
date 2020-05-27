<?php
class Personnage
{
    protected $id;
    protected $degats;
    protected $nom;
    protected $experience;
    protected $niveau;
    protected $strength;
    protected $type;



    const CEST_MOI = 1; // Constante renvoyée par la méthode `frapper` si on se frappe soi-même.
    const PERSONNAGE_TUE = 2; // Constante renvoyée par la méthode `frapper` si on a tué le personnage en le frappant.
    const PERSONNAGE_FRAPPE = 3; // Constante renvoyée par la méthode `frapper` si on a bien frappé le personnage.
    const PERSONNAGE_ENSORCELE = 4; // Constante renvoyée par la méthode `lancerUnSort` (voir classe Magicien) si on a bien ensorcelé un personnage.
    const PAS_DE_MAGIE = 5; // Constante renvoyée par la méthode `lancerUnSort` (voir classe Magicien) si on veut jeter un sort alors que la magie du magicien est à 0.


    public function __construct(array $donnees)
    {
        $this->hydrate($donnees);
        $this->type = strtolower(static::class);

    }

    public function frapper(Personnage $perso)
    {
        if ($perso->id() === $this->id) {
            return self::CEST_MOI;
        }
        $this->experience += 25;
        // On indique au personnage qu'il doit recevoir des dégâts.
        // Puis on retourne la valeur renvoyée par la méthode : self::PERSONNAGE_TUE ou self::PERSONNAGE_FRAPPE
        return $perso->recevoirDegats();
    }

    public function hydrate(array $donnees)
    {
        foreach ($donnees as $key => $value) {
            $method = 'set' . ucfirst($key);

            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    public function nomValide(){
        return !empty($this->nom);
    }


    public function recevoirDegats()
    {
        $this->degats += 5;

        // Si on a 100 de dégâts ou plus, on dit que le personnage a été tué.
        if ($this->degats >= 100) {
            return self::PERSONNAGE_TUE;
        }

        // Sinon, on se contente de dire que le personnage a bien été frappé.
        return self::PERSONNAGE_FRAPPE;


    }

    public function gagnerExperience(){
        $this->setExperience($this->experience() + $this->niveau() * 5);

        if ($this->experience() >= 100){
            $this->setNiveau($this->niveau() + 1);
            $this->setExperience(0);
        }
    }





    // GETTERS //

    public function degats()
    {
        return $this->degats;
    }

    public function id()
    {
        return $this->id;
    }

    public function nom()
    {
        return $this->nom;
    }

    public function type(){
        return $this->type;
    }

    public function niveau(){
        return $this->niveau;
    }

    public function experience(){
        return $this->experience;
    }

    public function strength(){
        return $this->strength;
    }

    public function setDegats($degats)
    {
        $degats = (int)$degats;

        if ($degats >= 0 && $degats <= 100) {
            $this->degats = $degats;
        }
    }

    public  function  setId($id){
        $id = (int) $id;
        if ($id > 0){
            $this->id = $id;
        }
    }

    public function setNom($nom)
    {
        if (is_string($nom)) {
            $this->nom = $nom;
        }
    }

    public function setNiveau($niveau){
        $niveau = (int)$niveau;
        if ($niveau >=0 && $niveau <=100){
            $this->niveau += $niveau;

        }
    }

    public function setExperience($experience){
        $experience = (int)$experience;
        if ($experience >= 0 && $experience <= 100){
            $this->experience = $experience;

        }
    }

    public function setStrength($strength){
        $this->strength = $strength;
    }
}