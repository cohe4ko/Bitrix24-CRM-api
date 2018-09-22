<?php

use Bitrix\bitrix;
use Bitrix\deal;
use Bitrix\contact;
use Bitrix\lead;

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
require_once("autoload.php");
require_once('Timer.php');
Timer::start();
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
