<?php

include 'color.php';
/**
 *
 */
class Game
{
  private $numberOfPlayer = 1;
  private $arrayColor = ['blue', 'red', 'green'];
  private $Player1;
  private $Player2;
  private $Player3;
  private $turn = 0;
  private $size;
  private $firestick;
  private $firestickEcho;
  private $looser;
  private $hard = false;

  public function __construct() {
    system('clear');

    $json = file_get_contents("data.json");
    $jsonIterator = new RecursiveIteratorIterator
    (
      new RecursiveArrayIterator(json_decode($json, TRUE)), RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($jsonIterator as $key => $val) {
      if ($key == 'Player1') {
        $this->Player1['name'] = $val['name'];
        $this->Player1['color'] = $val['color'];
      }
      elseif ($key == 'Player2') {
        $this->Player2['name'] = $val['name'];
        $this->Player2['color'] = $val['color'];
      }
      elseif ($key == 'Player3') {
        $this->Player3['name'] = $val['name'];
        $this->Player3['color'] = $val['color'];
      }
    }
    $key = false;
    do {
      $keypress = readline("How many player Humain ? (betwen 1-3):\n");
      if ($keypress == 1|| $keypress == 2 || $keypress == 3) {
        $key = true;
      }
      else {
        echo "Error: invalid  input (wanted 1, 2 or 3)\n";
      }
    } while (!($key));
    $this->numberOfPlayer = $keypress;
    if ($this->numberOfPlayer == 1) {
      $key = false;
      do {
        $keypress = readline("Super Hard Computeur ? (pres Y) for \"Yes\", (pres n) for \"No\" :");
        $keypress = strtoupper($keypress);
        if ($keypress == 'N'|| $keypress == 'Y') {
          $key = true;
        }
        else {
          echo "Error: invalid  input (wanted Y or N)\n";
        }
      } while (!($key));
    }
    if ($keypress == 'Y') {
      $this->hard = true;
    }
    $key = false;
    do {
      $keypress = readline("How many firestick you want for this game ?(betwen 11-99):\n");
      if ($keypress < 11 || $keypress > 99 && !(is_numeric($keypress))) {
        echo "Error: invalid  input (wanted number between 11 and 99)\n";
        $key = false;
      }
      else {
        $key = true;
      }
    } while (!($key));
    $this->numberOfStick = $keypress;
    $key = false;
    do {
      $keypress = readline("Want to load config ? (pres Y) for \"Yes\", (pres n) for \"No\" :");
      $keypress = strtoupper($keypress);
      if ($keypress == 'N'|| $keypress == 'Y') {
        $key = true;
      }
      else {
        echo "Error: invalid  input (wanted Y or N)\n";
      }
    } while (!($key));
    if ($keypress == 'Y') {
      // TODO: go play
    }
    else {
      $arrayColorCopy = $this->arrayColor;
      for ($i=1; $i <= $this->numberOfPlayer; $i++) {
        $key = false;
        do {
          $keypress = readline("name of Player $i ?:\n");
          $keypress = ucfirst($keypress);
          $ok = readline("name of Player $i is ->$keypress. Are you sure ? (pres Y) for \"Yes\", (pres another) for \"No\" :\n");
          $ok = strtoupper($ok);
          if ($ok == 'Y') {
            $key = true;
          }
        } while (!($key));
        $target = 'Player' . $i;
        $this->$target['name'] = $keypress;
        do {
          $key = false;
          echo "Colors availables :";
          foreach ($this->arrayColor as $value) {
            echo " -$value ";
          }
          echo "\n";
          $keypress = readline("Color of Player -> ". $this->$target['name']." ?:\n");
          $keypress = lcfirst($keypress);
          foreach ($arrayColorCopy as $value) {
            if ($keypress == $value) {
              $key = true;
              unset($arrayColorCopy[array_search($value, $arrayColorCopy)]);
            }
          }
        } while (!($key));
        $this->$target['color'] = $keypress;
      }
      if ($this->numberOfPlayer == 2) {
        $color = array_rand($arrayColorCopy, 1);
        $this->Player3['color'] = $arrayColorCopy[$color];
      }
      elseif ($this->numberOfPlayer == 1) {
        $color = array_rand($arrayColorCopy, 1);
        $this->Player2['color'] = $arrayColorCopy[$color];
        unset($arrayColorCopy[$color]);
        $color = array_rand($arrayColorCopy, 1);
        $this->Player3['color'] = $arrayColorCopy[$color];
      }
      $array = Array(
          "Player1" => Array (
              "name" => $this->Player1['name'],
              "color" => $this->Player1['color']
          ),
          "Player2" => Array (
              "name" => $this->Player2['name'],
              "color" => $this->Player2['color']
          ),
          "Player3" => Array (
            "name" => $this->Player3['name'],
            "color" => $this->Player3['color']
          )
      );
      $json = json_encode(array('data' => $array));
      file_put_contents("data.json", $json);
    }
        system('clear');
	}

  public function init(){
    $this->firestickEcho[0] = str_repeat(". ", $this->numberOfStick);
    $this->firestickEcho[1] = str_repeat("| ", $this->numberOfStick);
    $this->firestick = str_repeat("|", $this->numberOfStick);
    $this->size = substr_count($this->firestick, '|');

    $this->turnPlayer('true');
  }

  private function my_echoColored($string){
    $colors = new Colors();
    $target = 'Player'. $this->turn;
    $color = $this->$target['color'];
    return $colors->getColoredString($string, $color);
  }
  private function my_echo(){
    $colors = new Colors();
    echo "\n";
    echo $colors->getColoredString($this->firestickEcho[0]."\n", 'red');
    echo $this->firestickEcho[1];
    echo "  (".$this->size.")\n\n";
  }

  private function maj($pick){
      $nbr = $pick + $pick;
      $this->firestickEcho[0] = substr($this->firestickEcho[0], 0, -$nbr);
      $this->firestickEcho[1] = substr($this->firestickEcho[1], 0, -$nbr);
  }

  private function turnOf(){
    $this->turn = ($this->turn + 1);
    if ($this->turn > $this->numberOfPlayer) {
      $this->turn = 1;
    }
    $target = 'Player'. $this->turn;
    if ($this->looser == $this->$target['name']) {
      $this->turnOf();
    }
  }

  private function turnPlayer($lastTurnOK){
    if ($lastTurnOK == 'true') {
      $this->turnOf();
    }
    $target = 'Player'. $this->turn;
    $took = null;
    do {
      $this->my_echo();
      $colors = new Colors();
      echo $colors->getColoredString("Your turn ".$this->$target['name']." : ", $this->$target['color']);
      $took = readline();
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
      echo $this->$target['name']." removed $took match(es)\n";

      if ($this->numberOfPlayer > 1) {
        $this->isLoosedMulti();
        $this->turnPlayer('true');
      }
      else {
        $this->isLoosed();
        $this->turnComputeur();
      }
    }
    else {
      $this->turnPlayer('false');
    }
  }

  private function turnComputeur(){
    $this->my_echo();
    $this->turn = 2;
    $rest = $this->size;
    if ($rest > 11) {
      $pick = rand(1, 3);
    }
    if ($rest == 8 || $rest == 4) { //pick 3
      $pick = 3;
    }
    elseif ($rest == 11 || $rest == 7 || $rest == 3) { //pick 2
      $pick = 2;
    }
    elseif ($rest == 10 || $rest == 9 || $rest == 6 || $rest == 5 || $rest == 2 || $rest == 1) { //pick 1
      $pick = 1;
    }
    if ($this->hard == false) {
      $easy = rand(1, 2);
      if ($easy == 1 && $pick == 2) {
        $easy = rand(1, 2);
        if ($easy == 1) {
          $pick--;
          if (($rest - $pick) >= 0) {
            $pick++;
          }
        }
        else {
          $pick++;
          if (($rest - $pick) >= 0) {
            $pick--;
          }
        }
      }
    }
    $this->firestick = substr($this->firestick, 0, -$pick);
    $this->size = $rest - $pick;
    $this->maj($pick);
    echo "AI's turn ...\n";
    if ($this->hard == true) {
      echo "AI's thinging hard .";
      for ($i=0; $i < 3; $i++) {
        sleep(1);
        echo ".";
      }
    }
    else {
      echo "AI's thinging";
      for ($i=0; $i < 2; $i++) {
        sleep(1);
        echo ".";
      }
    }

    echo "\nAI removed $pick match(es)\n";
    $this->isLoosed();
    $this->turnPlayer('true');
  }

  private function isLoosedMulti(){
    $rest = $this->size;
    if ($rest == 0) {
      $target = 'Player'. $this->turn;
      echo $this->$target['name']." You lost, too bad...\n";
      if ($this->looser != '' && $this->numberOfPlayer == 3) {
        for ($i=1; $i <= $this->numberOfPlayer; $i++) {
          $winner = 'Player'. $i;
          if ($this->$winner['name'] != $this->looser && $this->$winner['name'] != $this->$target['name']) {
              echo "GG you win ". $this->$winner['name']." ! ! !\n";
              exit();
          }
        }
      }
      elseif($this->numberOfPlayer == 2) {
        $this->looser = $this->$target['name'];
        for ($i=1; $i <= $this->numberOfPlayer; $i++) {
          $winner = 'Player'. $i;
          if ($this->$winner['name'] != $this->looser) {
              echo "GG you win ". $this->$winner['name']." ! ! !\n";
              exit();
          }
        }
      }
      else {
        $this->looser = $this->$target['name'];
        $this->init();
      }
    }
  }

  private function isLoosed(){
    $rest = $this->size;
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


  private function verif($pick){
    $rest = $this->size;
    if (($rest - $pick) >= 0) {
      $this->firestick = substr($this->firestick, 0, -$pick);
      $this->maj($pick);
      $this->size = $rest - $pick;
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
