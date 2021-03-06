<?php
$htmlBackground = 'https://iw233.cn/api.php?sort=random';//自定义背景图片，可以设置为任意图片或随机图片的API，默认无背景
$htmlBgBlur = 0;//自定义背景高斯模糊，单位px
$htmlBgOpacity = 1;//自定义背景透明度，区间为0-1，0为完全透明，1为完全不透明
$htmlIcon = './icon.ico';//自定义图标

function post($url, $data){
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
curl_setopt($curl, CURLOPT_CONNECTTIMEOUT_MS, 0);
curl_setopt($curl, CURLOPT_HEADER, 0);
curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json; charset=utf-8', 'Content-Length:'.strlen($data)]);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$res = curl_exec($curl);
curl_close($curl);
return $res;
};
if ($_GET['download'] != null){
    $downloadInfo = json_decode(urldecode($_GET['download']), true);
    if ($downloadInfo['json'] != null){
        if ($_SERVER["SERVER_PORT"] != ("80" || "443")) $serverPort = ":".$_SERVER["SERVER_PORT"];
        $downloadURL = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['SERVER_NAME'].$serverPort.dirname($_SERVER['PHP_SELF'])."/".$downloadInfo['json'];
    }else if ($downloadInfo['url'] != null){
        $downloadURL = $downloadInfo['url'];
    }else{
        exit;
    };
    header('Content-Type: application/force-download');
    header('Content-Disposition: attachment; filename="'.basename($downloadURL).'"');
    header('Content-Transfer-Encoding: binary');
    header('Connection: close');
    readfile($downloadURL);
    exit;
};
if ($_POST['url'] != null){
    if ($_SERVER["SERVER_PORT"] != ("80" || "443")) $serverPort = ":".$_SERVER["SERVER_PORT"];
    $api = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['SERVER_NAME'].$serverPort.dirname($_SERVER['PHP_SELF'])."/api.php?type=table";
    $htmlTable = post($api, json_encode(['url' => $_POST['url']]));
    if ($htmlTableX = @json_decode($htmlTable, true)) $htmlTable = $htmlTableX['code']."<br>".$htmlTableX['message'];
};
$htmlBgHeader = @get_headers($htmlBackground, 1);
if ($htmlBgHeader['Location'] != null) $htmlBackground = $htmlBgHeader['Location'];
if ($htmlBgHeader['location'] != null) $htmlBackground = $htmlBgHeader['location'];
$bgDownload = urlencode(json_encode(['url' => $htmlBackground]));
?>
<html>
<head>
    <meta http-equiv'Content-Type' content='text/html; charset=utf-8'>
    <meta name='viewport' content='width=device-width,maximum-scale=1.0, minimum-scale=1.0, user-scalable=no'>
    <title>原神抽卡记录分析工具</title>
    <link href='<?php print_r($htmlIcon);?>' rel='icon' type='image/x-icon' />
    <style>
    <?php print_r(file_get_contents("index.css")."\n");?>
    .overlay:before {
        background:url(<?php print_r($htmlBackground);?>) no-repeat;
        background-size:cover;
        background-position:center 0;
        width:100%;
        height:100%;
        content:"";
        position:absolute;
        top:0;
        left:0;
        z-index:-1;
        -webkit-filter:blur(<?php print_r($htmlBgBlur);?>px);
        filter:blur(<?php print_r($htmlBgBlur);?>px);
        opacity:<?php print_r($htmlBgOpacity);?>;
        margin:0;
        padding:0;
        position:fixed;
    }
    </style>
</head>
<body style='margin:0px'>
    <div id='overlay' class='overlay'>
        <div class='text-bg'>
            <div class='input_control'>
                <form method='post' style='margin:0px'>
                    <h4>请在下方文本框粘贴抽卡记录地址:</h4>
                    <h6>P.S.如果您曾使用过本工具，您亦可以输入您的UID以获取曾经保存的记录；注意，如需更新记录还请重新获取并粘贴新的抽卡记录地址</h6>
                    <textarea id='url' name='url' style='min-width:100%;max-width:100%;min-height:15em'><?php print_r($_POST['url']);?></textarea>
                    <input id='submit' type='submit' value='开始分析抽卡记录',name='submit' <?php if($_GET['app'] == null) print_r("onclick=\"alert('请稍作等待，我们正在逐页获取您的全部抽卡记录并分析；\\n这意味着您抽卡记录越多，分析时间也会越长，请见谅！\\n切勿刷新页面，这可能会导致存储在网站服务器上的数据出现错误！');\"");?>>
                </form>
                <?php print_r($htmlTable);?>
                <footer id='footer'>
                    <hr><p class='copyright'>2022 &copy; Powered by 0803QwQ</p>
                </footer>
            </div>
        </div>
        <div id='dlbg' class='dlbg'>
            <a href="JavaScript:openBg()">
                <button id='download'>获取背景</button>
            </a>
        </div>
        <div id='imgLayer' onclick="closeBg()" />
        <div id='imgBoxl' class='modal'>
            <a href="JavaScript:download('<?php print_r($bgDownload);?>')">
                <img id='bigimg' src='<?php print_r($htmlBackground);?>' title='点击图片以保存&#10;点击空白处以关闭' />
            </a>
        </div>
    </div>
    <script src="index.js"></script>
</body>
</html>
