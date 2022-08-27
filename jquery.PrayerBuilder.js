/**
 * @author Mohammed Meah 
 * @email mmeah2 (@at@) gmail (.dot.) com
 * @version 0.9.5
 * Updated Nov 01, 2015
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/mit-license.php
 *
 */
	
(function($) {

    var _options = {};
	var _container = {};
	var _prayerTimes = {};

	$.fn.PrayerBuilder = function(options) {
		var id = $(this).get(0).id;
		_options[id] = $.extend({}, $.fn.PrayerBuilder.defaults, options);

		this.each(function()
		{
			_container[id] = $(this);
			_prayerTimes = $.fn.PrayerBuilder.buildPrayer(id);
		});
		
		return _prayerTimes;
		
		
	}
	
	$.fn.PrayerBuilder.buildPrayer = function(id){
		$.fn.PrayerBuilder.obj = $("#"+id);

		//if(typeof $.fn.PrayerBuilder.data_feed_entry == "undefined") $.fn.PrayerBuilder.data_feed_entry=[];
		
		$.ajaxSetup({cache: false});
		
		try{
		//$(function(){
		  var json_uri = "//spreadsheets.google.com/feeds/list/0ApHK2KAGec6cdHEwRW5XSHVVWXQ1eTYxa3VjT2swbGc/od7/public/values?alt=json";
		  $.ajax({
			type: "get",
			async: false,
			cache: false,
			url: _options[id].url + '&callback=?',
			//contentType: "application/json; charset=utf-8",
			dataType: "jsonp",
			data: "",
			jsonpCallback: "callback_buildPrayer",
			success: function(data){
				
			},
			error:function(data){
				var redoMessage = "<br>"+"Don't see the prayer time? <a href=\"prayer_error.html\" target=\"_blank\">Click here</a> or <a onclick='location.reload()' href='#'>Reload Page</a>";
				var buildHtml_0 = $.fn.PrayerBuilder.iframe0 + redoMessage;
				var buildHtml_1 = $.fn.PrayerBuilder.iframe1 + redoMessage;				
				$.fn.PrayerBuilder.obj.html(buildHtml_0);
				PrayerBuilderRefresh_html=buildHtml_1;
				setTimeout(function(){
					$.fn.PrayerBuilder.obj.html(buildHtml_1);
				},1000);
			}
		  });
		  //console.log(data_feed_entry);
		//});
		}catch(e){if(console && console.log) console.log(e);}

		//setTimeout(function(){buildPrayer(id)}, 1000);

		return $.fn.PrayerBuilder.data_feed_entry;
	}
	
	$.fn.PrayerBuilder.getTimeDD = function(theTime){
		try{
			if(theTime.trim() === "") return "-";
			theTime = theTime.split(":");
			var ampm = "";
			var h = theTime[0];
			var m = theTime[1];
			if (h>=12)
			{
				if (h>12) h = h-12;
				ampm = " PM";
			}
			else
			{
				if (h==0) h = 12;
				ampm = " AM";
			}
			if(h<10) h = "0" + h;
			var finalTime = h +":"+ m;
			if(finalTime.indexOf("M")>1)return finalTime;
			return finalTime + ampm;
		}catch(e){
			return "-";
		}
	}	
	
	$.fn.PrayerBuilder.data_feed_entry=[];
	$.fn.PrayerBuilder.iframe0 = "<iframe frameborder=\"0\" width=\"100%\" height=\"170px\" scrolling=\"no\" src=\"https://accounts.google.com/Logout\"></iframe>";
	
	$.fn.PrayerBuilder.iframe1 = "<iframe frameborder=\"0\" width=\"100%\" height=\"170px\" scrolling=\"no\" src=\"//docs.google.com/spreadsheet/pub?key=0ApHK2KAGec6cdHEwRW5XSHVVWXQ1eTYxa3VjT2swbGc&single=true&gid=1&range=A1%3AC8&output=html&widget=false\"></iframe>";
	$.fn.PrayerBuilder.iframe2 = "<iframe frameborder=\"0\" width=\"100%\" height=\"170px\" scrolling=\"no\" src=\"//docs.google.com/spreadsheet/pub?key=0ApHK2KAGec6cdHEwRW5XSHVVWXQ1eTYxa3VjT2swbGc&single=true&gid=1&range=A9%3AC17&output=html&widget=false\"></iframe>";
	
	//iframe = "<iframe frameborder=\"0\" width=\"100%\" height=\"100%\" scrolling=\"no\" src=\"prayer_error.html\"></iframe>";	
	
	$.fn.PrayerBuilder.buildStyle="<style>.tblGenFixed td {padding:0 3px;overflow:hidden;white-space:normal;letter-spacing:0;word-spacing:0;background-color:#fff;z-index:1;border-top:0px none;border-left:0px none;border-bottom:1px solid rgb(255,255,255);border-bottom:1px solid rgba(204, 204, 204, 0);border-right:1px solid rgb(255,255,255);border-right:1px solid rgba(204, 204, 204, 0);} .dn {display:none} .tblGenFixed td.s0 {background-color:white;font-family:arial,sans,sans-serif;font-size:120.0%;font-weight:bold;font-style:normal;color:#000000;text-decoration:none;text-align:left;vertical-align:bottom;direction:auto-ltr;white-space:normal;overflow:hidden;border-top:1px solid #ffffff;border-right:1px solid #ffffff;border-bottom:1px solid #b7b7b7;border-left:1px solid #ffffff;} .tblGenFixed td.s2 {background-color:white;font-family:arial,sans,sans-serif;font-size:120.0%;font-weight:bold;font-style:normal;color:#000000;text-decoration:none;text-align:left;vertical-align:bottom;direction:auto-ltr;white-space:normal;overflow:hidden;border-top:1px solid #ffffff;border-right:;border-bottom:1px solid #b7b7b7;} .tblGenFixed td.s1 {background-color:white;font-family:arial,sans,sans-serif;font-size:120.0%;font-weight:bold;font-style:normal;color:#000000;text-decoration:none;text-align:left;vertical-align:bottom;direction:auto-ltr;white-space:normal;overflow:hidden;border-top:1px solid #ffffff;border-right:1px solid #ffffff;border-bottom:1px solid #b7b7b7;} .tblGenFixed td.s7 {background-color:white;font-family:arial,sans,sans-serif;font-size:120.0%;font-weight:normal;font-style:normal;color:#000000;text-decoration:none;text-align:left;vertical-align:bottom;direction:Context;white-space:normal;overflow:hidden;border-right:1px solid #ffffff;border-bottom:1px solid #b7b7b7;} .tblGenFixed td.s5 {background-color:;font-family:arial,sans,sans-serif;font-size:120.0%;font-weight:normal;font-style:normal;color:#000000;text-decoration:none;text-align:left;vertical-align:middle;direction:auto-ltr;white-space:normal;overflow:hidden;border-right:1px solid #ffffff;border-bottom:1px solid #b7b7b7;border-left:1px solid #ffffff;} .tblGenFixed td.s6 {background-color:;font-family:arial,sans,sans-serif;font-size:120.0%;font-weight:normal;font-style:normal;color:#000000;text-decoration:none;text-align:left;vertical-align:middle;direction:Context;white-space:normal;overflow:hidden;border-right:1px solid #ffffff;border-bottom:1px solid #b7b7b7;} .tblGenFixed td.s3 {background-color:#f3f3f3;font-family:arial,sans,sans-serif;font-size:120.0%;font-weight:normal;font-style:normal;color:#000000;text-decoration:none;text-align:left;vertical-align:middle;direction:auto-ltr;white-space:normal;overflow:hidden;border-right:1px solid #ffffff;border-bottom:1px solid #b7b7b7;border-left:1px solid #ffffff;} .tblGenFixed td.s4 {background-color:#f3f3f3;font-family:arial,sans,sans-serif;font-size:120.0%;font-weight:normal;font-style:normal;color:#000000;text-decoration:none;text-align:left;vertical-align:middle;direction:Context;white-space:normal;overflow:hidden;border-right:1px solid #ffffff;border-bottom:1px solid #b7b7b7;}"
	+".tblGenFixed,.tblGeneric{font-size:13px}"
	+"</style>";

	$.fn.PrayerBuilder.buildTable1="";

	$.fn.PrayerBuilder.buildTable1+="<div><span style='font-weight: bold; font-size: 18px;'>{hijri}</span>"
	+" <span style='font-size: 10px;'>according to <a href='http://chicagohilal.org/' target='_blank'>Chicago Hilal</a>."
	+" Updated {updated_today}</span></div>";
	
	$.fn.PrayerBuilder.buildTable1+="<table dir='ltr' border=0 cellpadding=0 cellspacing=0 class='tblGenFixed' id='tblMain' style='margin-top: 5px;'>"
	+"<tr class='rShim'><td class='rShim' style='width:88px;'><td class='rShim' style='width:112px;'><td class='rShim' style='width:115px;'>"
	+"<tr dir='ltr'><td  dir='ltr' class='s0'>Salah<td  dir='ltr' class='s1'>Start Time<td  dir='ltr' class='s2'>Iqamah</tr>"
	+"<tr dir='ltr'><td  dir='ltr' class='s5'>Fajr		<td  class='s6'>{t0}<td  class='s6'>{i0}</tr>"
	+"<tr dir='ltr'><td  dir='ltr' class='s3'>Sunrise	<td  class='s4'>{t1}<td  class='s4'>{i1}</tr>"			
	+"<tr dir='ltr'><td  dir='ltr' class='s5'>Zuhr		<td  class='s6'>{t2}<td  class='s6'>{i2}</tr>"
	+"<tr dir='ltr'><td  dir='ltr' class='s3'>Asr		<td  class='s4'>{t3}<td  class='s4'>{i3}</tr>"
	+"<tr dir='ltr'><td  dir='ltr' class='s5'>Maghrib	<td  class='s7'>{t4}<td  class='s7'>{i4}</tr>"
	+"<tr dir='ltr'><td  dir='ltr' class='s3'>Isha		<td  class='s4'>{t5}<td  class='s4'>{i5}</tr>"
	+"<tr dir='ltr'><td  dir='ltr' class='s5'>Next Fajr	<td  class='s7'>{t6}<td  class='s7'>{i6}</tr>"
	+"</table>";
	
	//default values
	jQuery.fn.PrayerBuilder.defaults = {
		url: '//spreadsheets.google.com/feeds/list/0ApHK2KAGec6cdHEwRW5XSHVVWXQ1eTYxa3VjT2swbGc/od7/public/values?alt=json'
	};

})(jQuery);

function callback_buildPrayer(data){
	var $=jQuery;
	var buildHtml = "<div id='content'>"+$.fn.PrayerBuilder.buildStyle+$.fn.PrayerBuilder.buildTable1+"</div>";
	if(data && typeof data.feed != "undefined" && typeof data.feed.entry != "undefined" && data.feed.entry.length>0){
		//data_feed_entry=$(data.feed.entry);
		//console.log(data_feed_entry);
		$(data.feed.entry).each(function(index){
			var entry = data.feed.entry[index];
			if(index<7){
				$.fn.PrayerBuilder.data_feed_entry[index]=[entry.gsx$starttime.$t,entry.gsx$iqamah.$t,"",""];
				buildHtml = buildHtml.replace("{t"+index+"}", $.fn.PrayerBuilder.getTimeDD(entry.gsx$starttime.$t));
				buildHtml = buildHtml.replace("{i"+index+"}", $.fn.PrayerBuilder.getTimeDD(entry.gsx$iqamah.$t));
			}
			if(index==7){
				buildHtml = buildHtml.replace("{hijri}", entry.gsx$salah.$t);
				window.time_of_day = entry.gsx$starttime.$t;
				buildHtml = buildHtml.replace("{updated_today}", entry.gsx$iqamah.$t);
			}
			if(index>8){
				var index2=index-9;
				if(index2<7){
					try{
						$.fn.PrayerBuilder.data_feed_entry[index2][2]=entry.gsx$iqamah.$t;							
						$.fn.PrayerBuilder.data_feed_entry[index2][3]=entry.gsx$salah.$t+"-"+$.fn.PrayerBuilder.getTimeDD(entry.gsx$iqamah.$t);
					}catch(e){
						if(console && console.log) console.log(e);
						// TODO - Find out why this fails???
					}
				}
			}
		});
		window.weather_codes = "";
		try{
			window.weather_codes = data.feed.entry[18].gsx$salah.$t;
		}catch(e){}
		$.fn.PrayerBuilder.obj.html(buildHtml);
	}
}