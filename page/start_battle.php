<?php
    //データベースクラスをインクルード
    include_once("../class/dbpdo.php");

    // データベースクラスを生成
    $pdo = new dbpdo();

    /**プレイヤーのステータス */
    $player_lv;
    $player_hp;
    $player_mp;
    $player_atk;
    $player_mgc;

    /**敵のステータス */
    $enemy_lv;
    $enemy_hp;
    $enemy_mp;
    $enemy_atk;
    $enemy_mgc;

    $msg = "〇〇があらわれた";

    try {
        //プレイヤーのデータを取得するSQLを発行
        $sqlPlayerList = "select * from tbl_player where id = :id;";
        //SQLパラメータの設定
        $pdostmt = $pdo->execute($sqlPlayerList, array(":id" => 1));
        while ($result = $pdostmt->fetch()) {
            $player_lv = $result["lv"];
            $player_hp = $result["hp"];
            $player_mp = $result["mp"];
            $player_atk = $result["atk"];
            $player_mgc = $result["mgc"];
        }

        //プレイヤーのデータを取得するSQLを発行
        $sqlEnemyList = "select * from tbl_enemy where id = :id;";
        //SQLパラメータの設定
        $pdostmt = $pdo->execute($sqlEnemyList, array(":id" => 1));
        while ($result = $pdostmt->fetch()) {
            $enemy_lv = $result["lv"];
            $enemy_hp = $result["hp"];
            $enemy_mp = $result["mp"];
            $enemy_atk = $result["atk"];
            $enemy_mgc = $result["mgc"];
        }

        $_SESSION["player_lv"] = $player_lv;
        $_SESSION["player_hp"] = $player_hp;
        $_SESSION["player_mp"] = $player_mp;
        $_SESSION["player_atk"] = $player_atk;
        $_SESSION["player_mgc"] = $player_mgc;

        $_SESSION["enemy_lv"] = $enemy_lv;
        $_SESSION["enemy_hp"] = $enemy_hp;
        $_SESSION["enemy_mp"] = $enemy_mp;
        $_SESSION["enemy_atk"] = $enemy_atk;
        $_SESSION["enemy_mgc"] = $enemy_mgc;

        $_SESSION["enemy_zan"] = $enemy_hp;
    } finally {
        $pdo->close();
    }
?>
<div class="main-screen">
    <form method="POST" action="">
        <div class="box-enemy">
            <div class="box-enemy">
                <img src="../img/020a.png">
            </div>
        </div>
        <div class="box-status">
            <fieldset>
                <legend>あなた</legend>
                <p>HP:<?php echo $player_hp; ?></p>
                <p>MP:<?php echo $player_mp; ?></p>
            </fieldset>
        </div>
        <div class="box-command">
            <fieldset>
                <legend>どうする？</legend>
                <input type="submit" name="PAttack" value="こうげき" />
                <input type="submit" name="PMagic" value="まほう(MP4)" />
            </fieldset>
        </div>
        <div class="box-msg">
            <p><?php echo $msg; ?></p>
        </div>
    </form>
</div>
