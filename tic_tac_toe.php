<?php
/* 変数宣言 */
$fieldValues = array_fill(0, 9, "-");
$victoryPattern = [
  [0, 1, 2],
  [3, 4, 5],
  [6, 7, 8],
  [0, 3, 6],
  [1, 4, 7],
  [2, 5, 8],
  [0, 4, 8],
  [2, 4, 6]
]; // 勝利パターン

/* 関数定義 */

/* 対人戦またはCPU戦を選択し、入力チェックを行う関数 */
$modeCheck = function () use (&$modeCheck) {
  $modeFlg = trim(fgets(STDIN));
  if ($modeFlg === "1" || $modeFlg === "2") {
    return intval($modeFlg);
  }else {
    echo "正しい値を入力してください : ";
    return $modeCheck();
  }
};

/* 入力した値をチェックする関数 */
$inputCheck = function () use (&$inputCheck) {
  global $fieldValues;

  $field = trim(fgets(STDIN));
  if (!($field > 9 || $field < 1)) {
    /* 入力されたフィールドが選択済みかどうかを判定する */
    if ($fieldValues[$field - 1] === "-") {
      return intval($field);
    }else {
      echo "\nそのマスは既に選択されています。\n1〜9から選択するマスを入力してください : ";
      return $inputCheck();
    }
  }else {
   echo "1〜9の中から選択してください : ";
   return $inputCheck();
  }
};

/* フィールドの番号を表示する関数 */
$fieldExplanation = function () {
  echo "\nマスの番号は以下のようになります。\n\n\n";
  for($i = 1; $i <= 9; $i++) {
    echo "　　　".$i;
    if ($i % 3 === 0) {
      echo "\n\n\n";
    }
    if ($i === 9) {
      echo "\n";
    }
  }
};

/* 現在のフィールドの状態を表示する関数 */
$currentFieldDisp = function () {
  global $fieldValues;

  echo "\n\n\n";
  for($i = 1; $i <= 9; $i++) {
    echo "　　　".$fieldValues[$i - 1];
    if ($i % 3 === 0) {
      echo "\n\n\n";
    }
    if ($i === 9) {
      echo "\n";
    }
  }
};

/* フィールドに〇または☓をセットする関数 */
$setFieldValue = function ($value, $field) {
  global $fieldValues;

  $fieldValues[$field - 1] = $value;
};

/* 引き分けを判定する関数 */
$drawCheck = function ($turn) {
  if ($turn === 10) return true;
  return false;
};

/* 勝敗を判定する関数 */
$judge = function () {
  global $fieldValues;
  global $victoryPattern;

  foreach($victoryPattern as $fields){
    if($fieldValues[$fields[0]] !== "-"){
      if($fieldValues[$fields[0]] === $fieldValues[$fields[1]]
       && $fieldValues[$fields[0]] === $fieldValues[$fields[2]]){
        return true;
      }
    }
  }

  return false;
};

/* 先手・後手を決める関数 */
$baCheck = function () use (&$baCheck) {
  echo "先手・後手を選んでください (1:先手, 2:後手) : ";
  $baFlg = trim(fgets(STDIN));
  switch ($baFlg) {
    case 1: return (1); break;
    case 2: return ("CPU"); break;
    default: echo "正しい値を入力してください。\n"; return $baCheck(); break;
  }
};

/* 奇数か偶数かを調べ、〇または☓を返す関数 */
$oeCheck = function ($turn) {
  if($turn % 2 === 1){
    return "☓";
  }else{
    return "〇";
  }
};

/* CPUの処理 */
$cpuProcess = function () use (&$cpuProcess) {
  global $fieldValues;

  $field = mt_rand(1,9);
  if ($fieldValues[$field - 1] === "-") {
    return $field;
  } else {
    return $cpuProcess();
  }
};

/* ゲーム処理 */
$gameProcess = function ($player = 1, $turn = 1, $cpu = false) {
  extract($GLOBALS);

  if($turn === 1) echo "\n\n";

  if($drawCheck($turn)){
    echo "引き分けです！\n\n";
    return;
  }

  echo "{$turn}ターン目\n";

  $field;

  switch ($player) {
    case "CPU":
      $field = $cpuProcess();
      sleep(2);
      echo "CPUの番 「{$field}」 を選択しました。\n";
      break;
    default:
      echo "{$player}Pの番 1〜9から選択するマスを入力してください : ";
      $field = $inputCheck();
      break;

  }

  $setFieldValue($oeCheck($turn), $field);

  $currentFieldDisp();

  if($judge()){
    switch($player){
      case "CPU": echo "CPU"; break;
      default: echo $player."P"; break;
    }
    echo "の勝利！\n";
    exit();
  }

  switch ($player) {
    case 1:
      if (!$cpu) {
        $gameProcess(++$player, ++$turn);
      }else {
        $gameProcess("CPU", ++$turn, true);
      }
      break;
    case 2: $gameProcess(--$player, ++$turn); break;
    case "CPU": $gameProcess(1, ++$turn, true); break;
  }
};

/* 関数定義ここまで */


/* ゲームスタート */
echo "\n対人戦かCPU戦を選んでください (1:対人, 2:CPU) : ";
$mode = $modeCheck();

$fieldExplanation();

switch ($mode) {
  case 1: $gameProcess(); break;
  case 2: $gameProcess($baCheck(), 1, true); break;
}
/* ゲーム終了 */
