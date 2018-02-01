<?php

class EditorConsole extends Console
{
  private $querySet = null;

  public function __construct($querySet)
  {
    $this->querySet = $querySet;
  }

  public function run()
  {
    $this->start();

    do {
      $this->showSequence();
      $next = $this->enterNextSequence();
    }
    while ($next);

    $this->end();
  }

  private function start()
  {
    echo $this->bold('EDITOR MODE ON') . PHP_EOL;
    echo $this->bold('--------------') . PHP_EOL;
  }

  private function showSequence()
  {
    echo $this->bold("Enter a sequence 'phrase|pronunciation|translation|hint':");
    $sequence = trim(readline(' '));
    echo PHP_EOL;

    $parts = explode('|', $sequence);

    if (count($parts) < 3) {
      echo $this->red('Sequence must includes at least 3 segments') . PHP_EOL;
      return;
    }

    $phrase = trim($parts[0]);
    $pronunciation = trim($parts[1]);
    $translation = trim($parts[2]);
    $hint = (count($parts) > 3) ? trim($parts[3]) : '';
    $this->querySet->insertPhrase($phrase, $pronunciation, $translation, $hint);
  }

  private function enterNextSequence()
  {
    echo $this->bold('Enter a next sequence? (y|n):');
    $answer = trim(readline(' '));
    echo PHP_EOL;

    $boolAnswer = ($answer === 'y');
    return $boolAnswer;
  }

  private function end()
  {
    echo $this->bold('---------------') . PHP_EOL;
    echo $this->bold('EDITOR MODE OFF') . PHP_EOL;
  }  
}
