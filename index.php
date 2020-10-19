<?session_start();
include "mysql_obj.php";
$sql=new mysql_obj;
if($_GET['page']=='adm') print '<form name="form_login" id="form_login" method="post" action="index.php">User: <input type="text" name="user" /> Pass: <input type="password" name="password" /> <input type="submit"></form>';
if($_POST['user']=='admin'&&$_POST['password']=='pass') $_SESSION['user']=true;
if($_POST['user']&&$_POST['password']&&($_POST['user']!='admin'||$_POST['password']!='pass')) $_SESSION['user']=false;
if($_GET['logout']) $_SESSION['user']=false;
?><!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Forex exchange</title>
<meta name="Keywords" content="forex,exchange,currency,money" />
<meta name="Description" content="Валутно бюро Форекс" />
<meta name="distribution" content="global">
<meta name="robots" content="index,follow,all,noarchive">
<meta name="resource-type" content="document">
<link rel="stylesheet" type="text/css" href="style.css">
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
</head>

<body><?
function razlika($val1,$val2){
    if($val1>$val2) $tmp=($val1-$val2)*100;
    if($val2>$val1) $tmp=($val2-$val1)*100;
    return $tmp; 
}

if($_SESSION['user']){
    if(in_array($_GET['t'], array('kupuva','prodava'))) {
        $fields=$sql->fetch_array("show fields from ".$_GET['t']); unset($set);
        foreach($fields as $key=>$value) if($value["Field"]!='id') $set[]=$value["Field"]."='".$_POST[$value["Field"]]."'";
        $set=implode(',', $set);//$sql->debug=true;
        if($_POST['usd']&&$_POST['date']&&!$_POST['id']) $sql->query("insert into ".$_GET['t']." set $set");
        if($_POST['usd']&&$_POST['date']&&$_POST['id']) $sql->query("update ".$_GET['t']." set $set where id='".$_POST['id']."'");
        if($_GET['id']&&$_GET['del']) $sql->query("delete from ".$_GET['t']." where id='".$_GET['id']."'");
        }
    
    print '<a href="?t=kupuva&d='.$z[0]['id'].'">добави купува</a> | <a href="?t=prodava&d='.$z[0]['id'].'">добави продава</a> | <a href="index.php?logout=1">ИЗХОД</a>';
    $z=$sql->fetch_array("select *, DATE_FORMAT(date,'%d-%m-%Y') as dd from kupuva");
    print '<div class="divleft">';
    for($i=0;$i<count($z);$i++) print '<p>'.$z[$i]['dd'].' - <a href="?t=kupuva&d='.$z[$i]['id'].'">купува</a> | <a href="?t=prodava&d='.$z[$i]['id'].'">продава</a> | <a href="?t=prodava&d='.$z[$i]['id'].'&del=1" title="Изтрий">X</a></p>';
    print '</div>';
    if($_GET['t']){
    print '<div class="divright"><form method="post" action="index.php?t='.$_GET['t'].'"><h3>'.$_GET['t'].'</h3>';
    if($_GET['d']) $cur=$sql->fetch_array("select *, DATE_FORMAT(date,'%d-%m-%Y') as dd from ".$_GET['t']." where id='".$_GET['d']."'");
    foreach($fields as $key=>$value) if($value["Field"]=='id') print '<input type="hidden" name="'.$value["Field"].'" value="'.$cur[0][$value["Field"]].'">'; else print '<p>'.$value["Field"].' <input type="text" name="'.$value["Field"].'" value="'.($cur[0][$value["Field"]]?$cur[0][$value["Field"]]:($value["Field"]=='date'?date("Y-m-d"):'')).'"></p>';
    print '<input type="submit"></form></div>';
    }
}
?>
<header id="headpic" class="body">
<div class="logo"><a href="index.php?r=home"><img id="logo" src="img/logo.png" alt="Forex" /></a></div>
<nav>
   <ul class="navtop"><span><?=date("d.m.Y г.")?></span>
      <li><a href="index.php">Начало</a></li>
      <li><a href="index.php?page=about">За нас</a></li>
      <li><a href="index.php?page=valuti">Валутни курсове</a></li>
      <li><a href="index.php?page=contacts">Контакти</a></li>
   </ul>
</nav>
<img src="img/headpic.jpg" />
</header>

<section class="body">

<?switch($_GET['page']){
case 'about': print '<h1>За нас</h1><p>Обмено бюро "FOREX EXCHANGE"</p><p>Приемаме всички конвертируеми и неконвертируеми валути.</p><p>БЕЗ КОМИСИОННИ И БЕЗ ТАКСИ</p><p>Извършваме обмяна на монети и на някои стари емисии банкноти.</p><p>Конвертируемите валути се търгуват с тесни маржове и актуални обменни валутни курсове, базирани на международния валутен пазар.За по-големи количества могат да се направят договорки за валутните котировки с дилъра на Бюрото.</p><p>Изпълняваме поръчки за набавяне на по-големи количества от дадена валута и доставка на някоя екзотична валута(ако я няма в наличност на касите на обменните бюра).</p><p>Сделки с том и спот вальор на предварително договорени курсове.</p><p>Проверка на валута и левове по молба на клиента.</p>';break;
case 'contacts': print '<h1>Контакти</h1><p>Адрес:<br>1000 София<br>ул. Черковна 6<br><br>Тел.: 02 1234567</p>';break;
case 'valuti': 
print '<h1>Валутни курсове</h1>';
   if($_GET['val']){
    $kupuva=$sql->fetch_array("select *, DATE_FORMAT(date,'%d.%m.%Y') as dd from kupuva order by date desc limit 1");
$prodava=$sql->fetch_array("select *, DATE_FORMAT(date,'%d.%m.%Y') as dd from prodava order by date desc limit 1");
$fields=$sql->fetch_array("show fields from kupuva");
foreach($fields as $key=>$value) if($value["Field"]!='id'&&$value["Field"]!='date')
$prodava=$sql->fetch_array("select *, DATE_FORMAT(date,'%d.%m.%Y') as dd from prodava order by date limit 10");?>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ["Date","<?=strtoupper($_GET['val']).'"'?>]
<?
for($i=0;$i<count($prodava);$i++){
    print ',["'.$prodava[$i]['dd'].'",'.$prodava[$i][$_GET['val']].']';
}?>
        ]);
        var options = {title: 'Курс на <?=strtoupper($_GET['val'])?> за последните 10 дни'};
        var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>
    <div id="chart_div" style="width: 980px; height: 300px;"></div>    
   <?}else{ 
    
$kupuva=$sql->fetch_array("select *, DATE_FORMAT(date,'%d.%m.%Y') as dd from kupuva order by date desc limit 1");
$prodava=$sql->fetch_array("select *, DATE_FORMAT(date,'%d.%m.%Y') as dd from prodava order by date desc limit 1");
print '<h3>Курсове за '.$kupuva[0]['dd'].'</h3>
<table style="width:300px;float: left;">
<tr><th>Валута</th><th>Купува</th><th>Продава</th></tr>';
$fields=$sql->fetch_array("show fields from kupuva");
foreach($fields as $key=>$value) if($value["Field"]!='id'&&$value["Field"]!='date')
print '<tr><td><a href="?page=valuti&val='.$value["Field"].'">'.$value["Field"].'</a></td><td>'.money_format('%.3n', $kupuva[0][$value["Field"]]).'</td><td>'.money_format('%.3n', $prodava[0][$value["Field"]]).'</td></tr>';
print '</table>';
$prodava=$sql->fetch_array("select *, DATE_FORMAT(date,'%d.%m.%Y') as dd from prodava order by date limit 10");?>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ["Date"<?foreach($fields as $key=>$value) if($value["Field"]!='id'&&$value["Field"]!='date') print ',"'.strtoupper($value["Field"]).'"';?>]
<?
for($i=0;$i<count($prodava);$i++){
    print ',["'.$prodava[$i]['dd'].'"';
    foreach($fields as $key=>$value) if($value["Field"]!='id'&&$value["Field"]!='date') print ','.$prodava[$i][$value["Field"]].'';
    print ']';
}?>
        ]);
        var options = {title: 'Последните 10 дни'};
        var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>
    <div id="chart_div" style="width: 600px; height: 500px;float: left;margin-top:-40px;"></div>  
<?}
break;
default: 
$kupuva=$sql->fetch_array("select *, DATE_FORMAT(date,'%d.%m.%Y') as dd from kupuva order by date desc limit 2");
$prodava=$sql->fetch_array("select *, DATE_FORMAT(date,'%d.%m.%Y') as dd from prodava order by date desc limit 2");
print '<h3>Курсове за '.$kupuva[0]['dd'].'</h3>
<table style="width:300px;float: left;">
<tr><th>Валута</th><th>Купува</th><th>Продава</th></tr>';
$fields=$sql->fetch_array("show fields from kupuva");
foreach($fields as $key=>$value) if($value["Field"]!='id'&&$value["Field"]!='date')
print '<tr><td>'.$value["Field"].'</td><td>'.money_format('%.3n', $kupuva[0][$value["Field"]]).(razlika($kupuva[0][$value["Field"]],$kupuva[1][$value["Field"]])>5?' <span>(над 5% разлика)</span>':'').'</td><td>'.money_format('%.3n', $prodava[0][$value["Field"]]).(razlika($prodava[0][$value["Field"]],$prodava[1][$value["Field"]])>5?' <span>(над 5% разлика)</span>':'').'</td></tr>';
print '</table>';
$prodava=$sql->fetch_array("select *, DATE_FORMAT(date,'%d.%m.%Y') as dd from prodava order by date limit 10");?>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ["Date"<?foreach($fields as $key=>$value) if($value["Field"]!='id'&&$value["Field"]!='date') print ',"'.strtoupper($value["Field"]).'"';?>]
<?
for($i=0;$i<count($prodava);$i++){
    print ',["'.$prodava[$i]['dd'].'"';
    foreach($fields as $key=>$value) if($value["Field"]!='id'&&$value["Field"]!='date') print ','.$prodava[$i][$value["Field"]].'';
    print ']';
}?>
        ]);
        var options = {title: 'Последните 10 дни'};
        var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>
    <div id="chart_div" style="width: 600px; height: 500px;float: left;margin-top:-40px;"></div>
<?break;
}?>


</section>

<footer class="body">
<nav>
<ul>
      <li><a href="index.php">Начало</a></li>
      <li><a href="index.php?page=about">За нас</a></li>
      <li><a href="index.php?page=valuti">Валутни курсове</a></li>
      <li><a href="index.php?page=contacts">Контакти</a></li>
</ul>
</nav>
<div class="clear"></div>
<div class="af1">All Rights Reserved.</div>
<div class="clear"></div>
</footer>

</body>
</html>
