<!-- <meta http-equiv="Content-Type" content="text/html; charset=utf-8"> -->
<?php
header("Content-type: text/html; charset=utf-8");
use app\modules\app\APPConfig;
use app\modules\app\PathConfig;
use app\modules\cms\CMSConfig;
use app\modules\contrib\gxassets\GxLaddaAsset;

GxLaddaAsset::register($this);
include('site_ext.php');
$pageData = [
    'pageTitle' => 'Bộ dữ liệu bình luận',
    'pageBreadcrumb' => [['Dữ liệu bình luận']],
    'backgoundHeader' => Yii::$app->homeUrl . 'resources/images/plan-header.jpg'
]; ?>
<?= $this->render(PathConfig::getAppViewPath('pageHeader'), $pageData); ?>

<?php
    // $start = preg_quote('<script type="text/javascript">', '/');
    // $end = preg_quote('</script>', '/');

    // preg_match("/$start(.*?)$end/", $html, $matches);
    
    // var_dump($matches);
    // var_dump(nl2br(($html)));
?>
<?php
    // include('test.py');
    // dd($html);
    // dd( $html->find('.more-string lh-24 fs-14 fc-sixth '));
    // foreach($html->find('.w-fit d-block pl-60') as $element)
    //     echo $element . '<br>';
    // $command = escapeshellcmd('python ' . $_SERVER['DOCUMENT_ROOT'] . '/VnCoreNLP/test.py');

    // vncorenlp -Xmx2g VnCoreNLP-1.1.1.jar -p 9000 -a "wseg,pos,ner,parse"
    // chdir('C:\xampp\htdocs\KLTN\modules\app\views\data');
    // $output = shell_exec('python \htdocs\KLTN\modules\app\views\data\test.py 2>&1');
    // var_dump($output);
    // var_dump(chdir('C:\xampp\htdocs\KLTN\modules\app\views\data'));
    // $cd = shell_exec('cd htdocs\KLTN\modules\app\views\data');
    // var_dump($cd);
    // var_dump(getenv('PATH'));
    // var_dump($_ENV);
    // var_dump(getenv());
    // echo $_SERVER['DOCUMENT_ROOT'];
    // current directory
    // echo dirname('test.py');
    // echo dirname(__DIR__);
    // echo dirname(__FILE__ . "\test.py 2>&1");
    // echo shell_exec("python " . dirname(__FILE__ ) . '\test.py 2>&1');
    $string = shell_exec("python " . __DIR__ . '\test.py 2>&1');
    $manage = json_decode($string, true);
    var_dump($manage);
    echo 'Before: <br>';
    print_r($manage);
    // echo shell_exec("python " . $_SERVER['DOCUMENT_ROOT'] . '/KLTN/modules/app/views/data/test.py 2>&1');
    // var_dump(is_dir($_SERVER['DOCUMENT_ROOT'] . '/KLTN/modules/app/views/data/test.py'));
    // Header("Content-Type: text/plain");
    // error_reporting(E_ALL);
    // print shell_exec('python "' . __DIR__ . '\test.py" 2>&1'); // > /dev/null 2>/dev/null &')

    // java -Xmx2g -jar VnCoreNLP-1.1.1.jar -fin input.txt -fout output.txt
?>


