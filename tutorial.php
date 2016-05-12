<?php
session_start();
include_once 'common.php';
?>

<html ng-app="tutorialApp">
<head>
	<meta charset="utf-8" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<style type="text/css">
	span:not(.glyphicon-home){
		margin: -10px 0 20px 0;
	}
	img:not(.flag){
		width: 100%;
	}
	</style>
</head>
<body ng-cloak>
	<div class="container" ng-app ng-controller="MyCtrl" >
		<div class="row">
			<div class="col-md-3 col-lg-3" ng-init="lang = '<?php echo $_COOKIE['lang']; ?>'">
				<a href="index.php?lang=en"><img class="flag" src="images/eng.png" /></a>
				<a href="index.php?lang=ee"><img class="flag" src="images/est.jpg" /></a>
				<div>
					<span class="glyphicon glyphicon-home" style="cursor: pointer; font-size: 25px;" onclick="window.location.assign('index.php')"></span>
				</div>
			</div>

			<div id="mainContent" class="col-md-6 col-lg-6" style="text-align: justify">
				<div ng-show="lang != 'ee'">
					Sorry! Instructions are only available in Estonian.<br>
					<a style="cursor: pointer" ng-click="lang = 'ee'">Click here if you want to see them in Estonian</a>
				</div>
				<div ng-show="lang == 'ee'">
					<h1>Kuidas rakendust kasutada?</h1>
					<h3>Uue mängu loomine: </h3>
					<p>Uue mängu loomiseks tuleb minna pealehel ja vajutada nupule: "Loo uus mäng"</p>
					<span class="glyphicon glyphicon-picture" style="font-size: 20px;" ng-hide="show1" ng-click="show1 = true;"></span>
					<span class="glyphicon glyphicon-picture" style="font-size: 20px; color:#5bc0de" ng-click="show1 = false;" ng-show="show1"></span>
					<img src="images/help1.gif" ng-if="show1">
					<p>Seejärel tuleb vajutada spordialale millele soovite statistikat teha</p>
					<span class="glyphicon glyphicon-picture" style="font-size: 20px;" ng-hide="show2" ng-click="show2 = true;"></span>
					<span class="glyphicon glyphicon-picture" style="font-size: 20px; color:#5bc0de" ng-click="show2 = false;" ng-show="show2"></span>
					<img src="images/help2.gif" ng-if="show2">
					<p>Nüüd tuleb valida mängule sätted</p>
					<p>Sätted on jaotatud gruppidesse. Iga grupi sätted avanevad kui vajutada selle grupi päise kõrval olevale noolekesele</p>
					<span class="glyphicon glyphicon-picture" style="font-size: 20px;" ng-hide="show3" ng-click="show3 = true;"></span>
					<span class="glyphicon glyphicon-picture" style="font-size: 20px; color:#5bc0de" ng-click="show3 = false;" ng-show="show3"></span>
					<img src="images/help3.gif" ng-if="show3">
					<p>Kui vastavad valikud on tehtud siis tuleb vajutada nupule "Edasi"</p>
					<p>Nüüd tuleb seadistada tuleva mängu võistkonnad</p>
					<p>Võistkondade sisestamiseks on kaks võimalust:</p>
					<ol>
						<li>
							<p>Sisestada mängijad käsitsi üks haaval</p>
							<p>Selleks tuleb valida ülevalt äärest kumba võistkonda te soovita mängijat lisada</p>
							<p>Sisestada lahtrid mängija särginumbri ja mängija nime jaoks</p>
							<p>Vajutada nupule "Lisa mängija"</p>
							<p>Sisestatud mängija kuvatakse vastavas võistkonnas</p>
							<span class="glyphicon glyphicon-picture" style="font-size: 20px;" ng-hide="show4" ng-click="show4 = true;"></span>
							<span class="glyphicon glyphicon-picture" style="font-size: 20px; color:#5bc0de" ng-click="show4 = false;" ng-show="show4"></span>
							<img src="images/help4.gif" ng-if="show4">
						</li>
						<li>	
							<p>Terve meeskonna sisestamine korraga failist</p>
							<p>Selleks tuleb vajutada nupu peale "Vali koduvõistkonna fail", kui soovid sisestada koduvõistkonda ning "Vali võõrsil võistkonna fail", võõrsil võistkonna jaoks</p>
							<p>Seejärel tuleb valida avanenud aknast meeskonna fail ning mängijad tekivadki vastava võistkonna nimekirja</p>
							<p>Milline peab fail välja nägema?</p>
							<p>Fail peab olema salvestatud ".txt" laiendiga</p>
							<p>Faili sisu peab olema selline:</p>
							<span class="glyphicon glyphicon-picture" style="font-size: 20px;" ng-hide="show5" ng-click="show5 = true;"></span>
							<span class="glyphicon glyphicon-picture" style="font-size: 20px; color:#5bc0de" ng-click="show5 = false;" ng-show="show5"></span>
							<img src="images/txt_fail.PNG" ng-if="show5"/>
							<p>Esimesel real on meeskonna nimi</p>
							<p>Järgmistel ridadel selle meeskonna mängijad. Alguses nimi ja siis mängija särgi number</p>
							<span class="glyphicon glyphicon-picture" style="font-size: 20px;" ng-hide="show6" ng-click="show6 = true;"></span>
							<span class="glyphicon glyphicon-picture" style="font-size: 20px; color:#5bc0de" ng-click="show6 = false;" ng-show="show6"></span>
							<img src="images/help6.gif" ng-if="show6">
						</li>	
					</ol>
					<p>Lisaks võistkonna mängijatele saab muuta ka võistkonna nime ja värvi. Selleks vajuta meeskonna nime rea peale, et avada muutmisvõimalus</p>
					<span class="glyphicon glyphicon-picture" style="font-size: 20px;" ng-hide="show7" ng-click="show7 = true;"></span>
					<span class="glyphicon glyphicon-picture" style="font-size: 20px; color:#5bc0de" ng-click="show7 = false;" ng-show="show7"></span>
					<img src="images/help5.gif" ng-if="show7">
					<p>Võistkonnast on võimalik ka kustutada mängijaid. Ühe mängija kustutamiseks vajuta selle mängija kõrval olevale ristile. Terve meeskonna kustutamiseks vajuta nupule "Tühjenda meeskond"</p>
					<span class="glyphicon glyphicon-picture" style="font-size: 20px;" ng-hide="show8" ng-click="show8 = true;"></span>
					<span class="glyphicon glyphicon-picture" style="font-size: 20px; color:#5bc0de" ng-click="show8 = false;" ng-show="show8"></span>
					<img src="images/help7.gif" ng-if="show8">
					<p>Kui võistkonnad on sätestatud tuleb vajutada nupule "Salvesta seaded"</p>
					<p>Edasi suunatakse Teid statistika tegemise lehele</p>
					<p>Statistika tegemine on väga lihtne</p>
					<p>Kõigepealt tuleb valida mängijad kes on hetkel väljakul</p>
					<p>Selleks, et seda teha tuleb võistkondade tabeli esimeses veerus olevatesse kastidesse teha linnuke nende mängijate ette, kes hetkel väljakul viibiva </p>
					<span class="glyphicon glyphicon-picture" style="font-size: 20px;" ng-hide="show9" ng-click="show9 = true;"></span>
					<span class="glyphicon glyphicon-picture" style="font-size: 20px; color:#5bc0de" ng-click="show9 = false;" ng-show="show9"></span>
					<img src="images/help8.gif" ng-if="show9">
					<p>Kui mängus viibivad mängijad on valitud võib statistika tegemine pihta hakata</p>
					<span class="glyphicon glyphicon-picture" style="font-size: 20px;" ng-hide="show10" ng-click="show10 = true;"></span>
					<span class="glyphicon glyphicon-picture" style="font-size: 20px; color:#5bc0de" ng-click="show10 = false;" ng-show="show10"></span>
					<img src="images/help9.gif" ng-if="show10">
					<p>Kui mängus viibivad mängijad on valitud võib statistika tegemine pihta hakata</p>

					<p>Statistika lisamise põhimõte on järgmine</p>
					<ul>
						<li>Kõigepealt tuleb valida mees, kellele soovid statistikat lisada</li>
						<li>Ning seejärel tuleb valida statistiline element, mida sellele mängijale soovid lisada</li>
					</ul>
					<span class="glyphicon glyphicon-picture" style="font-size: 20px;" ng-hide="show11" ng-click="show11 = true;"></span>
					<span class="glyphicon glyphicon-picture" style="font-size: 20px; color:#5bc0de" ng-click="show11 = false;" ng-show="show11"></span>
					<img src="images/help10.gif" ng-if="show11">
					<p>Hetkel aktiivset mängijat näed mängijate nuppude paneelis rohelisena</p>
					<p>Hetkel mängus viibivaid mängijaid näed aga punasena</p>
					<p>Kui on vaja lisada mingi statistilne element ainult võistkonnale, saab seda teha vajutades meeskonna nime peale</p>
					<span class="glyphicon glyphicon-picture" style="font-size: 20px;" ng-hide="show12" ng-click="show12 = true;"></span>
					<span class="glyphicon glyphicon-picture" style="font-size: 20px; color:#5bc0de" ng-click="show12 = false;" ng-show="show12"></span>
					<img src="images/help14.gif" ng-if="show12">
					<p>Kui midagi läks valesti, on võimalus viimane sissekanne kustutada. Selleks tuleb vajutada mängu logi kastikeses punase risti peale</p>
					<span class="glyphicon glyphicon-picture" style="font-size: 20px;" ng-hide="show13" ng-click="show13 = true;"></span>
					<span class="glyphicon glyphicon-picture" style="font-size: 20px; color:#5bc0de" ng-click="show13 = false;" ng-show="show13"></span>
					<img src="images/help11.gif" ng-if="show13">	
					<p>Kui aga selgub, et midagi on juba ammu valesti läinud. Siis saab statistikat muuta ka tabelis sellele numbrile peale klõpsates. Niimoodi käitudes aga ei kustu vastav sissekanne mängu logist.</p>
					<span class="glyphicon glyphicon-picture" style="font-size: 20px;" ng-hide="show14" ng-click="show14 = true;"></span>
					<span class="glyphicon glyphicon-picture" style="font-size: 20px; color:#5bc0de" ng-click="show14 = false;" ng-show="show14"></span>
					<img src="images/help13.gif"  ng-if="show14">
					<p>Mängu kella saab muuta kui vajutada selle peale, määrata õiged minutid ja sekundid ning vajutada linnukesele valiku kõrval</p>
					<span class="glyphicon glyphicon-picture" style="font-size: 20px;" ng-hide="show15" ng-click="show15 = true;"></span>
					<span class="glyphicon glyphicon-picture" style="font-size: 20px; color:#5bc0de" ng-click="show15 = false;" ng-show="show15"></span>
					<img src="images/help12.gif" ng-if="show15">
					<p>Statistika tegemise lehe allosas leiad järgnevad võimalused</p>
					<ul>
						<li>Link, mida avades on võimalik mängu statistikat näha reaalajas. Seda võib vabalt sõprade/tuttavatega jagada. Läbi selle lehe midagi statistikas muuta ei saa.</li>
						<li>Igal mängu hetkel on võimalus alla laadida hetkeline mängu statistika ja mängu logi</li>
					</ul>
					<p>Kui mäng on lõppenud siis tuleb vajutada nupule "Lõpeta mäng". Siis salvestatakse teie tehtud mäng andmebaasi ning pääsete sellele hiljem ligi pealehelt "Minu mängude" nupu alt.</p>
				</div>
			</div>

			<div class="col-md-3 col-lg-3"></div>

		</div>
	</div>
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.9/angular.min.js"></script>
	<script src="js/tutorialApp.js"></script
	</body>
	</html>