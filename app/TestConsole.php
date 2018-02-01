<?php

class TestConsole extends Console
{
  const FOREIGN_TO_NATIVE = 'FN';
  const NATIVE_TO_FOREIGN = 'NF';

  private $querySet = null;
  private $mode = 0;

  public function __construct($querySet, $mode)
  {
    $this->querySet = $querySet;
    $this->mode = $mode;
  }

  public function run($phrasesMaxCount)
  {
    $this->start();

    switch ($this->mode) {
      case self::FOREIGN_TO_NATIVE:
        $phrases = $this->querySet->selectPhrasesFN($phrasesMaxCount);
        break;

      case self::NATIVE_TO_FOREIGN:
        $phrases = $this->querySet->selectPhrasesNF($phrasesMaxCount);
        break;

      default:
        return;
    }

    $phrasesCount = count($phrases);

    for ($index = 0; $index < $phrasesCount; $index++) {
      $phrase = $phrases[$index];
      $success = $this->showQuestion($phrase, $index + 1, 1);

      if (!$success) {
        $this->showQuestion($phrase, $index + 1, 2);
      }
    }

    $this->end();
  }

  private function start()
  {
    echo $this->bold('TEST MODE ON') . PHP_EOL;
    echo $this->bold('--------------') . PHP_EOL;
  }

  private function showQuestion($phrase, $questionNumber, $tryNumber)
  {
    switch ($this->mode) {
      case self::FOREIGN_TO_NATIVE:
        return $this->showQuestionFN($phrase, $questionNumber, $tryNumber);

      case self::NATIVE_TO_FOREIGN:
        return $this->showQuestionNF($phrase, $questionNumber, $tryNumber);

      default:
        return false;
    }
  }

  private function showQuestionFN($phrase, $questionNumber, $tryNumber)
  {
    $tryText = ($tryNumber > 1) ? "try {$tryNumber}" : '';

    $question = $phrase['foreign_phrase'];
    $sample = mb_strtolower(trim($phrase['native_phrase']));
    $pronunciation = $phrase['pronunciation'];

    echo $this->bold('Number: ') . $questionNumber . ' ' . $tryText . PHP_EOL;
    echo $this->bold('Question: ') . $question . PHP_EOL;
    echo $this->bold('Pronunciation: ') . $pronunciation . PHP_EOL;

    if ($phrase['instances_cnt'] > 1 || $tryNumber > 1) {
      $boundary = $this->makeBoundary($phrase['native_phrase']);
      echo $this->bold('Boundary: ') . $boundary . PHP_EOL;
    }

    echo $this->bold('Translation:');

    $translation = mb_strtolower(trim(readline(' ')));

    if ($sample !== $translation) {
      echo $this->bold('Result: ') . $this->red('wrong') . PHP_EOL;
      echo PHP_EOL;
      return false;
    }

    echo $this->bold('Result: ') . $this->green('correct') . PHP_EOL;
    echo PHP_EOL;

    $this->writeTest($phrase);
    return true;
  }

  private function showQuestionNF($phrase, $questionNumber, $tryNumber)
  {
    $tryText = ($tryNumber > 1) ? "try {$tryNumber}" : '';

    $question = $phrase['native_phrase'];
    $sample = mb_strtolower(trim($phrase['foreign_phrase']));
    $pronunciation = $phrase['pronunciation'];

    echo $this->bold('Number: ') . $questionNumber . ' ' . $tryText . PHP_EOL;
    echo $this->bold('Question: ') . $question . PHP_EOL;

    if ($phrase['instances_cnt'] > 1 || $tryNumber > 1) {
      $boundary = $this->makeBoundary($phrase['foreign_phrase']);
      echo $this->bold('Boundary: ') . $boundary . PHP_EOL;
    }

    echo $this->bold('Translation:');

    $translation = mb_strtolower(trim(readline(' ')));

    if ($sample !== $translation) {
      echo $this->bold('Result: ') . $this->red('wrong') . PHP_EOL;
      echo PHP_EOL;
      return false;
    }

    echo $this->bold('Pronunciation: ') . $pronunciation . PHP_EOL;
    echo $this->bold('Result: ') . $this->green('correct') . PHP_EOL;
    echo PHP_EOL;

    $this->writeTest($phrase);
    return true;
  }

  private function makeBoundary($word)
  {
    $length = mb_strlen($word);
    $newWord = '';

    for ($index = 0; $index < $length; $index++) {
      $symbol = mb_substr($word, $index, 1);
      $newWord .= (($index === 0 || $index === $length - 1) ? $symbol : '*');
    }
    return $newWord;
  }

  private function writeTest($phrase)
  {
    if ($phrase['test_exists'] > 0) {
      $this->querySet->updateTest($phrase, $this->mode);
    }
    else {
      $this->querySet->insertTest($phrase, $this->mode);
    }
  }

  private function end()
  {
    echo $this->bold('---------------') . PHP_EOL;
    echo $this->bold('TEST MODE OFF') . PHP_EOL;
  }
}
