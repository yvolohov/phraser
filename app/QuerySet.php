<?php

class QuerySet
{
  private $pdo = null;

  public function __construct($host, $db, $user, $password, $charset)
  {
    $dsn = "mysql:host={$host};dbname={$db};charset={$charset}";

    try {
      $this->pdo = new \PDO($dsn, $user, $password);
    }
    catch (\Exception $e) {
      exit($e->getMessage() . PHP_EOL);
    }
  }

  public function selectPhrasesFN($count)
  {
    $stmt = $this->pdo->prepare(
        "SELECT
        phrases.id,
        phrases.foreign_phrase,
        phrases.native_phrase,
        phrases.pronunciation,
        phrases.hint,
        IFNULL(instances.instances_cnt, 1) AS instances_cnt,
        IFNULL(tests.passages_cnt, 0) AS passages_cnt,
        IFNULL(tests.first_passage, '0000-00-00 00:00:00') AS first_passage,
        IFNULL(tests.last_passage, '0000-00-00 00:00:00') AS last_passage,
        IF(tests.phrase_id IS NULL, 0, 1) AS test_exists
        FROM phrases
        LEFT JOIN (
          SELECT
          foreign_phrase,
          count(*) as instances_cnt
          FROM phrases
          GROUP BY foreign_phrase
        ) instances
        ON (phrases.foreign_phrase = instances.foreign_phrase)
        LEFT JOIN tests
        ON (phrases.id = tests.phrase_id AND tests.test_type = 'FN')
        LIMIT :phrases_count"
    );

    $stmt->bindValue(':phrases_count', $count, \PDO::PARAM_INT);
    $stmt->execute();
    $phrases = $stmt->fetchAll();
    return $phrases;
  }

  public function selectPhrasesNF($count)
  {
    $stmt = $this->pdo->prepare(
        "SELECT
        phrases.id,
        phrases.foreign_phrase,
        phrases.native_phrase,
        phrases.pronunciation,
        phrases.hint,
        IFNULL(instances.instances_cnt, 1) AS instances_cnt,
        IFNULL(tests.passages_cnt, 0) AS passages_cnt,
        IFNULL(tests.first_passage, '0000-00-00 00:00:00') AS first_passage,
        IFNULL(tests.last_passage, '0000-00-00 00:00:00') AS last_passage,
        IF(tests.phrase_id IS NULL, 0, 1) AS test_exists
        FROM phrases
        LEFT JOIN (
          SELECT
          native_phrase,
          count(*) as instances_cnt
          FROM phrases
          GROUP BY native_phrase
        ) instances
        ON (phrases.native_phrase = instances.native_phrase)
        LEFT JOIN tests
        ON (phrases.id = tests.phrase_id AND tests.test_type = 'NF')
        LIMIT :phrases_count"
    );

    $stmt->bindValue(':phrases_count', $count, \PDO::PARAM_INT);
    $stmt->execute();
    $phrases = $stmt->fetchAll();
    return $phrases;
  }

  public function insertPhrase($phrase, $pronunciation, $translation, $hint)
  {
    $stmt = $this->pdo->prepare(
      "INSERT INTO phrases (foreign_phrase, pronunciation, native_phrase, hint)
      VALUES (:foreign_phrase, :pronunciation, :native_phrase, :hint)"
    );
    $stmt->execute([
      ':foreign_phrase' => $phrase,
      ':pronunciation' => $pronunciation,
      ':native_phrase' => $translation,
      ':hint' => $hint
    ]);
  }

  public function insertTest($phrase, $mode)
  {
    $stmt = $this->pdo->prepare(
      "INSERT INTO tests (test_type, phrase_id, passages_cnt, first_passage, last_passage)
      VALUES (:test_type, :phrase_id, 1, NOW(), NOW())"
    );

    $stmt->execute([
      ':test_type' => $mode,
      ':phrase_id' => $phrase['id']
    ]);
  }

  public function updateTest($phrase, $mode)
  {
    $stmt = $this->pdo->prepare(
      "UPDATE tests SET passages_cnt = :passages_cnt, last_passage = NOW()
      WHERE test_type = :test_type AND phrase_id = :phrase_id"
    );

    $stmt->execute([
      ':test_type' => $mode,
      ':phrase_id' => $phrase['id'],
      ':passages_cnt' => $phrase['passages_cnt'] + 1
    ]);
  }

  public function __destruct()
  {
    $this->pdo = null;
  }
}
