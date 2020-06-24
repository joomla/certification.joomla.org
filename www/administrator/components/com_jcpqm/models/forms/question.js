/**
 * @package    Joomla Certification Program, question manager
 *
 * @author     marco dings <http://certification.joomla.org>
 * @copyright  Copyright (C) 2015. All Rights Reserved
 * @license    GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html
 */

// Some Global Values
jform_vvvvvvwvvv_required = false;
jform_vvvvvvwvvw_required = false;
jform_vvvvvvwvvx_required = false;
jform_vvvvvvwvvy_required = false;
jform_vvvvvvwvvz_required = false;
jform_vvvvvvwvwa_required = false;
jform_vvvvvvwvwb_required = false;
jform_vvvvvvwvwc_required = false;
jform_vvvvvvyvwd_required = false;
jform_vvvvvvyvwe_required = false;
jform_vvvvvvyvwf_required = false;
jform_vvvvvvyvwg_required = false;
jform_vvvvvvyvwh_required = false;
jform_vvvvvvyvwi_required = false;
jform_vvvvvvzvwj_required = false;
jform_vvvvvvzvwk_required = false;
jform_vvvvvwavwl_required = false;
jform_vvvvvwavwm_required = false;

// Initial Script
jQuery(document).ready(function()
{
	var questiontype_vvvvvvv = jQuery("#jform_questiontype").val();
	vvvvvvv(questiontype_vvvvvvv);

	var questiontype_vvvvvvw = jQuery("#jform_questiontype").val();
	vvvvvvw(questiontype_vvvvvvw);

	var questiontype_vvvvvvx = jQuery("#jform_questiontype").val();
	vvvvvvx(questiontype_vvvvvvx);

	var questiontype_vvvvvvy = jQuery("#jform_questiontype").val();
	vvvvvvy(questiontype_vvvvvvy);

	var questiontype_vvvvvvz = jQuery("#jform_questiontype").val();
	vvvvvvz(questiontype_vvvvvvz);

	var questiontype_vvvvvwa = jQuery("#jform_questiontype").val();
	vvvvvwa(questiontype_vvvvvwa);
});

// the vvvvvvv function
function vvvvvvv(questiontype_vvvvvvv)
{
	if (isSet(questiontype_vvvvvvv) && questiontype_vvvvvvv.constructor !== Array)
	{
		var temp_vvvvvvv = questiontype_vvvvvvv;
		var questiontype_vvvvvvv = [];
		questiontype_vvvvvvv.push(temp_vvvvvvv);
	}
	else if (!isSet(questiontype_vvvvvvv))
	{
		var questiontype_vvvvvvv = [];
	}
	var questiontype = questiontype_vvvvvvv.some(questiontype_vvvvvvv_SomeFunc);


	// set this function logic
	if (questiontype)
	{
		jQuery('#jform_q_atf').closest('.control-group').hide();
		jQuery('.q_htf').closest('.control-group').hide();
	}
}

// the vvvvvvv Some function
function questiontype_vvvvvvv_SomeFunc(questiontype_vvvvvvv)
{
	// set the function logic
	if (questiontype_vvvvvvv != 1)
	{
		return true;
	}
	return false;
}

// the vvvvvvw function
function vvvvvvw(questiontype_vvvvvvw)
{
	if (isSet(questiontype_vvvvvvw) && questiontype_vvvvvvw.constructor !== Array)
	{
		var temp_vvvvvvw = questiontype_vvvvvvw;
		var questiontype_vvvvvvw = [];
		questiontype_vvvvvvw.push(temp_vvvvvvw);
	}
	else if (!isSet(questiontype_vvvvvvw))
	{
		var questiontype_vvvvvvw = [];
	}
	var questiontype = questiontype_vvvvvvw.some(questiontype_vvvvvvw_SomeFunc);


	// set this function logic
	if (questiontype)
	{
		jQuery('#jform_q_a1-lbl').closest('.control-group').hide();
		// remove required attribute from q_a1 field
		updateFieldRequired('q_a1',1);
		jQuery('#jform_q_a1').removeAttr('required');
		jQuery('#jform_q_a1').removeAttr('aria-required');
		jQuery('#jform_q_a1').removeClass('required');
		jform_vvvvvvwvvv_required = true;

		jQuery('#jform_q_a1c').closest('.control-group').hide();
		jQuery('#jform_q_a2-lbl').closest('.control-group').hide();
		// remove required attribute from q_a2 field
		updateFieldRequired('q_a2',1);
		jQuery('#jform_q_a2').removeAttr('required');
		jQuery('#jform_q_a2').removeAttr('aria-required');
		jQuery('#jform_q_a2').removeClass('required');
		jform_vvvvvvwvvw_required = true;

		jQuery('#jform_q_a2c').closest('.control-group').hide();
		jQuery('#jform_q_a3-lbl').closest('.control-group').hide();
		// remove required attribute from q_a3 field
		updateFieldRequired('q_a3',1);
		jQuery('#jform_q_a3').removeAttr('required');
		jQuery('#jform_q_a3').removeAttr('aria-required');
		jQuery('#jform_q_a3').removeClass('required');
		jform_vvvvvvwvvx_required = true;

		jQuery('#jform_q_a3c').closest('.control-group').hide();
		jQuery('#jform_q_a4-lbl').closest('.control-group').hide();
		// remove required attribute from q_a4 field
		updateFieldRequired('q_a4',1);
		jQuery('#jform_q_a4').removeAttr('required');
		jQuery('#jform_q_a4').removeAttr('aria-required');
		jQuery('#jform_q_a4').removeClass('required');
		jform_vvvvvvwvvy_required = true;

		jQuery('#jform_q_a4c').closest('.control-group').hide();
		jQuery('.q_h1').closest('.control-group').hide();
		jQuery('.q_h2').closest('.control-group').hide();
		jQuery('.q_h3').closest('.control-group').hide();
		jQuery('.q_h4').closest('.control-group').hide();
		jQuery('#jform_q_m1').closest('.control-group').hide();
		// remove required attribute from q_m1 field
		updateFieldRequired('q_m1',1);
		jQuery('#jform_q_m1').removeAttr('required');
		jQuery('#jform_q_m1').removeAttr('aria-required');
		jQuery('#jform_q_m1').removeClass('required');
		jform_vvvvvvwvvz_required = true;

		jQuery('#jform_q_m2').closest('.control-group').hide();
		// remove required attribute from q_m2 field
		updateFieldRequired('q_m2',1);
		jQuery('#jform_q_m2').removeAttr('required');
		jQuery('#jform_q_m2').removeAttr('aria-required');
		jQuery('#jform_q_m2').removeClass('required');
		jform_vvvvvvwvwa_required = true;

		jQuery('#jform_q_m3').closest('.control-group').hide();
		// remove required attribute from q_m3 field
		updateFieldRequired('q_m3',1);
		jQuery('#jform_q_m3').removeAttr('required');
		jQuery('#jform_q_m3').removeAttr('aria-required');
		jQuery('#jform_q_m3').removeClass('required');
		jform_vvvvvvwvwb_required = true;

		jQuery('#jform_q_m4').closest('.control-group').hide();
		// remove required attribute from q_m4 field
		updateFieldRequired('q_m4',1);
		jQuery('#jform_q_m4').removeAttr('required');
		jQuery('#jform_q_m4').removeAttr('aria-required');
		jQuery('#jform_q_m4').removeClass('required');
		jform_vvvvvvwvwc_required = true;

	}
}

// the vvvvvvw Some function
function questiontype_vvvvvvw_SomeFunc(questiontype_vvvvvvw)
{
	// set the function logic
	if (questiontype_vvvvvvw == 1)
	{
		return true;
	}
	return false;
}

// the vvvvvvx function
function vvvvvvx(questiontype_vvvvvvx)
{
	if (isSet(questiontype_vvvvvvx) && questiontype_vvvvvvx.constructor !== Array)
	{
		var temp_vvvvvvx = questiontype_vvvvvvx;
		var questiontype_vvvvvvx = [];
		questiontype_vvvvvvx.push(temp_vvvvvvx);
	}
	else if (!isSet(questiontype_vvvvvvx))
	{
		var questiontype_vvvvvvx = [];
	}
	var questiontype = questiontype_vvvvvvx.some(questiontype_vvvvvvx_SomeFunc);


	// set this function logic
	if (questiontype)
	{
		jQuery('#jform_q_atf').closest('.control-group').show();
		jQuery('.q_htf').closest('.control-group').show();
	}
}

// the vvvvvvx Some function
function questiontype_vvvvvvx_SomeFunc(questiontype_vvvvvvx)
{
	// set the function logic
	if (questiontype_vvvvvvx == 1)
	{
		return true;
	}
	return false;
}

// the vvvvvvy function
function vvvvvvy(questiontype_vvvvvvy)
{
	if (isSet(questiontype_vvvvvvy) && questiontype_vvvvvvy.constructor !== Array)
	{
		var temp_vvvvvvy = questiontype_vvvvvvy;
		var questiontype_vvvvvvy = [];
		questiontype_vvvvvvy.push(temp_vvvvvvy);
	}
	else if (!isSet(questiontype_vvvvvvy))
	{
		var questiontype_vvvvvvy = [];
	}
	var questiontype = questiontype_vvvvvvy.some(questiontype_vvvvvvy_SomeFunc);


	// set this function logic
	if (questiontype)
	{
		jQuery('#jform_q_a1-lbl').closest('.control-group').show();
		// add required attribute to q_a1 field
		updateFieldRequired('q_a1',0);
		jQuery('#jform_q_a1').prop('required','required');
		jQuery('#jform_q_a1').attr('aria-required',true);
		jQuery('#jform_q_a1').addClass('required');
		jform_vvvvvvyvwd_required = false;

		jQuery('#jform_q_a1c').closest('.control-group').show();
		jQuery('#jform_q_a2-lbl').closest('.control-group').show();
		// add required attribute to q_a2 field
		updateFieldRequired('q_a2',0);
		jQuery('#jform_q_a2').prop('required','required');
		jQuery('#jform_q_a2').attr('aria-required',true);
		jQuery('#jform_q_a2').addClass('required');
		jform_vvvvvvyvwe_required = false;

		jQuery('#jform_q_a2c').closest('.control-group').show();
		jQuery('#jform_q_a3-lbl').closest('.control-group').show();
		// add required attribute to q_a3 field
		updateFieldRequired('q_a3',0);
		jQuery('#jform_q_a3').prop('required','required');
		jQuery('#jform_q_a3').attr('aria-required',true);
		jQuery('#jform_q_a3').addClass('required');
		jform_vvvvvvyvwf_required = false;

		jQuery('.q_h1').closest('.control-group').show();
		jQuery('.q_h2').closest('.control-group').show();
		jQuery('.q_h3').closest('.control-group').show();
		jQuery('#jform_q_m1').closest('.control-group').show();
		// add required attribute to q_m1 field
		updateFieldRequired('q_m1',0);
		jQuery('#jform_q_m1').prop('required','required');
		jQuery('#jform_q_m1').attr('aria-required',true);
		jQuery('#jform_q_m1').addClass('required');
		jform_vvvvvvyvwg_required = false;

		jQuery('#jform_q_m2').closest('.control-group').show();
		// add required attribute to q_m2 field
		updateFieldRequired('q_m2',0);
		jQuery('#jform_q_m2').prop('required','required');
		jQuery('#jform_q_m2').attr('aria-required',true);
		jQuery('#jform_q_m2').addClass('required');
		jform_vvvvvvyvwh_required = false;

		jQuery('#jform_q_m3').closest('.control-group').show();
		// add required attribute to q_m3 field
		updateFieldRequired('q_m3',0);
		jQuery('#jform_q_m3').prop('required','required');
		jQuery('#jform_q_m3').attr('aria-required',true);
		jQuery('#jform_q_m3').addClass('required');
		jform_vvvvvvyvwi_required = false;

	}
}

// the vvvvvvy Some function
function questiontype_vvvvvvy_SomeFunc(questiontype_vvvvvvy)
{
	// set the function logic
	if (questiontype_vvvvvvy != 1)
	{
		return true;
	}
	return false;
}

// the vvvvvvz function
function vvvvvvz(questiontype_vvvvvvz)
{
	if (isSet(questiontype_vvvvvvz) && questiontype_vvvvvvz.constructor !== Array)
	{
		var temp_vvvvvvz = questiontype_vvvvvvz;
		var questiontype_vvvvvvz = [];
		questiontype_vvvvvvz.push(temp_vvvvvvz);
	}
	else if (!isSet(questiontype_vvvvvvz))
	{
		var questiontype_vvvvvvz = [];
	}
	var questiontype = questiontype_vvvvvvz.some(questiontype_vvvvvvz_SomeFunc);


	// set this function logic
	if (questiontype)
	{
		jQuery('#jform_q_a4-lbl').closest('.control-group').hide();
		// remove required attribute from q_a4 field
		updateFieldRequired('q_a4',1);
		jQuery('#jform_q_a4').removeAttr('required');
		jQuery('#jform_q_a4').removeAttr('aria-required');
		jQuery('#jform_q_a4').removeClass('required');
		jform_vvvvvvzvwj_required = true;

		jQuery('#jform_q_a4c').closest('.control-group').hide();
		jQuery('.q_h4').closest('.control-group').hide();
		jQuery('#jform_q_m4').closest('.control-group').hide();
		// remove required attribute from q_m4 field
		updateFieldRequired('q_m4',1);
		jQuery('#jform_q_m4').removeAttr('required');
		jQuery('#jform_q_m4').removeAttr('aria-required');
		jQuery('#jform_q_m4').removeClass('required');
		jform_vvvvvvzvwk_required = true;

	}
}

// the vvvvvvz Some function
function questiontype_vvvvvvz_SomeFunc(questiontype_vvvvvvz)
{
	// set the function logic
	if (questiontype_vvvvvvz == 2 || questiontype_vvvvvvz == 3)
	{
		return true;
	}
	return false;
}

// the vvvvvwa function
function vvvvvwa(questiontype_vvvvvwa)
{
	if (isSet(questiontype_vvvvvwa) && questiontype_vvvvvwa.constructor !== Array)
	{
		var temp_vvvvvwa = questiontype_vvvvvwa;
		var questiontype_vvvvvwa = [];
		questiontype_vvvvvwa.push(temp_vvvvvwa);
	}
	else if (!isSet(questiontype_vvvvvwa))
	{
		var questiontype_vvvvvwa = [];
	}
	var questiontype = questiontype_vvvvvwa.some(questiontype_vvvvvwa_SomeFunc);


	// set this function logic
	if (questiontype)
	{
		jQuery('#jform_q_a4-lbl').closest('.control-group').show();
		// add required attribute to q_a4 field
		updateFieldRequired('q_a4',0);
		jQuery('#jform_q_a4').prop('required','required');
		jQuery('#jform_q_a4').attr('aria-required',true);
		jQuery('#jform_q_a4').addClass('required');
		jform_vvvvvwavwl_required = false;

		jQuery('#jform_q_a4c').closest('.control-group').show();
		jQuery('.q_h4').closest('.control-group').show();
		jQuery('#jform_q_m4').closest('.control-group').show();
		// add required attribute to q_m4 field
		updateFieldRequired('q_m4',0);
		jQuery('#jform_q_m4').prop('required','required');
		jQuery('#jform_q_m4').attr('aria-required',true);
		jQuery('#jform_q_m4').addClass('required');
		jform_vvvvvwavwm_required = false;

	}
}

// the vvvvvwa Some function
function questiontype_vvvvvwa_SomeFunc(questiontype_vvvvvwa)
{
	// set the function logic
	if (questiontype_vvvvvwa == 4 || questiontype_vvvvvwa == 5)
	{
		return true;
	}
	return false;
}

// update required fields
function updateFieldRequired(name,status)
{
	var not_required = jQuery('#jform_not_required').val();

	if(status == 1)
	{
		if (isSet(not_required) && not_required != 0)
		{
			not_required = not_required+','+name;
		}
		else
		{
			not_required = ','+name;
		}
	}
	else
	{
		if (isSet(not_required) && not_required != 0)
		{
			not_required = not_required.replace(','+name,'');
		}
	}

	jQuery('#jform_not_required').val(not_required);
}

// the isSet function
function isSet(val)
{
	if ((val != undefined) && (val != null) && 0 !== val.length){
		return true;
	}
	return false;
}


/* BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN
*
*  TODO MDI Javascript edit Question :: BEGIN
*
*/

function onchangeExam1(el)
{
	// var e = document.getElementById("jform_exam");
	// var value = e.options[e.selectedIndex].value;
	// var text = e.options[e.selectedIndex].text;
	alert('onchangeExam::' + el.value + " : " + el.options[el.selectedIndex].text);
}

function onchangeExam2(el) {
	var component 			= 'com_jcpqm'
	var controller  		= 'ajax'
	var controlerTask 		= 'setQuestionOptions'
	var task 				= controller + '.' + controlerTask
	var format 				= 'json'
	var getUrl = "/index.php"
			     '?' + 'option' + '=' +	component +
		         '&' + 'task'   + '=' +	task      +
		         '&' + 'format' + '=' +	format;

	// token is set/defined in \JcpqmViewQuestion::setDocument by JCB
	var getParam=[], getValue=[],getParameters=[];
	getParam[1]   = "exacmCatId"
	getValue[1]   = el.value
	getParam[2]   = "view"
	getValue[2]   = "question"
	for (i = 1; i < getParam.length; i++) {
		getParameters.push(getParam[i]+"="+getValue[i]);
	}

	if (el.value > 0) {
		alert("\n" + token + "\n\n"   + getUrl + "\n\n" + getParameters.join('&'));
	}
	var request = 	'token=' + token +
					getParameters.join('&');
}

// getUrl = "/index.php?option=com_jcpqm&task=ajax.setQuestionOptions&format=json";


function onchangeExam(el){
	// javascript:(/** @version 0.5.2 */function() {document.cookie='XDEBUG_SESSION='+'PHPSTORM'+';path=/;';})()

	var component 			= 'com_jcpqm'
	var controller  		= 'ajax'
	var controlerTask 		= 'setQuestionOptions'
	var task 				= controller + '.' + controlerTask
	var format 				= "json"
	var getUrl 				= window.location.pathname;
	var options = '?' + 'option' + '=' +	component +
		'&' + 'task'   + '=' +	task    +
		'&' + 'token'  + '=' +	token   +
		'&' + 'format' + '=' +	format;
	var getParams = {}

	getParams['raw'] 			= true;
	getParams['examCatId'] 		= el.value;
	getParams['view'] 			= "exam"
	getParams['XDEBUG_SESSION'] = "PHPSTORM"
	var getParameters=''
	for (var getParamKey in getParams)
	{
		getParameters = getParameters + '&' + getParamKey + "=" + getParams[getParamKey];
	}
	// alert(  getUrl + options + getParameters )
	// console.log(  getUrl + options + getParameters )
	if (el.value > 0) {
		// https://developer.mozilla.org/en-US/docs/Web/Guide/AJAX/Getting_Started
		var httpRequest = new XMLHttpRequest();
		httpRequest.open('GET', getUrl + options + getParameters, true);
		httpRequest.setRequestHeader("Cache-Control", "no-cache")
		httpRequest.send();

		httpRequest.onreadystatechange = function () {
			if (httpRequest.readyState === XMLHttpRequest.DONE) {
				if (httpRequest.status === 200) {
					// Success!`
					var rawData = httpRequest.responseText;
					var data = JSON.parse(httpRequest.responseText);
					var id = data[0];
					var val = data[1];

					// select .control-wrapper-catidplus\d+, we can not do that directly :(
					var elements = document.getElementsByClassName('catidplus' );
                    Array.prototype.forEach.call(elements, function (el, i) {

						var isActiveCatId = el.classList.contains('id' + id)
						// console.log("=============== " + id + ' ' + 'id:'+ el.id + ' value:'+ el.value);
						Array.from(el.options).some(function (opt, index, _arr)
						{
							if (opt.getAttribute('disabled') != 'disabled')
							{
								el.options[index].setAttribute('selected', 'selected')
								return true
							}
						})

						el.removeAttribute("required")
						el.removeAttribute("aria-required")
						controlWrapperStyle = 'none'
						if ( isActiveCatId )
						{
							controlWrapperStyle = 'block'
							el.setAttribute("required", "required")
							el.setAttribute("aria-required", "required")
							el.value = val
							// console.log(id + "=>" + val)
						}
						// console.log("\n")
						controlWrapper = el.parentNode.parentNode;
						controlWrapper.style.display = controlWrapperStyle;
					});
                } else {
					// We reached our target server, but it returned an error
                    alert("error " + httpRequest.status);
                }
            }
        };
	}
}

/* 
*  TODO MDI Javascript edit Question :: END
*   
END END END END END END END END END END END END END END END END END END END END END END END END */ 
