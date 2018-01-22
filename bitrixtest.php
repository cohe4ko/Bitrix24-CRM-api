<html>
    <head>
        <title>Bitrix Class Test</title>
    </head>
    <body>
<?php
/**
 * Класс для измерения времени выполнения скрипта или операций
 */
use Bitrix\bitrix;
use Bitrix\deal;
use Bitrix\contact;
use Bitrix\lead;
class Timer
{
    /**
     * @var float время начала выполнения скрипта
     */
    private static $start = .0;

    /**
     * Начало выполнения
     */
    static function start()
    {
        self::$start = microtime(true);
    }

    /**
     * Разница между текущей меткой времени и меткой self::$start
     * @return float
     */
    static function finish()
    {
        return microtime(true) - self::$start;
    }
}
/**
 * Created by PhpStorm.
 * User: dimoc
 * Date: 23.11.2017
 * Time: 20:15
 */
//дебаг
ini_set('display_errors', 1);
error_reporting(E_ALL);
//подключаю библиотеку
Timer::start();
require_once("autoload.php");
$bitrix = new bitrix('dreambani.bitrix24.ru',1,'wrv8k07q66cwux57');
//$deal = new deal(412);
//$deal->setTitle('Privet!');
//$deal->setResponsibleId('6');
//$deal->save();
$search = $bitrix->searchLeads('89853381868');
//$search = $bitrix->getLeadList();
//$cont = new lead();
//$cont->setTitle('Teeeega');
//$cont->setResponsibleId('6');
//$cont->setPhone('79853381868');
//$cont->setFamilyName('FamilyName');
//$cont->setName('Name');
//$cont->setOtchestvo('Otchestvo');
//$cont->save();

echo "<pre>";
print_r($search);
echo "<br>";
echo 'Время работы скрипта: '.Timer::finish() . ' сек.';

?>
<!-- ROISTAT CODE BEGIN -->
<script>
    (function(w, d, s, h, id) {
        w.roistatProjectId = id; w.roistatHost = h;
        var p = d.location.protocol == "https:" ? "https://" : "http://";
        var u = /^.*roistat_visit=[^;]+(.*)?$/.test(d.cookie) ? "/dist/module.js" : "/api/site/1.0/"+id+"/init";
        var js = d.createElement(s); js.charset="UTF-8"; js.async = 1; js.src = p+h+u; var js2 = d.getElementsByTagName(s)[0]; js2.parentNode.insertBefore(js, js2);
    })(window, document, 'script', 'cloud.roistat.com', 'fa4882129d892f0997543a8bddcd96ca');
</script>
<!-- ROISTAT CODE END -->
    </body>
</html>
