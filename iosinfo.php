<?php

$jsonFilePath = __DIR__ .  "/devices.json";

$LineFeedCode = "\n";

//if (php_sapi_name() != 'cli' && !isset($_GET['f'])) {
//
//    if (file_exists($jsonFilePath)) {
//
//        $json = file_get_contents($jsonFilePath);
//
//        // ヘッダー付与
//        header("Content-Type: application/json; charset=utf-8");
//        header('X-Content-Type-Options: nosniff');
//
//        echo $json;
//        exit;
//    }
//}


if (php_sapi_name() == 'cli') {

    // 失敗は FALSE が返る
    file_put_contents($jsonFilePath, $json);

} else {

    if (isset($_GET['type']) && ($_GET['type'] === "json" || $_GET['type'] === "obc" || $_GET['type'] === "swift")) {

        $url = "http://www.enterpriseios.com/wiki/iOS_Devices";

        $contents = file_get_contents($url, false);
        $status_code200 = strpos($http_response_header[0], '200');
        if ($status_code200 === false && $contents === NULL) {
            // 情報取得に失敗しました
            header("HTTP/1.1 500 Internal Server Error");
            exit;
        }

        $dom = new DOMDocument;
        @$dom->loadHTML(
            mb_convert_encoding($contents, 'HTML-ENTITIES', 'UTF-8')
        );
        $xpath = new DOMXPath($dom);
        $timestamp = time();
        date_default_timezone_set('Asia/Tokyo');
        $date = date("c", $timestamp);


        $entries = Array();
        $entries["timestamp"] = $timestamp;
        $entries["date"] = $date;

        //$entries["url"] = "http://www.enterpriseios.com/wiki/iOS_Devices";
        foreach ($xpath->query('//table[@class="views-table sticky-enabled cols-5"]/tbody/tr') as $node) {
            $entries["devices"][] = Array(
                'friendlyName'      => $xpath->evaluate('string(td[@class="views-field views-field-field-device-friendly-value"]/a)', $node),
                'identifier'        => $xpath->evaluate('normalize-space(td[@class="views-field views-field-title"])', $node),
                'introducedsort'    => $xpath->evaluate('string(td[@class="views-field views-field-field-device-introduced-value"]/span)', $node),
                'latestiOS'         => $xpath->evaluate('normalize-space(td[@class="views-field views-field-field-device-version-value"]/text()[1])', $node),
            );
        }


        if ($_GET['type'] === "json") {

            $options = 0;
            if (isset($_GET['format']) && $_GET['format'] === "true") {
                $options |= JSON_PRETTY_PRINT;
            }

            // JSON 出力
            $json = "";
            $json =  json_encode($entries, $options);

            // ヘッダー付与
            header("Content-Type: application/json; charset=utf-8");
            header('X-Content-Type-Options: nosniff');

            if (isset($_GET['file']) && $_GET['file'] === "true") {
                header('Content-Disposition: attachment; filename=devices.json');
            }

            echo $json;


        } else if ($_GET['type'] === "obc") {

            header("Content-Type: text/html; charset=UTF-8");
            $body = '<pre class="brush: objc;">' .  $LineFeedCode;
            $body .= '- (NSString *)getDeviceName:(NSString *)deviceId' .  $LineFeedCode;
            $body .= '{' .  $LineFeedCode;
            $body .= '    // デバイス判定用Dictionary' . $LineFeedCode;
            $body .= '    // データ取得日時　' . $date . $LineFeedCode;
            $body .= '    NSDictionary *devices = @{';
            for($i = 0; $i < count($entries["devices"]); $i++) {
                $identifier = $entries["devices"][$i]["identifier"];
                $friendlyName = $entries["devices"][$i]["friendlyName"];

                if ($i == 0) {
                    $body .= '@"' . $identifier . '" : @"' . $friendlyName . '"';
                } else {
                    $body .= ',' . $LineFeedCode . '                              ';
                    $body .= '@"' . $identifier . '" : @"' . $friendlyName . '"';
                }
            }
            $body .= '};';

            $body .= $LineFeedCode . $LineFeedCode;

            $body .= '    // 端末名判定' . $LineFeedCode;
            $body .= '    return devices[deviceId] ?: @"該当するデバイス名はありません";' .  $LineFeedCode;
            $body .= '}' .  $LineFeedCode;
            $body .= '</pre>';

            setHtml($body, $LineFeedCode);

        } else if ($_GET['type'] === "swift") {

            header("Content-Type: text/html; charset=UTF-8");
            $body = '<pre class="brush: swift;">' .  $LineFeedCode;
            $body .= 'func getDeviceName(deviceId: String) -> String {' .  $LineFeedCode;
            $body .= '    // デバイス判定用Dictionary' . $LineFeedCode;
            $body .= '    // データ取得日時　' . $date . $LineFeedCode;
            $body .= "    let devices: Dictionary = [";
            for($i = 0; $i < count($entries["devices"]); $i++) {
                $identifier = $entries["devices"][$i]["identifier"];
                $friendlyName = $entries["devices"][$i]["friendlyName"];

                if ($i == 0) {
                    $body .= '"' . $identifier . '" : "' . $friendlyName . '"';
                } else {
                    $body .= ',' . $LineFeedCode . '                               ';
                    $body .= '"' . $identifier . '" : "' . $friendlyName . '"';
                }
            }
            $body .= ']';

            $body .= $LineFeedCode . $LineFeedCode;

            $body .= '    // 端末名判定' . $LineFeedCode;
            $body .= '    return devices[deviceId] ?? "該当するデバイス名はありません"' .  $LineFeedCode;
            $body .= '}' .  $LineFeedCode;
            $body .= '</pre>';

            setHtml($body, $LineFeedCode);

        }

    } else {

        // パラメータの説明を書いたテキストデータを返す

        // jsonデータを取得する　?type=json
            // 整形する　format=true
            // jsonファイルとしてダウンロードする  file=true
        // objective-c の NSDictionary としてデータを取得する　?type=obc
        // swift の Dictionary としてデータを取得する　?type=swift

        header("Content-Type: text/html; charset=UTF-8");

        $body = "下記のサイトをスクレイピングしてデータを取得します<br>";
        $body .= "http://www.enterpriseios.com/wiki/iOS_Devices<br><br>";

        $body .= "パラメータ一覧<br>";
        $body .= "・jsonデータを取得する　?type=json<br>";
        $body .= "　　　　整形する　<a href=\"?type=json&format=true\" target='_blank'>?type=json&format=true</a><br>";
        $body .= "　　　　jsonファイルとしてダウンロードする  <a href=\"?type=json&file=true\" target='_blank'>?type=json&file=true</a><br>";
        $body .= "　　　　jsonファイルとして整形してダウンロードする  <a href=\"?type=json&file=true&format=true\" target='_blank'>?type=json&file=true&format=true</a><br>";
        $body .= "・objective-c の NSDictionary としてデータを取得する　<a href=\"?type=obc\" target='_blank'>?type=obc</a><br>";
        $body .= "・swift の Dictionary としてデータを取得する　<a href=\"?type=swift\" target='_blank'>?type=swift</a><br>";

        $body .= "<br><br><br>";

        $body .= "JSONデータの見栄えについて<br>";
        $body .= "ある程度は、パラメータ等で整形できますがカラーリング等は出来ません。<br>";
        $body .= "ブラウザの拡張プラグインを利用頂くことできれいに表示できます。<br>";
        $body .= "<br>";
        $body .= "Chrome：<a href='https://chrome.google.com/webstore/detail/jsonview/chklaanhfefbnpoihckbnefhakgolnmc' target='_blank'>JSONView</a><br>";
        $body .= "Firefox：<a href='https://addons.mozilla.org/ja/firefox/addon/jsonview/' target='_blank'>JSONView</a><br>";
        $body .= "Opera：<a href='https://addons.opera.com/en/extensions/details/jsonviewer/?display=en' target='_blank'>JsonViewer</a><br>";
        $body .= "<br>";

        setHtml($body, $LineFeedCode);
    }

}


function setHtml($body, $LineFeedCode) {
    echo '<!DOCTYPE html>', $LineFeedCode;
    echo '<html lang="ja">', $LineFeedCode;
    echo '<head>', $LineFeedCode;
    echo '    <meta charset="UTF-8">', $LineFeedCode;
    echo '    <title></title>', $LineFeedCode;
    echo'    <script type="text/javascript" src="scripts/shCore.js"></script>', $LineFeedCode;
    echo'    <script type="text/javascript" src="scripts/shBrushJScript.js"></script>', $LineFeedCode;
    echo'    <script type="text/javascript" src="scripts/shBrushObjectiveC.js"></script>', $LineFeedCode;
    echo'    <script type="text/javascript" src="scripts/shBrushSwift.js"></script>', $LineFeedCode;
    echo'    <link type="text/css" rel="stylesheet" href="styles/shCoreDefault.css"/>', $LineFeedCode;
    echo'    <script type="text/javascript">SyntaxHighlighter.all();</script>', $LineFeedCode;
    echo '</head>', $LineFeedCode;
    echo '<body>', $LineFeedCode;
    echo $body, $LineFeedCode;
    echo '</body>', $LineFeedCode;
    echo '</html>', $LineFeedCode;
}