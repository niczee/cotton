<?

//YOU CAN CHANGE THESE
$mailTo = "joan@cottonlandtitle.com";
$mailFrom = "Joan@cottonlandtitle.com";
$mailCc = "joan@cottongates.com";
$mailFromName = "Cotton Land Title";
$mailSubject = "New Title Insurance Enquiry";

$surcharge = 3.28;
$settlementfee = 350;
$titlesearch = 75;
$secondarypolicy = 25;
$liensearch = 55;
$endorse81 = 25;
$endorsePUD = 25;
$endorseCONDO = 25;
//BUT DONT CHANGE ANYTHING ELSE...

$title1 = $_POST['title1'];
$title2 = $_POST['title2'];
$buyer = $_POST['buyer'];
$seller = $_POST['seller'];
$address = $_POST['address'];
$sales = $_POST['salesprice'];
$mortgage = $_POST['mortgageamt'];
$lender = $_POST['lender'];
$real1 = $_POST['realtor1'];
$real2 = $_POST['realtor2'];

$msg;

function c($c){
	return '$'.number_format($c,2);
}

if(isset($_POST['submit'])){
	$tmpName = $_FILES['contract']['tmp_name'];
	$fileName = $_FILES['contract']['name'];
	$fileType = $_FILES['contract']['type'];
	$ext = pathinfo(basename($fileName), PATHINFO_EXTENSION);
	if(!in_array($ext, array('doc','docx','pdf','xlsx','png','jpg'))){
		$msg = "Invalid File Type";
	}else{
		$title1 = floatval($title1);
		$title2 = floatval($title2);
		$headers = array(); $message = array();
		$headers[] = "MIME-Version: 1.0";
		$headers[] = "From: {$mailFromName} <{$mailFrom}>";
		$headers[] = "Cc: {$mailCc}";
		$headers[] = "Subject: {$subject}";
		$headers[] = "X-Mailer: PHP/".phpversion();
		$uid = md5(uniqid(time()));
		$headers[] = "Content-Type: multipart/mixed; boundary=\"{$uid}\"";
		$message[] = "This is a multi-part message in MIME format.";
		$body = '<h2>Insurance Quote</h2>';
		$body .= "<strong>Purchase Price / Owner's Title:</strong> ".c($title1)."<br/>";
		$body .= "<strong>Mortgage Amount / Lender's Title:</strong> ".c($title2)."<br/><br/>";
		//recalculate
		$total = 428.28;
		$t1 = ($title1>=$title2?$title1:$title2);
		$t2 = ($title1>=$title2?$title2:$title1);
		$premium = 0;
		//0-100000 @ 5.75
		$temp = 0;
		$v = (($t1/1000)*5.75);
		if($t1<100000) $temp = ($v<100?100:$v);
		else if($t1>=100000) $temp = 575;
		$premium += $temp;
		$body .= '<strong>$0 up to $100,000 - $5.75 per $1000 (min $100):</strong> '.c($temp).'<br/>';
		//100000-1000000 @ 5
		$temp = 0;
		if($t1>100000 && $t1<=1000000) $temp = ((($t1-100000)/1000)*5);
		else if($t1>1000000) $temp = 4500;
		$premium += $temp;
		$body .= '<strong>Over $100,000 up to $1 Million - $5.00 per $1000:</strong> '.c($temp).'<br/>';
		//1000000-5000000 @ 2.5
		$temp = 0;
		if($t1>1000000 && $t1<=5000000) $temp = ((($t1-1000000)/1000)*2.5);
		else if($t1>5000000) $temp = 10000;
		$premium += $temp;
		$body .= '<strong>Over $1 Million up to $5 Million - $2.50 per $1000:</strong> '.c($temp).'<br/>';
		//5000000-10000000 @ 2.25
		$temp = 0;
		if($t1>5000000 && $t1<=10000000) $temp = ((($t1-5000000)/1000)*2.25);
		else if($t1>10000000) $temp = 11250;
		$premium += $temp;
		$body .= '<strong>Over $5 Million up to $10 Million - $2.25 per $1000:</strong> '.c($temp).'<br/>';
		//>10000000 @ 2
		$temp = 0;
		if($t1>10000000) $temp = ((($t1-10000000)/1000)*2);
		$premium += $temp;
		$body .= '<strong>Over $10 Million - $2.00 per $1000:</strong> '.c($temp).'<br/><br/>';
		$body .= '<strong>Surcharge:</strong> '.c($surcharge).'<br/>';
		$body .= '<strong>Settlement Fee:</strong> '.c($settlementfee).'<br/>';
		$body .= '<strong>Title Search:</strong> '.c($titlesearch).'<br/>';
		//secondary policy
		if($t2>0 && $t2<=$t1){
			$premium += 25;
			$body .= '<strong>Secondary Policy:</strong> '.c($secondarypolicy).'<br/>';
		}
		//lien
		if($_POST['lien']=='on'){
			$total += 55;
			$body .= '<strong>Lien Search:</strong> '.c($liensearch).'<br/>';
		}
		//8.1
		if($_POST['endor81']=='on'){
			$total += 25;
			$body .= '<strong>8.1:</strong> '.c($endorse81).'<br/>';
		}
		//fl9
		if($_POST['endorFL9']=='on'){
			$fl9 = ($premium*0.1);
			$total += $fl9;
			$body .= '<strong>FL 9:</strong> '.c($fl9).'<br/>';
		}
		//pud
		if($_POST['endorPUD']=='on'){
			$total += 25;
			$body .= '<strong>PUD:</strong> '.c($endorsePUD).'<br/>';
		}
		//condo
		if($_POST['endorCONDO']=='on'){
			$total += 25;
			$body .= '<strong>CONDO:</strong> '.c($endorseCONDO).'<br/>';
		}
		//
		$total += $premium;
		$body .= '<h3><strong>Total:</strong> '.c($total).'</h3>';
		$body .= '<h2>Contact Information</h2>';
		$body .= '<strong>Buyer:</strong> '.$buyer.'<br/>';
		$body .= '<strong>Seller:</strong> '.$seller.'<br/>';
		$body .= '<strong>Property Address:</strong> '.$address.'<br/>';
		$body .= '<strong>Sales Price:</strong> '.c($sales).'<br/>';
		$body .= '<strong>Mortgage Amount:</strong> '.c($mortgage).'<br/>';
		$body .= '<strong>Lender Name:</strong> '.$lender.'<br/>';
		$body .= '<strong>Realtor 1:</strong> '.$real1.'<br/>';
		$body .= '<strong>Realtor 2:</strong> '.$real2.'<br/>';
		$message[] = "--{$uid}";
		$message[] = "Content-Type: text/html; charset=ISO-8859-1";
		$message[] = "Content-Transfer-Encoding: 7bit\r\n";
		$message[] = $body;
		//upload contract
		$file = fopen($tmpName,'rb');
		$data = chunk_split(base64_encode(fread($file,filesize($tmpName))));
		fclose($file);
		$message[] = "\r\n--{$uid}";
		$message[] = "Content-Type: {$fileType}; name=\"{$fileName}\"";
		$message[] = "Content-Transfer-Encoding: base64\r\n";
		$message[] = $data;
		$message[] = "\r\n--{$uid}--";
		if(mail($mailTo, $mailSubject, implode("\r\n", $message), implode("\r\n", $headers))){
			$msg = "Enquiry Sent";
		}else $msg = "Could not send enquiry";
	}
}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<style>
			table {
				width: 100%;
			}
			#tblContact {
				margin-top: 20px;
			}
			button[type="submit"] {
				float: right;
			}
			table td {
				padding: 2px 0;
			}
			input[type="text"] {
				width: 100%;
			}
			#lien {
				float: right;
			}
			#totalRow {
				font-size: 20px;
			}
			input[type="text"] {
				background-color: #f9eadd;
				border: 0 solid #301c0d;
				border-radius: 0;
				box-shadow: 0 1px 4px 0 rgba(0, 0, 0, 0.6);
				box-sizing: border-box;
				color: #60391a;
				font: 14px/1.4em georgia,palatino,"book antiqua","palatino linotype",serif;
				padding: 5px;
				width: 100%;
			}
			button {
				background-color: #ddc3af;
				border: medium none;
				border-radius: 0;
				box-shadow: 0 1px 4px 0 rgba(0, 0, 0, 0.6);
				color: #60391a;
				cursor: pointer;
				display: inline-block;
				font: 18px/1.4em georgia,palatino,"book antiqua","palatino linotype",serif;
				max-width: 35%;
				padding: 5px;
				vertical-align: top;
			}
			label {
				box-sizing: border-box;
				color: #60391a;
				font: 14px/1.4em georgia,palatino,"book antiqua","palatino linotype",serif;
			}	
		</style>
	</head>
	<body>
		<h3 style="text-align:center;"><? echo $msg; ?></h3>
		<form method="post" enctype="multipart/form-data">
			<table id="tblRate">
				<tr>
					<td><label>Purchase Price / Owner's Title</label></td>
					<td><input type="text" name="title1" id="title1" value="<? echo $title1; ?>"/></td>
				</tr>
				<tr>
					<td><label>Mortgage Amount / Lender's Title Insurance</label></td>
					<td><input type="text" name="title2" id="title2" value="<? echo $title2; ?>"/></td>
				</tr>
				<tr>
					<td colspan="2">
						<label>Endorsements</label><br/>
						<input type="checkbox" name="endor81" id="endor81"<? echo(isset($_POST['submit'])?($_POST['endor81']=='on'?' checked="checked"':''):' checked="checked"'); ?>/>8.1<br/>
						<input type="checkbox" name="endorFL9" id="endorFL9"<? echo(isset($_POST['submit'])?($_POST['endorFL9']=='on'?' checked="checked"':''):' checked="checked"'); ?>/>FL 9<br/>
						<input type="checkbox" name="endorPUD" id="endorPUD"<? echo($_POST['endorPUD']=='on'?' checked="checked"':''); ?>/>PUD<br/>
						<input type="checkbox" name="endorCONDO" id="endorCONDO"<? echo($_POST['endorCONDO']=='on'?' checked="checked"':''); ?>/>CONDO<br/>
					</td>
				</tr>
				<tr>
					<td>Surcharge</td>
					<td><? echo c($surcharge); ?></td>
				</tr>
				<tr>
					<td>Settlement Fee</td>
					<td><? echo c($settlementfee); ?></td>
				</tr>
				<tr>
					<td>Title Search</td>
					<td><? echo c($titlesearch); ?></td>
				</tr>
				<tr id="secondpolicy">
					<td>Secondary Policy</td>
					<td><? echo c($secondarypolicy); ?></td>
				</tr>
				<tr>
					<td>Lien Search<input type="checkbox" name="lien" id="lien"<? echo($_POST['lien']=='on'?' checked="checked"':''); ?>/></td>
					<td><? echo c($liensearch); ?></td>
				</tr>
				<tr id="81">
					<td>8.1</td>
					<td><? echo c($endorse81); ?></td>
				</tr>
				<tr>
					<td>FL 9</td>
					<td>$<span id="fl9"></span></td>
				</tr>
				<tr id="pud">
					<td>PUD</td>
					<td><? echo c($endorsePUD); ?></td>
				</tr>
				<tr id="condo">
					<td>CONDO</td>
					<td><? echo c($endorseCONDO); ?></td>
				</tr>
				<tr id="totalRow">
					<td><strong>Total</strong></td>
					<td><strong>$<span id="total"></span></strong></td>
				</tr>
			</table>
			<table id="tblContact">
				<tr>
					<td colspan="2"><label id="quotenote" style="width:100%;text-align:center;font-weight:bold;font-size:12px;">You can now place your order electronically by completing the fields below.</label></td>
				</tr>
				<tr>
					<td><label>Buyer</label></td>
					<td><input type="text" name="buyer" id="buyer" value="<? echo $buyer; ?>"/></td>
				</tr>
				<tr>
					<td><label>Seller</label></td>
					<td><input type="text" name="seller" id="seller" value="<? echo $seller; ?>"/></td>
				</tr>
				<tr>
					<td><label>Property Address</label></td>
					<td><input type="text" name="address" id="address" value="<? echo $address; ?>"/></td>
				</tr>
				<tr>
					<td><label>Sales Price</label></td>
					<td><input type="text" name="salesprice" id="salesprice" value="<? echo $sales; ?>"/></td>
				</tr>
				<tr>
					<td><label>Mortgage Amount</label></td>
					<td><input type="text" name="mortgageamt" id="mortgageamt" value="<? echo $mortgage; ?>"/></td>
				</tr>
				<tr>
					<td><label>Lender Name</label></td>
					<td><input type="text" name="lender" id="lender" value="<? echo $lender; ?>"/></td>
				</tr>
				<tr>
					<td><label>Realtor Name 1</label></td>
					<td><input type="text" name="realtor1" id="realtor1" value="<? echo $real1; ?>"/></td>
				</tr>
				<tr>
					<td><label>Realtor Name 2</label></td>
					<td><input type="text" name="realtor2" id="realtor2" value="<? echo $real2; ?>"/></td>
				</tr>
				<tr>
					<td><label>Contract</label></td>
					<td><input type="file" name="contract" id="contract" value="<? echo $_POST['contract']; ?>"/></td>
				</tr>
				<tr>
					<td colspan="2">
						<button type="submit">Send</button>
						<input type="hidden" name="submit"/>
					</td>
				</tr>
			</table>
		</form>
	</body>
	<!-- jQuery --><script type="text/javascript" src="//code.jquery.com/jquery-1.11.0.min.js"></script>
	<!-- numeric --><script type="text/javascript">(function($){$.fn.numeric=function(config,callback){if(typeof config==="boolean"){config={decimal:config,negative:true,decimalPlaces:-1}}config=config||{};if(typeof config.negative=="undefined"){config.negative=true}var decimal=config.decimal===false?"":config.decimal||".";var negative=config.negative===true?true:false;var decimalPlaces=typeof config.decimalPlaces=="undefined"?-1:config.decimalPlaces;callback=typeof callback=="function"?callback:function(){};return this.data("numeric.decimal",decimal).data("numeric.negative",negative).data("numeric.callback",callback).data("numeric.decimalPlaces",decimalPlaces).keypress($.fn.numeric.keypress).keyup($.fn.numeric.keyup).blur($.fn.numeric.blur)};$.fn.numeric.keypress=function(e){var decimal=$.data(this,"numeric.decimal");var negative=$.data(this,"numeric.negative");var decimalPlaces=$.data(this,"numeric.decimalPlaces");var key=e.charCode?e.charCode:e.keyCode?e.keyCode:0;if(key==13&&this.nodeName.toLowerCase()=="input"){return true}else if(key==13){return false}var allow=false;if(e.ctrlKey&&key==97||e.ctrlKey&&key==65){return true}if(e.ctrlKey&&key==120||e.ctrlKey&&key==88){return true}if(e.ctrlKey&&key==99||e.ctrlKey&&key==67){return true}if(e.ctrlKey&&key==122||e.ctrlKey&&key==90){return true}if(e.ctrlKey&&key==118||e.ctrlKey&&key==86||e.shiftKey&&key==45){return true}if(key<48||key>57){var value=$(this).val();if($.inArray("-",value.split(""))!==0&&negative&&key==45&&(value.length===0||parseInt($.fn.getSelectionStart(this),10)===0)){return true}if(decimal&&key==decimal.charCodeAt(0)&&$.inArray(decimal,value.split(""))!=-1){allow=false}if(key!=8&&key!=9&&key!=13&&key!=35&&key!=36&&key!=37&&key!=39&&key!=46){allow=false}else{if(typeof e.charCode!="undefined"){if(e.keyCode==e.which&&e.which!==0){allow=true;if(e.which==46){allow=false}}else if(e.keyCode!==0&&e.charCode===0&&e.which===0){allow=true}}}if(decimal&&key==decimal.charCodeAt(0)){if($.inArray(decimal,value.split(""))==-1){allow=true}else{allow=false}}}else{allow=true;if(decimal&&decimalPlaces>0){var dot=$.inArray(decimal,$(this).val().split(""));if(dot>=0&&$(this).val().length>dot+decimalPlaces){allow=false}}}return allow};$.fn.numeric.keyup=function(e){var val=$(this).val();if(val&&val.length>0){var carat=$.fn.getSelectionStart(this);var selectionEnd=$.fn.getSelectionEnd(this);var decimal=$.data(this,"numeric.decimal");var negative=$.data(this,"numeric.negative");var decimalPlaces=$.data(this,"numeric.decimalPlaces");if(decimal!==""&&decimal!==null){var dot=$.inArray(decimal,val.split(""));if(dot===0){this.value="0"+val;carat++;selectionEnd++}if(dot==1&&val.charAt(0)=="-"){this.value="-0"+val.substring(1);carat++;selectionEnd++}val=this.value}var validChars=[0,1,2,3,4,5,6,7,8,9,"-",decimal];var length=val.length;for(var i=length-1;i>=0;i--){var ch=val.charAt(i);if(i!==0&&ch=="-"){val=val.substring(0,i)+val.substring(i+1)}else if(i===0&&!negative&&ch=="-"){val=val.substring(1)}var validChar=false;for(var j=0;j<validChars.length;j++){if(ch==validChars[j]){validChar=true;break}}if(!validChar||ch==" "){val=val.substring(0,i)+val.substring(i+1)}}var firstDecimal=$.inArray(decimal,val.split(""));if(firstDecimal>0){for(var k=length-1;k>firstDecimal;k--){var chch=val.charAt(k);if(chch==decimal){val=val.substring(0,k)+val.substring(k+1)}}}if(decimal&&decimalPlaces>0){var dot=$.inArray(decimal,val.split(""));if(dot>=0){val=val.substring(0,dot+decimalPlaces+1);selectionEnd=Math.min(val.length,selectionEnd)}}this.value=val;$.fn.setSelection(this,[carat,selectionEnd])}};$.fn.numeric.blur=function(){var decimal=$.data(this,"numeric.decimal");var callback=$.data(this,"numeric.callback");var negative=$.data(this,"numeric.negative");var val=this.value;if(val!==""){var re=new RegExp(negative?"-?":""+"^\\d+$|^\\d*"+decimal+"\\d+$");if(!re.exec(val)){callback.apply(this)}}};$.fn.removeNumeric=function(){return this.data("numeric.decimal",null).data("numeric.negative",null).data("numeric.callback",null).data("numeric.decimalPlaces",null).unbind("keypress",$.fn.numeric.keypress).unbind("keyup",$.fn.numeric.keyup).unbind("blur",$.fn.numeric.blur)};$.fn.getSelectionStart=function(o){if(o.type==="number"){return undefined}else if(o.createTextRange&&document.selection){var r=document.selection.createRange().duplicate();r.moveEnd("character",o.value.length);if(r.text=="")return o.value.length;return Math.max(0,o.value.lastIndexOf(r.text))}else{try{return o.selectionStart}catch(e){return 0}}};$.fn.getSelectionEnd=function(o){if(o.type==="number"){return undefined}else if(o.createTextRange&&document.selection){var r=document.selection.createRange().duplicate();r.moveStart("character",-o.value.length);return r.text.length}else return o.selectionEnd};$.fn.setSelection=function(o,p){if(typeof p=="number"){p=[p,p]}if(p&&p.constructor==Array&&p.length==2){if(o.type==="number"){o.focus()}else if(o.createTextRange){var r=o.createTextRange();r.collapse(true);r.moveStart("character",p[0]);r.moveEnd("character",p[1]-p[0]);r.select()}else{o.focus();try{if(o.setSelectionRange){o.setSelectionRange(p[0],p[1])}}catch(e){}}}}})(jQuery);</script>
	<!-- validation --><script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.13.1/jquery.validate.min.js"></script>
	<script type="text/javascript">function commatize(c){for(;/(\d+)(\d{3})/.test(c.toString());)c=c.toString().replace(/(\d+)(\d{3})/,"$1,$2");return c} function calc(){var c=<? echo floatval($surcharge+$settlementfee+$titlesearch) ?>,a=parseFloat($("#title1").val()),d=parseFloat($("#title2").val()),a=isNaN(a)?0:a,d=isNaN(d)?0:d,b=a>=d?a:d,d=a>=d?d:a,a=0,e=b/1E3*5.75;1E5>b?a+=100>e?100:e:1E5<=b&&(a+=575);1E5<b&&1E6>=b?a+=(b-1E5)/1E3*5:1E6<b&&(a+=4500);1E6<b&&5E6>=b?a+=(b-1E6)/1E3*2.5:5E6<b&&(a+=1E4);5E6<b&&1E7>=b?a+=(b-5E6)/1E3*2.25:1E7<b&&(a+=11250);1E7<b&&(a+=(b-1E7)/1E3*2);$("#lien").prop("checked")&&(c+=<? echo floatval($liensearch); ?>); 0<d&&d<=b?(a+=<? echo floatval($secondarypolicy); ?>,$("#secondpolicy").show()):$("#secondpolicy").hide();$("#endor81").prop("checked")?(c+=<? echo floatval($endorse81); ?>,$("#81").show()):$("#81").hide();$("#endorFL9").prop("checked")?(b=.1*a,c+=b,$("#fl9").closest("tr").show(),$("#fl9").text(commatize(b.toFixed(2)))):$("#fl9").closest("tr").hide();$("#endorPUD").prop("checked")?(c+=<? echo floatval($endorsePUD); ?>,$("#pud").show()):$("#pud").hide();$("#endorCONDO").prop("checked")?(c+=<? echo floatval($endorseCONDO); ?>, $("#condo").show()):$("#condo").hide();c+=a;$("#total").text(commatize(c.toFixed(2)))} $(function(){$("form").validate({rules:{title1:"required",title2:"required",buyer:"required",seller:"required",address:"required",salesprice:"required",mortgageamt:"required",lender:"required",realtor1:"required",realtor2:"required",contract:"required"},errorPlacement:function(c,a){},invalidHandler:function(c,a){alert("All fields are required")}});$("#title1, #title2, #salesprice, #mortgageamt").numeric();$("#lien, #endorFL9, #endor81, #endorPUD, #endorCONDO").on("change",calc);$("#title1, #title2").on("keyup", calc);calc()});</script>
</html>