<?php
class  Magicien extends Personnage{
    public function lancerUnsort(Personnage $perso){
        if ($this->degats >= 0 && $this->degats <= 100){
            $this->degats += $this->degats * 2;
        }else{
            echo 'PPL';
        }

        if ($perso->id == $this->id){
            return self::CEST_MOI;
        }

        if ($this->degats >= 100){
            return self::PERSONNAGE_TUE;
        }
        return self::PERSONNAGE_FRAPPE;

    }
}
