function commatize(val){
	while (/(\d+)(\d{3})/.test(val.toString())){
	  val = val.toString().replace(/(\d+)(\d{3})/, '$1,$2');
	}
	return val;
}
function calc(){
	var total = "<? echo floatval($surcharge+$settlementfee+$titlesearch) ?>";
	var title1 = parseFloat($('#title1').val())
	var title2 = parseFloat($('#title2').val());
	title1 = (isNaN(title1)?0:title1);
	title2 = (isNaN(title2)?0:title2);
	var t1 = (title1>=title2?title1:title2);
	var t2 = (title1>=title2?title2:title1);
	//0-100000 @ 5.75
	var premium = 0;
	var v = ((t1/1000)*5.75);
	if(t1<100000) premium += (v<100?100:v);
	else if(t1>=100000) premium += 575;
	//100000-1000000 @ 5
	if(t1>100000 && t1<=1000000) premium += (((t1-100000)/1000)*5);
	else if(t1>1000000) premium += 4500;
	//1000000-5000000 @ 2.5
	if(t1>1000000 && t1<=5000000) premium += (((t1-1000000)/1000)*2.5);
	else if(t1>5000000) premium += 10000;
	//5000000-10000000 @ 2.25
	if(t1>5000000 && t1<=10000000) premium += (((t1-5000000)/1000)*2.25);
	else if(t1>10000000) premium += 11250;
	//>10000000 @ 2
	if(t1>10000000) premium += (((t1-10000000)/1000)*2);
	//lien
	if($('#lien').prop('checked')) total += "<? echo floatval($liensearch); ?>";
	//secondary policy
	if(t2>0 && t2<=t1){
		premium += "<? echo floatval($secondarypolicy); ?>";
		$('#secondpolicy').show();
	}else $('#secondpolicy').hide();
	//8.1
	if($('#endor81').prop('checked')){
		total += "<? echo floatval($endorse81); ?>";
		$('#81').show();
	}else $('#81').hide();
	//fl9
	if($('#endorFL9').prop('checked')){
		var fl9 = (premium*0.1);
		total += fl9;
		$('#fl9').closest('tr').show();
		$('#fl9').text(commatize(fl9.toFixed(2)));
	}else $('#fl9').closest('tr').hide();
	//pud
	if($('#endorPUD').prop('checked')){
		total += "<? echo floatval($endorsePUD); ?>";
		$('#pud').show();
	}else $('#pud').hide();
	//condo
	if($('#endorCONDO').prop('checked')){
		total += "<? echo floatval($endorseCONDO); ?>";
		$('#condo').show();
	}else $('#condo').hide();
	//
	total += premium;
	$('#total').text(commatize(total.toFixed(2)));
}
$(function(){
	$('form').validate({
		rules:{
			title1:'required',
			title2:'required',
			buyer:'required',
			seller:'required',
			address:'required',
			salesprice:'required',
			mortgageamt:'required',
			lender:'required',
			realtor1:'required',
			realtor2:'required',
			contract:'required'
		},
		errorPlacement:function(err, ele){
		},
		invalidHandler:function(e, v){
			alert('All fields are required');
		}
	});
	$('#title1, #title2, #salesprice, #mortgageamt').numeric();
	$('#lien, #endorFL9, #endor81, #endorPUD, #endorCONDO').on('change', calc);
	$('#title1, #title2').on('keyup', calc);
	calc();
});