<!DOCTYPE html>
<html>
    <head>
        <meta charset='utf-8'>
        <title>RPGゲーム</title>
        <link rel="stylesheet" href="../main.css">
    </head>
    <body>
        <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // ボタンの押された種類を確認
                if (isset($_POST['PAttack']) || isset($_POST['PMagic'])) {
                    // こうげきorまほうならin_battle.phpのファイルを読み込む
                    include 'in_battle.php';
                } else {
                    //初回表示時はstart_battleのファイルを読み込む
                    //まずセッションをクリアする
                    $_SESSION = array();
                    //セッションの記録を開始
                    session_start();
                    include 'start_battle.php';
                }
            }
        ?>
    </body>
</html>
