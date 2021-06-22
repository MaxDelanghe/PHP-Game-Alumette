<?php
/**
 *
 */
class Game
{
  private $firestick;
  private $turn;


  public function __construct() {
	}

  public function init(){
    $this->firestick = array('|||||||||||');
    print_r($this->firestick);
    $this->turnPlayer();
  }
  private function turnPlayer(){
    $this->turn = 1;
    $took = null;
    do {
      echo $this->firestick[0]."\n";
      $took = readline("Your turn: \n$ ");
      if (!(is_numeric($took) && $took > 0 )) {
        echo "Error: invalid  input (positive number expected)\n";
      }
      elseif($took > 3) {
        echo "Error: you cannot remove more than 3 matches per turn...\n";
      }
      elseif ((strlen ( $took ) > 1)) {
        $took = null;
        echo "Error: invalid input (positive number expected)\n";
      }
    } while (!(is_numeric($took) && $took > 0 && $took <= 3 ));
    $numberRemovedIsOk = $this->verif($took);
    if ($numberRemovedIsOk) {
      echo "Matches : $took\n";
      echo "Player removed $took match(es)\n";
      $this->isLoosed(); // TODO: a faire
      $this->turnComputeur();
    }
    else {
      $this->turnPlayer();
    }
  }

  private function turnComputeur(){
    echo $this->firestick[0]."\n";

    $this->turn = 2;
    $rest = strlen($this->firestick[0]);
    if ($rest == 55 || $rest == 8 || $rest == 4) { //pick 3
      $pick = 3;
    }
    elseif ($rest == 11 || $rest == 7 || $rest == 3) { //pick 2
      $pick = 2;
    }
    elseif ($rest == 10 || $rest == 9 || $rest == 6 || $rest == 5 || $rest == 2 || $rest == 1) { //pick 1
      $pick = 1;
    }
    $this->firestick[0] = substr($this->firestick[0], 0, -$pick);
    echo "AI's turn ...\n";
    echo "AI removed $pick match(es)\n";
    $this->isLoosed();
    $this->turnPlayer();
  }

  private function isLoosed(){
    $rest = strlen($this->firestick[0]);
    if ($rest == 0) {
      if ($this->turn == 2) { //computeur loose
        echo "I lost...  snif... but I'll get you next time!!\n";
      }
      else { //player loose
        echo "You lost, too bad...\n";
      }
      exit();
    }
  }


  private function verif($nbr){
    $rest = strlen($this->firestick[0]);
    if (($rest - $nbr) >= 0) {
      $this->firestick[0] = substr($this->firestick[0], 0, -$nbr);
      return true;
    }
    else {
      echo "Error: not enough matches on this line\n";
      return false;
    }
  }
}

$Game = new Game();
$Game->init();
