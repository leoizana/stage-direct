<?php
$perso1 = new Guerrier();
$perso1->setFirstname('Emeric');
$perso1->attaquer();

$perso1->setFirstname('Emeric')->attaquer();

class Guerrier {
    private $firstname;
    public function setfirstname($firstname) {
        $this->firstname = $firstname;
        return $this;
    }
    public function attaquer(){
        print('Le guerrier attaque');
    }
}