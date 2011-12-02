/*!
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */

$("#showToolbarLink").find("a").one("click",function(){$(".publish_field").resizable({handles:"e",minHeight:49,stop:function(){var a=Math.round($(this).outerWidth()/$(this).parent().width()*10)*10;a<10&&(a=10);a>99&&(a=100);$(this).css("width",a+"%")}});$("#tools ul li a.field_selector").draggable({revert:!0,zIndex:33,helper:"clone"}).click(function(){return!1});var a={};a[EE.lang.add_tab]=add_publish_tab;$("#new_tab_dialog").dialog({autoOpen:!1,open:function(){$("#tab_name").focus()},resizable:!1,
modal:!0,position:"center",minHeight:0,buttons:a});$(".add_tab_link").click(function(){$("#tab_name").val("");$("#add_tab label").text(EE.lang.tab_name+": ");$("#new_tab_dialog").dialog("open");setup_tabs();return!1})}).toggle(function(){disable_fields(!0);$(".tab_menu").sortable({axis:"x",tolerance:"pointer",placeholder:"publishTabSortPlaceholder",items:"li:not(.addTabButton)"});$(EE._hidden_fields).closest(".publish_field").show();$("a span","#showToolbarLink").text(EE.lang.hide_toolbar);$("#showToolbarLink").animate({marginRight:"210"});
$("#holder").animate({marginRight:"196"},function(){$("#tools").show();$("#showToolbarImg").hide();$("#hideToolbarImg").css("display","inline")});$(".publish_field").animate({backgroundPosition:"0 0"},"slow");$(".handle").css("display","block");$(".ui-resizable-e").show(500);$(".addTabButton").css("display","inline")},function(){disable_fields(!1);$("#tools").hide();$(".tab_menu").sortable("destroy");$("a span","#showToolbarLink").text(EE.lang.show_toolbar);$("#showToolbarLink").animate({marginRight:"20"});
$("#holder").animate({marginRight:"10"});$(".publish_field").animate({backgroundPosition:"-15px 0"},"slow");$(".handle").css("display","none");$(".ui-resizable-e").hide();$(".addTabButton").hide();$("#hideToolbarImg").hide();$("#showToolbarImg").css("display","inline");$(EE._hidden_fields).closest(".publish_field").hide()});$("#tab_menu_tabs").sortable({tolerance:"intersect",items:"li:not(.addTabButton)",axis:"x"});
$("#tools h3 a").toggle(function(){$(this).parent().next("div").slideUp();$(this).toggleClass("closed")},function(){$(this).parent().next("div").slideDown();$(this).toggleClass("closed")});$("#toggle_member_groups_all").toggle(function(){$("input.toggle_member_groups").each(function(){this.checked=!0})},function(){$("input.toggle_member_groups").each(function(){this.checked=!1})});
$(".delete_field").click(function(a){a.preventDefault();var a=$(this),b=a.attr("id").substr(13),b=$("#hold_field_"+b);a.children("img");a.attr("data-visible")=="y"?(b.is(":hidden")?b.css("display","none"):b.slideUp(),b.attr("data-width",EE.publish.get_percentage_width(b)),a.attr("data-visible","n").children().attr("src",EE.THEME_URL+"images/closed_eye.png")):(b.slideDown(),b.attr("data-width",!1),a.attr("data-visible","y").children().attr("src",EE.THEME_URL+"images/open_eye.png"))});
_delete_tab_hide=function(a,b){$(".menu_"+b).parent().fadeOut();$(a).fadeOut();$("#"+b).fadeOut();selected_tab=get_selected_tab();b==selected_tab&&(prev=$(".menu_"+selected_tab).parent().prevAll(":visible"),prev=prev.length>0?prev.attr("id").substr(5):"publish_tab",tab_focus(prev));return!1};get_selected_tab=function(){return jQuery("#tab_menu_tabs .current").attr("id").substring(5)};
_delete_tab_reveal=function(){tab_to_show=$(this).attr("href").substring(1);$(".menu_"+tab_to_show).parent().fadeIn();$(this).children().attr("src",EE.THEME_URL+"images/content_custom_tab_show.gif");$("#"+tab_to_delete).fadeIn();return!1};
tab_req_check=function(a){var b=!1,c=[],d=EE.publish.required_fields;$("#"+a).find(".publish_field").each(function(){var a=this.id.replace(/hold_field_/,""),e=0,f="";for(f in d)d[f]==a&&(b=!0,c[e]=a,e++)});return b===!0?($.ee_notice(EE.publish.lang.tab_has_req_field+c.join(","),{type:"error"}),!0):!1};
function delete_publish_tab(){$("#publish_tab_list").unbind("click.tab_delete");$("#publish_tab_list").bind("click.tab_delete",function(a){a.target!==this&&(a=$(a.target).closest("li"),the_id=a.attr("id").replace(/remove_tab_/,""),tab_req_check(the_id)||_delete_tab_hide(a,the_id));return!1})}delete_publish_tab();
add_publish_tab=function(){tab_name=$("#tab_name").val();/^[a-zA-Z0-9 _-]+$/.test(tab_name)?tab_name===""?$.ee_notice(EE.lang.tab_name_required):_add_tab(tab_name)?$("#new_tab_dialog").dialog("close"):$.ee_notice(EE.lang.duplicate_tab_name):$.ee_notice(EE.lang.illegal_characters)};
function _add_tab(a){tab_name_filtered=a.replace(/ /g,"_").toLowerCase();if($("#"+tab_name_filtered).length)return $("#"+tab_name_filtered).css("display")=="none"?($("#remove_tab_"+tab_name_filtered).fadeIn(),$("#menu_"+tab_name_filtered).fadeIn(),$("#tab_menu_tabs li").removeClass("current"),$("#menu_"+tab_name_filtered).addClass("current"),tab_focus(tab_name_filtered),!0):!1;$(".addTabButton").before('<li id="menu_'+tab_name_filtered+'" title="'+a+'" class="content_tab"><a href="#" class="menu_'+
tab_name_filtered+'" title="menu_'+tab_name_filtered+'">'+a+"</a></li>").fadeIn();$("#publish_tab_list").append('<li id="remove_tab_'+tab_name_filtered+'"><a class="menu_focus" title="menu_+tab_name_filtered+" href="#">'+a+'</a> <a href="#'+tab_name_filtered+'" class="delete delete_tab"><img src="'+EE.THEME_URL+'images/content_custom_tab_delete.png" alt="Delete" width="19" height="18" /></a></li>');new_tab=$('<div class="main_tab"><div class="insertpoint"></div><div class="clear"></div></div>').attr("id",
tab_name_filtered);new_tab.prependTo("#holder");$("#tab_menu_tabs li:visible").length<=2&&tab_focus(tab_name_filtered);$("#tab_menu_tabs li").removeClass("current");$("#menu_"+tab_name_filtered).addClass("current");setup_tabs();delete_publish_tab();return!0}$("#tab_name").keypress(function(a){if(a.keyCode=="13")return add_publish_tab(),!1});$(".tab_menu").sortable("destroy");
