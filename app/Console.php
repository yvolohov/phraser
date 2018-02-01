<?php

class Console
{
  public function showHelp()
  {
    echo $this->bold('HELP') . PHP_EOL;
    echo $this->bold('----') . PHP_EOL;
    echo $this->green('-e ') . ' : editor mode;' . PHP_EOL;
    echo $this->green('-fn') . ' : test foreign to native language;' . PHP_EOL;
    echo $this->green('-nf') . ' : test native to foreign language;' . PHP_EOL;
  }

  protected function bold($text)
  {
    return "\x1b[1m{$text}\x1b[0m";
  }

  protected function red($text)
  {
    return "\x1b[31m{$text}\x1b[0m";
  }

  protected function green($text)
  {
    return "\x1b[32m{$text}\x1b[0m";
  }
}
