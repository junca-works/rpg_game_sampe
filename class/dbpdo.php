<?php
    /**
     * DB定数定義
     */
    define("MYSQL_HOST", "localhost");
    define("MYSQL_DBNAME", "rpgdb");
    define("MYSQL_CHARSET", "UTF8");
    define("MYSQL_USER", "rpguser");
    define("MYSQL_PASSWORD", "rpguser01");

    class dbpdo {
        private $pdo;
        private $host;
        private $dbname;
        private $user;
        private $password;
        private $charset;

        /** 最後に実行したSQL */
        private $sql;
        /** 最終発生エラー情報(配列データ)
         * 配列[0]:SQLSTATE エラーコード (SQLで定義されたID)
         * 配列[1]:エラーコード
         * 配列[2]:エラーメッセージ
        */
        private $error;

        /**
         * 最後に発生したエラー情報を取得する処理
         */
        public function getLastError() {
            return $this->$error;
        }

        /**
         * コンストラクタ
         * $host       string     接続先ホスト      (デフォルト：MYSQL_HOST)
         * $dbname     string     データベース名    (デフォルト：MYSQL_DBNAME)
         * $charset    string     キャラクタセット名(デフォルト：MYSQL_CHARSET)
         * $user       string     接続ユーザ        (デフォルト：MYSQL_USER)
         * $password   string     接続パスワード    (デフォルト：MYSQL_PASSWORD)
         */
        public function __construct($host = MYSQL_HOST, $dbname = MYSQL_DBNAME, $charset = MYSQL_CHARSET,
                                $user = MYSQL_USER, $password = MYSQL_PASSWORD) {
            //  データベース指定値退避
            $this->host     = trim($host);
            $this->dbname   = trim($dbname);
            $this->charset  = trim($charset);
            $this->user     = trim($user);
            $this->password = trim($password);
            //  初期化
            $this->sql = "";
            $this->clearError();
            //  データベース接続
            try {
                $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
                $this->pdo = new PDO($dsn, $this->user, $this->password);
            } catch (PDOException $e) {
                echo 'データベース接続エラー:'.$e->getMessage();
            }
            if ($this->pdo == false) {
                die("データベースへの接続に失敗しました。");
            }
        }

        /**
         * エラー情報のクリア
         */
        protected function clearError() {
            $this->error = array(0 => null, 1 => null, 2 => null);
        }

        /**
         * データベース切断
         */
        public function close() {
            $this->pdo = null;
        }

        /**
         * トランザクション開始
         */
        public function begin() {
            return $this->pdo->beginTransaction();
        }

        /**
         * トランザクションのコミット
         */
        public function commit() {
            return $this->pdo->commit();
        }

        /**
         * トランザクションのロールバック
         */
        public function rollback() {
            return $this->pdo->rollBack();
        }

        /**
         * [prepare]メソッドのラッパ関数
         */
        public function prepare($sql) {
            //  エラークリア
            $this->clearError();
            //  SQL文の準備
            $pdostmt = $this->pdo->prepare($sql);
            if ($pdostmt === false) {
                //  エラーの場合
                $this->error = $pdostmt->errorInfo();
                return false;
            }
            //  [PDOStatement]オブジェクトを返す
            return $pdostmt;
        }

        /**
         * SQL実行
         */
        public function execute($sql, $arrBind = array()) {
            //  SQL準備
            $pdostmt = $this->prepare($sql);
            if ($pdostmt === false) {
                return false;
            }
            //  バインド
            foreach ($arrBind as $key => $val) {
                $ret = $pdostmt->bindValue($key, $val);
                if ($ret === false) {
                    $this->error = $pdostmt->errorInfo();
                    return false;
                }
            }
            //  実行
            $ret = $pdostmt->execute();
            if ($ret === false) {
                $this->error = $pdostmt->errorInfo();
                return false;
            }
            //  SELECTステートメントの場合??
            if (preg_match('/^select/i', trim($sql))) {
                //  [PDOStatement]オブジェクト
                return $pdostmt;
            }
            else {
                //  selectステートメント以外の場合
                return true;
            }
        }

        /**
         * SQL実行し1個のカラム値を取得
         */
        public function getColumnData($sql, $arrBind = array(), $columnName = 0) {
            //  SQLステートメント実行
            $pdostmt = $this->execute($sql, $arrBind);
            if ($pdostmt === false) {
                return false;
            }
            //  1行取得
            $row = $pdostmt->fetch();
            if ($row === false) {
                //  エラー
                $this->error = $pdostmt->errorInfo();
                $ret = false;
            } else {
                //  カラムデータ
                $ret = $row[$columnName];
            }
            //  接続を閉じる
            $pdostmt = null;
            //  カラムデータを返す
            return $ret;
        }

        /**
         * SQL実行結果の件数(レコード数)を取得
         */
        public function getSelectCount($sql, $arrBind = array()) {
            //  SELECT文を SELECT count(*) でラップ
            $sql = "select count(*) from ({$sql}) as tx";
            //  件数を取得して返します。
            return $this->getColumnData($sql, $arrBind);
        }
    }

?>
