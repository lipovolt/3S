<?php

class EmailTemplateAction extends CommonAction{

	public function index($language){
		$de_templates = array(
			'Large Letter 规定时间内未收到货'=>'Hallo,<br>
				Ihre Artikel ist am XXXXXXXXXXXX per Deutsche Post Warensendung aus Bremen verschickt. Es gibt keine Verfolgungsnummer für die Sendungsart. Die automatisch hochgeladene Nummer ist nur eine Verarbeitungsnummer vom Lager und dient zur Markierung des Bestellungsstatus.<br>
					Die von Post gegebene durchschnittliche Lieferzeit für die Sendungsart beträgt ca. 3-8 Arbeitstage. Bitte warten Sie noch ein paar Tage. Wenn der Artikel bis die achte Arbeitstag noch nicht geliefert ist, bitte melden Sie noch mal per Email. Vielen Dank',
			'没有paypal， 询问其他付款方式'=>'Hallo,<br>
 				da ich bin ausländische Verkäufer und habe keine Bankverbindung in Deutschland. Deswegen  die Zahlung geht nur per Paypal. Falls Sie kein PayPal haben, storniere ich den Kauf?',
			'自提'=>'Hallo,<br>
				die Artikel sind zwar in Bremen gelegen. Aber das Lager ist von andere Frima gemietet. Und das Lager ist auch von der Firma gewaltet. Die bietet keine Abholung Service. Ich bitte um Ihre Verständnisse.',
			'已卖出，没库存，取消订单'=>'Hallo,<br>
				vielen Dank für Ihre Kauf. Ich habe gerade Nachtricht von Kollege im Lager bekommen dass der Artikel im Lager nicht gefunden werden kann. Um unnötige Warten Zeit zu vermeiden, ich werde den Kauf abbrechen. Ich bitte um Ihre Entschuldigung für den Umstände.',
			'顾客要求马上发货，在指定日期前收到'=>'Hallo,<br>
				vielen Dank für Ihre Kauf. Leider die Bestellung muss nach die standard Vorgang verarbeitet werden. Leider Ich (Kundendienst) kann die Verarbeitungsvorgang nicht beschleunigen. Vorraussichtlich die Bestellung wird am XXXXX  verschickt. Dann es ist unmöglich bevor die angeforderte Datum zuzustellen. Soll ich den Kauf abbrechen? ',
			'DHL出现延迟'=>'Hallo,<br>
				Ein Anstieg der Bestellungen aus Deutschland Bremen DHL Hub übersteigt die tägliche Einsatzfähigkeit, wodurch Ihre Bestellung Ascan und Dscan Verzögerung Tage Lieferzeit. Ich bitte um Ihre Entschuldigung für den Umstände.',
			'系统问题导致延迟出库'=>'Hallo,<br>
				vielen Dank für Ihre Kauf. Wegen der System Fehler, Ihre Bestellung wird 1-2 Tage verzögert auszuschicken. Ich bitte um Ihre Entschuldigung für die Verzögerung. Sobald die System Fehler beseitigt ist, schicken wir auch die Bestellung früher.',
			'顾客询问按照需要的数量购买'=>'Hallo,<br>
				alle Artikel sind fertig verpackt und in einem gemietete Lager in Bremen. Der Lager macht nur Versandaufgabe. Sie können den verpackte Artikel nicht öffnen oder umpacken. Deswegen ich kann den Artikel nicht nach Ihre Bedarf verkaufen. Ich bitte um Ihre Verständnisse.',
			'顾客要求含增值税发票'=>'Hallo,<br>
				wie bereits in jeder Artikel Beschreibung geklärt, wir sind ausländische Verkäufer und können nur Rechnung ohne MwSt. anbieten, weil wir keine MwSt. von Kunden übernehmen. <br>
					Die Netto Rechnung kann von der Link heruntergeladen.',
			'尺寸不符合DHL要求，是否可以等带再次发货'=>'Hallo,<br>
				Es gibt ein kleines Problem für Ihre Bestellung. Die gestern geschickte Packung erfüllt nicht die Verpackungsanforderungen von DHL. Der Bereit der Verpackung ist 10cm. Der DHL benötigt 11 cm. Deswegen die Sendung wird zurück zu uns geschickt. Wenn die Sendung zurück ist, verpacken wir den Artikel und noch mal verschicken. Aber es könnt noch 1-3 Tage dauern. <br>
					Können Sie dafür warten? Oder soll ich den Kauf abbrechen? Ich bitte um Ihre Entschuldigung für den Umstände.',
			'德国退货地址'=>'Amelie-Jie Tian<br>Hinsbecker Loeh 12<br>45257 Essen',
			'顾客要求取消订单，但是已经发货'=>'Hallo,

Ich habe gerade Ihre Abbruch Anfrage gesehen. Und die Anfrage war von System automatisch geantwortet. Ich möchte noch mal erklären warum die Bestellung nicht storniert werden kann.

Wir benutzen eines System. Das System lesen automatisch die Bestellungen von ebay und kaufen Versandschein von Post und leiten die Bestellungen an Lager weiter. Das System dient zur Beschleunigung der Verarbeitung der Bestellungen. 

Wenn ich Ihre Nachricht gesehen habe, Ihre Bestellung war schon von Post abgeholt. Deswegen kann Ihre Bestellung nicht storniert werde. Ich bitte um Ihre Verständnisse. ',
			'德国收款银行账户'=>'Bank name: Wirecard Bank AG<br>
								Bank Adresse: Einsteinring 35 85609 Aschheim, Germany<br>
								BIC: WIREDEMM<br>
								IBAN: DE24512308006501748392<br>
								Konto Inhaber: Yu Zhang',
			'新冠病毒导致延误，请顾客多等一周'=>'Hallo,
Ihre Artikel ist am #################### per Deutsche Post Warensendung aus Bremen verschickt. Normalerweise das Packet kann innerhalb 3-8 Arbeitstage zugestellt werden.
wegen der COVID-19 Virus, die Lieferzeit des Pakets kann nicht exakt der üblichen entsprechen. Die Bearbeitungszeit der Post ist jetzt stark verzögert. Bitte warten Sie eine Woche mehr.
Vielen Dank für Ihre Verständnisse.',
'新冠病毒导致延误'=>'Hallo,
Ihre Artikel ist am ################ per Deutsche Post Warensendung aus Bremen verschickt. Normaleriweise Post brauche ca. 3-8 Arbeitstage um das Packet zu zustellen. Aber wegen COVID-19 Virus die Lieferzeit von Post könnte länger dauern. Bitte warten Sie noch ein paar Tage. Vielen Dank',
			);
		$en_templates = array(
			'USPS 爆仓'=>'Hello,<br>
				Your order has been given to the USPS on xxx. I have just checked the tracking information. The parcel is delayed to be scanned by USPS. <br>
				A reply of USPS is <br>
				"An increase in orders exceeds the USPS daily operational capability, making your order delay a few days delivery time."<br>
				Because of the shopping season and holidays, the USPS can’t process all parcels timely. So I ask for your understanding and wait a few days more. Thanks',
			'自提'=>'Hello,<br>
				You can pick up the item from our warehouse. But please confirm and pay the order on ebay, because ebay does not allow us to make deal bypass ebay.  Otherwise my seller account will be blocked.<br>
				Leave a notice in the order that you will pick up the item self. And tell me timely that you have ordered the item. I will remind colleagues in warehouse again, Do not ship the order.<br>
				Then I will give you our warehouse address. You can contact colleague to appointed pickup time.',
			'没库存，取消订单'=>'Hello,<br>
				Thanks for your purchasing. I just received message from warehouse, this item can not be found in warehouse. To avoid long waiting time, i will cancel the order. Apologize for the inconvenience.',
			'没库存，询问是否可以等待'=>'Hello,<br>
				Thanks for your purchasing. I have listed wrong quantity of the item. It is now out of stock. The new item will be restocked in one week. Can you wait? Or i should cancel the order? Apologize for the inconvenience.',
			'订单已发走，无法取消订单'=>'Hello,<br>
				I just see the cancellation request. Then I immediately contacted warehouse to confirm the order status. But the package has been given to USPS. I am very sorry I can not cancel the order. Apologize for the inconvenience.',
			'包裹已送达邮局，没有A-Scan'=>'Hello,<br>
				Your package has been given to the local post office. Tracking number is XXXXXXXXXXXXXXX. Sometime they do not do A-scan and transit all packages together to package processing center. So you can not see the tracking information immediately. But the package is in transit.',
			'Fisrt Class Mail显示已投递，顾客说没收到'=>'Hello, This should be a issue of the local postman.The package may be delivered to wrong mailbox or forgotten in the mail truck. Could you see the local postman and confirm the package status? 

Item is in yellow bubble envelope 4.5" x 7"',
			'Fisrt Class Mail显示已投递，顾客说没收到,让我们联系邮局'=>'Hello,

I can go to my local post office to ask the package status. But they can not give helpful information for this problem.

This should be a issue of the postman, who is responsible for your local. The package may be delivered to wrong mailbox or forgotten in the mail truck. The best way to resolve the problem is to ask the local postman. He should remember this package. Item is in black plastic bag 4.5" x 8"',
			'亚马逊跟卖警告1'=>'Hello，<br>
				We are the registered proprietor of the USA Trade Mark Number 4996636 for LIPOVOLT in classes 9（Registered Date： July-12 2016）.Please refrain from using our Trademark when selling or listing any of your products.<br>
				Please remove your listing from our advert immediately ASIN: *******<br>
				The product is branded LIPOVOLT to reflect this, along with the title, brand and manufacturer.<br>
				If this is not done, we will have to report you to Amazon and they will remove it on your behalf. Please note:If a seller is removed 3 times by Amazon, the selling privilege would be removed by Amazon.<br>				
				LIPOVOLT',
			'美自建仓发货时间'=>"Hello，<br>
				The order before Pacific time 9 a.m. will be sent on the same working day. Otherwise the order will be sent on the next working day.",
			'未发货订单换地址，取消，重拍'=>"Hello，<br>
				I (customer service) can't direct change the shipping address. I have cancelled the order, please make a new order with correct address. Thanks",
			'美国退货地址'=>'Min Zhang<br>13754 mango dr unit 303, del mar, ca 92014<br>626-203-7018<br>New Address:<br>7960 Silverton Avenue, Suite 107，San Diego, CA 92126',
			"发货时间或指定日期到货"=>"the order before 9 o'clock will be sent on the same working day. Otherwise the order will be sent on the next working day. I regret I can't guarantee the transportation speed of USPS. I ask for your understanding.",
			"ebay global shipping 解释"=>"Hello,<br>
			Your order is shipped through ebay global shipping program. That means we send the item to ebay warehouse in USA. Then ebay send the item from USA to you. The tracking number 9400109699938567132513 is for the first way. The item is delivered to ebay warehouse. Now the item will be shipped to you. But when the ebay will ship the item and what is the tracking number, I don't know yet. You can wait a few days. Or direct contact ebay online customer service.",
			"发货时间或指定日期到货"=>"the order before 9 o'clock will be sent on the same working day. Otherwise the order will be sent on the next working day. I regret I can't guarantee the transportation speed of USPS. I ask for your understanding.",
			"COVID-19导致投递时效延误解释"=>"Hello,<br>
			The package has been given to the USPS on XXXXXXXXXXXXXXX. Because of the COVID-19 Viren, The processing speed of the post office is affected. Please wait a few more days. Thank you for your patience.",
			"货物已经交给邮差，邮局未扫描的解释"=>"Hello,<br>
			The package has been given to postman on XXXXXXXXXXXXXXX. The updating of tracking information 'SHIPMENT RECEIVED ACCEPTANCE PENDING' is from the postman who has picked up the package from our warehouse.
			Then the postman bring the package to USPS processing center. If the package is scanned in processing center, the tracking information will be updated with the information 'ORIGIN ACCEPTANCE'.",
			);
		if($language=='de'){
			$this->assign('templates', $de_templates);
		}elseif($language=='en'){
			$this->assign('templates', $en_templates);
		}		
        $this->display();
	}
}

?>