<?php
    if (session_start()) {
        //セッションが開始されているなら保持されている情報を取得
        $player_lv = $_SESSION["player_lv"];
        $player_hp = $_SESSION["player_hp"];
        $player_mp = $_SESSION["player_mp"];
        $player_atk = $_SESSION["player_atk"];
        $player_mgc = $_SESSION["player_mgc"];

        $enemy_lv = $_SESSION["enemy_lv"];
        $enemy_hp = $_SESSION["enemy_hp"];
        $enemy_mp = $_SESSION["enemy_mp"];
        $enemy_atk = $_SESSION["enemy_atk"];
        $enemy_mgc = $_SESSION["enemy_mgc"];

        $enemy_zan = $_SESSION["enemy_zan"];

        $msg = "";
        $enemy_down = false;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['PAttack'])) {
                // こうげき
                $msg = "あなたのこうげき<br>";

                // 0～10の乱数を取得
                $rnd = mt_rand(0, 10);

                // 5or10が出た場合はかいしんのいちげきとする
                if ($rnd == 5 || $rnd == 10) {
                    //メッセージに出力
                    $msg = $msg."かいしんのいちげき<br>";
                    $msg = $msg.(ceil(($player_atk * 2.35)))."のダメージ<br>";
                    //かいしんのいちげき
                    $enemy_zan = $enemy_zan - ($player_atk * 2.35);
                } else {
                    // ふつうのこうげき
                    $msg = $msg.($player_atk)."のダメージ";
                    $enemy_zan = $enemy_zan - $player_atk;
                }

                if ($enemy_zan < 0) {
                    // たおした
                    $zan = 0;
                    $msg = "〇〇をたおした！<br>";
                    $enemy_down = true;
                } else {
                    //敵の残HPをセッションに保存する
                    $_SESSION["enemy_zan"] = $enemy_zan;
                }

            } else if (isset($_POST['PMagic'])) {
                // まほう
                if ($player_mp == 0) {
                    //MPがない場合
                    $msg = $msg."MPがない！！<br>";

                } else {
                    //残りのMP - 3が0より小さくなってしまう(マイナス)かチェック
                    if (($player_mp - 3) < 0) {
                        //マイナスになってしまう場合
                        $msg = $msg."MPがない！！<br>";
                    } else {
                        //画面に表示されているMPを減らす
                        $player_mp = $player_mp - 4;
                        //プレイヤーのMPをセッションに保存する
                        $_SESSION["player_mp"] = $player_mp;

                        $msg = $msg."〇〇にまほうをつかった！<br>";
                        $msg = $msg.($player_mgc + 5)."のダメージ<br>";

                        $enemy_zan = $enemy_zan - ($player_mgc + 5);
                        if ($enemy_zan < 0) {
                            // たおした
                            $enemy_zan = 0;
                            $msg = "〇〇をたおした！<br>";
                            $enemy_down = true;
                        } else {
                            $_SESSION["enemy_zan"] = $enemy_zan;
                        }
                    }
                }
            }
        }
    }
?>
<div class="main-screen">
    <form method="POST" action="<?php if($enemy_down) echo "../" ?>">
        <div class="box-enemy">
            <?php
                if (!$enemy_down) {
            ?>
                <div class="buruburu">
                    <img src="../img/020a.png">
                </div>
            <?php
                }
            ?>
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
                <?php
                    if ($enemy_down) {
                ?>
                        <input type="submit" name="PAttack" value="こうげき" disabled />
                        <input type="submit" name="PMagic" value="まほう(MP4)" disabled />
                        <input type="submit" value="終了" />
                <?php
                    } else {
                ?>
                        <input type="submit" name="PAttack" value="こうげき" />
                        <input type="submit" name="PMagic" value="まほう(MP4)" />
                <?php
                    }
                ?>
            </fieldset>
        </div>
        <div class="box-msg">
            <p><?php echo $msg; ?></p>
        </div>
    </form>
</div>
