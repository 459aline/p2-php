<?php
/*
ReplaceImageURL(url)        メイン関数
save(array)                 データを保存
load()                      データを読み込んで返す(自動的に実行される)
clear()                     データを削除
*/

class ReplaceWordCtl
{
    protected $isLoaded = false;
    protected $data = array();

    public function setup()
    {
        if (!$this->isLoaded) {
            $this->load();
            $this->isLoaded = true;
        }
    }

    // ファイル名を返す
    public function filename($cont)
    {
        return 'p2_replace_' . $cont . '.txt';
    }

    // ファイルを削除
    public function clear($cont)
    {
        global $_conf;

        $path = $_conf['pref_dir'] . '/' . $this->filename($cont);

        return @unlink($path);
    }

    // 全てのデータを読み込む
    public function load()
    {
        $this->loadFile('name');
        $this->loadFile('mail');
        $this->loadFile('date');
        $this->loadFile('msg');

        return $this->data;
    }

    // ファイルを読み込む
    public function loadFile($cont)
    {
        global $_conf;

        $lines = array();
        $path = $_conf['pref_dir'].'/'.$this->filename($cont);
        if ($lines = @file($path)) {
            foreach ($lines as $l) {
                if (substr($l, 0, 1) === ';' || substr($l, 0, 1) === "'" ||
                    substr($l, 0, 1) === '#' || substr($l, 0, 2) === '//') {
                    //"#" ";" "'" "//"から始まる行はコメント
                    continue;
                }
                $lar = explode("\t", trim($l));
                // Matchは必要だがReplaceは空でも良い
                if (strlen($lar[0]) == 0)  continue;

                $ar = array(
                    'match'   => $lar[0], // 対象文字列
                    'replace' => $lar[1], // 置換文字列
                    'mode'    => $lar[2]  // モード(0:両方, 1:PC, 2:携帯)
                );

                $this->data[$cont][] = $ar;
            }
        }
        return $this->data[$cont];
    }

    // ファイルを保存
    public function save($data)
    {
        global $_conf;

        $path = $_conf['pref_dir'] . '/' . $this->filename($cont);

        $newdata = '';

        foreach ($data as $na_info) {
            $a[0] = strtr(trim($na_info['match']  , "\t\r\n"), "\t\r\n", "   ");
            $a[1] = strtr(trim($na_info['replace'], "\t\r\n"), "\t\r\n", "   ");
            $a[2] = strtr(trim($na_info['mode']   , "\t\r\n"), "\t\r\n", "   ");
            if ($na_info['del'] || ($a[0] === '' || $a[1] === '')) {
                continue;
            }
            $newdata .= implode("\t", $a) . "\n";
        }
        return FileCtl::file_write_contents($path, $newdata);
    }

    /*
    $cont:対象
          name:名前
          mail:メール
          date:日付その他
          msg:メッセージ
    $aThread
          Threadクラスオブジェクトを指定(showthread.inc.phpなら$this->thread)
    $ares:レスの内容
    $i:レス番号
    */
    public function replace($cont, $aThread, $ares, $i)
    {
        global $_conf;

        $this->setup();

        $resar   = $aThread->explodeDatLine($ares);
        $name    = $resar[0];
        $mail    = $resar[1];
        $date_id = $resar[2];
        $msg     = $resar[3];

        switch ($cont) {
            case 'name':
                $word = $name;
                break;
            case 'mail':
                $word = $mail;
                break;
            case 'date':
                $word = $date_id;
                break;
            case 'msg':
                $word = $msg;
                break;
            // エラー
            default:
                // そのまま返す
                return $word;
        }
        // 置換設定が無い場合はそのまま返す
        if (!isset($this->data[$cont])) {
            return $word;
        }

        preg_match('|ID: ?([0-9A-Za-z/.+]{8,11})|',$date_id, $matches);
        $id = $matches[1];
        foreach ($this->data[$cont] as $v) {
            // 携帯モードでデータがPC用なら飛ばす
            if ($_conf['ktai']  && $v['mode'] == 1) continue;
            // PCモードでデータが携帯用なら飛ばす
            if (!$_conf['ktai'] && $v['mode'] == 2) continue;

            /* Match用の変数展開(用途が思い浮かばないのでコメントアウト)
            $v['match'] = str_replace ('$i',         $i, $v['match']);
            $v['match'] = str_replace ('$ttitle',    $aThread->ttitle, $v['match']);
            $v['match'] = str_replace ('$ttitle_hd', $aThread->ttitle_hd, $v['match']);
            $v['match'] = str_replace ('$host',      $aThread->host, $v['match']);
            $v['match'] = str_replace ('$bbs',       $aThread->bbs,  $v['match']);
            $v['match'] = str_replace ('$key',       $aThread->key,  $v['match']);
            $v['match'] = str_replace ('$name',      $name,  $v['match']);
            $v['match'] = str_replace ('$mail',      $mail,  $v['match']);
            $v['match'] = str_replace ('$date_id',   $date_id,  $v['match']);
            $v['match'] = str_replace ('$msg',       $msg,  $v['match']);
            $v['match'] = str_replace ('$id_base64', base64_encode($id),  $v['match']);
            $v['match'] = str_replace ('$id',        $id,  $v['match']);
            */
            /*
            これ自体に正規表現が入っていたらどうしよう。
            実質的に使うのは$i, $host, $bbs, $key, $date_idくらいだから問題ないだろうけど。
            */
            $v['replace'] = strtr($v['replace'], array(
                    '$ttitle_hd' => $aThread->ttitle_hd,
                    '$host' => $aThread->host,
                    '$bbs' => $aThread->bbs,
                    '$key' => $aThread->key,
                    '$id' => $id,
                    '$id_base64' => base64_encode($id),
                    '$i' => $i
            ));
            $word = @preg_replace ('{'.$v['match'].'}', $v['replace'], $word);
        }

        return $word;
    }
}
