<?php
$months = __('months', true);
ksort($months);

$theme = isset($_GET['theme']) ? $_GET['theme'] : $tpl['option_arr']['o_theme']; 
?>
<div id="pjWrapperCarRental_<?php echo $theme;?>">
	<div id="crContainer" class="crContainer"></div>
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<?php echo stripslashes(nl2br($tpl['term_arr'][0]['content']));?>
				</div><!-- /.modal-body -->

				<div class="modal-footer">
					<button type="button" class="btn btn-default pjCrBtntDefault" data-dismiss="modal"><?php __('front_1_close');?></button>
				</div><!-- /.modal-footer -->
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal fade -->
	<div class="modal fade" id="pjCrMapModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<div id="pjCrMapCanvas" style="height: 450px;"></div>
				</div><!-- /.modal-body -->

				<div class="modal-footer">
					<button type="button" class="btn btn-default pjCrBtntDefault" data-dismiss="modal"><?php __('front_1_close');?></button>
				</div><!-- /.modal-footer -->
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal fade -->
</div>

<script type="text/javascript">

var pjQ = pjQ || {},
	myCR;
(function () {
	"use strict";
	var isSafari = /Safari/.test(navigator.userAgent) && /Apple Computer/.test(navigator.vendor),
	
	loadCssHack = function(url, callback){
		var link = document.createElement('link');
		link.type = 'text/css';
		link.rel = 'stylesheet';
		link.href = url;
	
		document.getElementsByTagName('head')[0].appendChild(link);
	
		var img = document.createElement('img');
		img.onerror = function(){
			if (callback && typeof callback === "function") {
				callback();
			}
		};
		img.src = url;
	},
	loadRemote = function(url, type, callback) {
		if (type === "css" && isSafari) {
			loadCssHack(url, callback);
			return;
		}
		var _element, _type, _attr, scr, s, element;
		
		switch (type) {
		case 'css':
			_element = "link";
			_type = "text/css";
			_attr = "href";
			break;
		case 'js':
			_element = "script";
			_type = "text/javascript";
			_attr = "src";
			break;
		}
		
		scr = document.getElementsByTagName(_element);
		s = scr[scr.length - 1];
		element = document.createElement(_element);
		element.type = _type;
		if (type == "css") {
			element.rel = "stylesheet";
		}
		if (element.readyState) {
			element.onreadystatechange = function () {
				if (element.readyState == "loaded" || element.readyState == "complete") {
					element.onreadystatechange = null;
					if (callback && typeof callback === "function") {
						callback();
					}
				}
			};
		} else {
			element.onload = function () {
				if (callback && typeof callback === "function") {
					callback();
				}
			};
		}
		element[_attr] = url;
		s.parentNode.insertBefore(element, s.nextSibling);
	},
	loadScript = function (url, callback) {
		loadRemote(url, "js", callback);
	},
	loadCss = function (url, callback) {
		loadRemote(url, "css", callback);
	},	
	getSessionId = function () {
		return sessionStorage.getItem("session_id") == null ? "" : sessionStorage.getItem("session_id");
	},
	createSessionId = function () {
		if(getSessionId()=="") {
			sessionStorage.setItem("session_id", "<?php echo session_id(); ?>");
		}
	},
	options = {
		folder: "<?php echo PJ_INSTALL_URL; ?>",
		validation: {
			error_dates: "<?php echo str_replace("{HOURS}", $tpl['option_arr']['o_min_hour'], __('front_1_v_err_dates', true, false)); ?>",
			error_title: "<?php  __('front_4_v_err_title'); ?>",
			error_email: "<?php  __('front_4_v_err_email'); ?>",
			error_length: "<?php echo str_replace("{DAYS}", $tpl['option_arr']['o_min_hour'], __('front_1_v_err_length', true, false)); ?>",
		},
		booking_periods: <?php echo pjAppController::jsonEncode($tpl['option_arr']['o_booking_periods']); ?>,
		min_hour: "<?php echo $tpl['option_arr']['o_booking_periods'] == 'perday' ? ($tpl['option_arr']['o_min_hour'] * 24) : $tpl['option_arr']['o_min_hour']; ?>",
		message_1: "<?php  __('front_msg_1'); ?>",
		message_2: "<?php  __('front_msg_2'); ?>",
		message_3: "<?php  __('front_msg_3'); ?>",
		message_4: "<?php  __('front_msg_4'); ?>",
		dateFormat: "<?php echo $tpl['option_arr']['o_date_format']; ?>",
		startDay: <?php echo $tpl['option_arr']['o_week_start']; ?>,
		dayNames: ["<?php echo join('","', __('day_names', true)); ?>"],
		monthNamesFull: ["<?php echo join('","', $months); ?>"],
		closeButton: "<?php  __('front_1_close'); ?>",
		pjLang: <?php echo isset($_GET['pjLang']) && (int) $_GET['pjLang'] > 0 ? $_GET['pjLang'] : 0; ?>,
		momentDateFormat: "<?php echo pjUtil::toMomemtJS($tpl['option_arr']['o_date_format']); ?>",
		time_format: "<?php echo $tpl['option_arr']['o_time_period'] == '12hours' ? 'LT' : "HH:mm";?>",
		google_api_key: "<?php echo @$tpl['option_arr']['o_google_map_api']; ?>"
	};
	<?php
	$dm = new pjDependencyManager(PJ_THIRD_PARTY_PATH);
	$dm->load(PJ_CONFIG_PATH . 'dependencies.php')->resolve();
	$google_api_key = isset($tpl['option_arr']['o_google_map_api']) ? (!empty($tpl['option_arr']['o_google_map_api']) ? 'key=' . $tpl['option_arr']['o_google_map_api'] : "") : "";
	?>
	loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('storage_polyfill'); ?>storagePolyfill.min.js", function () {
		if (isSafari) {
			createSessionId();
			options.session_id = getSessionId();
		}else{
			options.session_id = "";
		}
		loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_jquery'); ?>pjQuery.min.js", function () {
			loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_validate'); ?>pjQuery.validate.min.js", function () {
				loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_bootstrap'); ?>pjQuery.bootstrap.min.js", function () {
					loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_bootstrap_datetimepicker'); ?>moment-with-locales.min.js", function () {
						loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_bootstrap_datetimepicker'); ?>pjQuery.bootstrap-datetimepicker.min.js", function () {
							loadScript("<?php echo PJ_INSTALL_URL . PJ_JS_PATH ?>pjFront.js", function () {
								myCR = CR(options);
							});
						});
					});
				});
			});
		});
	});
})();
</script>