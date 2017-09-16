# Mastodon-WebSocket-Listener

## 動作環境

* PHP CLI 5.4.16 or higher
 * cURL 拡張
 * mbstring 拡張

## 使い方

### oauth.php の編集

インスタンスのURL、ユーザ名、パスワード、アプリケーション名を設定します。

設定後、コマンドラインから実行すると"access_token.json"が生成されます。


### receiver.php の改造

wrcallback() 関数内のswitch/case文が主な処理を行う場所です。

トゥートを送出する場合は toot() 関数を使用してください。



## 既知の問題
* イベントを取りこぼすことがあります