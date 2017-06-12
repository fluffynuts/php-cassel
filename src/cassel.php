<?php
// vim: ft=php foldmarker=<<<,>>>
$js_cascade_fn_included = false;
class Cassel {
	var $cascades = array();
	var $id = "";
	var $name = "";
	var $style = "";
	var $sel_idx = "";
	var $sel_val = "";
	var $set_on_start = true;
	var $default_name = "__default__";

	function Cassel ($opts = array(/*<<<*/
								"cascades" 	=> array(),
								"name"		=> "",
								"id"		=> "",
								"style"		=> "",
								"sel_idx"	=> "",
								"sel_val"	=> "",
								"set_on_start" => true,
								"default_name" => "__default__",
								"defaults"	=> array(),
							)) {
		if (!is_array($opts)) $opts = array();
		$this->cascades = $this->arr_or_def($opts, "cascades", array());
		$this->id = $this->arr_or_def($opts, "id", $this->id);
		$this->name = $this->arr_or_def($opts, "name", $this->id);
		$this->style = $this->arr_or_def($opts, "style",
			$this->style);
		$this->sel_idx = $this->arr_or_def($opts, "sel_idx",
			$this->sel_idx);
		$this->sel_val = $this->arr_or_def($opts, "sel_val", 
			$this->sel_val);
		$this->set_on_start = $this->arr_or_def($opts, "set_on_start",
			$this->set_on_start);
		$this->default_name = $this->arr_or_def($opts, "default_name", 
			$this->default_name);
		$this->defaults = $this->arr_or_def($opts, "defaults", array());
	}
/*>>>*/
	function arr_or_def(&$arr, $idx, $def, $allow_blank = true) {/*<<<*/
		if ((array_key_exists($idx, $arr)) 
			&& ($allow_blank || ($arr[$idx] != ""))) {
			return $arr[$idx];
		} else {
			return $def;
		}
	}
/*>>>*/
	function render($print = true) {/*<<<*/
		// render the original options
		$r="<select id=\"".$this->id."\" onchange=\"cascade('".$this->id
			."', this.options[this.selectedIndex].text)\" name=\""
			.$this->name."\"";
		if (strlen($this->style)) {
			if (strpos(":", $this->style)) {
				$r.=" style=\"".$this->style."\"";
			} else {
				$r.=" class=\"".$this->style."\"";
			}
		}
		$r.=">";
		foreach ($this->cascades as $idx => $arr) {
			if ($idx == $this->default_name) continue; // special default list val
			if (is_array($arr)) {
				$tmp = array_keys($arr);
				$val = $tmp[0];
				$r.="\n<option value=\"".htmlentities($idx)."\"";
				if (($idx == $this->sel_idx) || ($val == $this->sel_val)) {
					$r.=" selected";
				}
				$r.=">".htmlentities($val)."</option>";
			} else {
				$r.="\n<option value=\"".$arr."\"";
				if (($idx == $this->sel_idx) || ($val == $this->sel_val)) {
					$r.=" selected";
				}
				$r.=">".htmlentities($arr)."</option>";
			}
		}
		$r.="\n</select>";
		// render the js code to manipulate children cascades
		$r.=$this->render_js();
		if ($print) {
			print($r);
		} else {
			return $r;
		}
	}
/*>>>*/
	function render_js() {/*<<<*/
		global $js_cascade_fn_included;
		// the arrays of cascade values
		$js = "casref[\"".$this->id."\"] = new Array();\n";
		foreach ($this->cascades as $parentval => $option) {
			if ($parentval == $this->default_name) {
				$ref = "casref[\"".$this->id."\"][\"".$this->default_name."\"]";
				$js .= $ref." = new Array();\n";
				if (is_array($option)) {
					foreach ($option as $childid => $childopts) {
						$cref = $ref."[\"".$childid."\"]";
						$js .= $cref." = new Array();\n";
						foreach ($childopts as $cidx => $cval) {
							$js .= $cref."[\"".$cidx."\"] = \"".
								htmlentities($cval)."\";\n";
						}
					}
				}
			} elseif (is_array($option)) {
				$tmp = array_keys($option);
				$opt = $tmp[0];
				$ref = "casref[\"".$this->id."\"][\"".$opt."\"]";
				$js .= $ref." = new Array();\n";
				if (is_array($option[$opt])) {
					foreach ($option[$opt] as $childid => $childopts) {
						$cref = $ref."[\"".$childid."\"]";
						$js.=$cref." = new Array();\n";
						foreach ($childopts as $cidx => $cval) {
							$js .= $cref."[\"".$cidx."\"] = \"".
								htmlentities($cval)."\";\n";
						}
					}
				}
			}
		}
		if (is_array($this->defaults)) {
			
			$js.="\ncassel_defaults['".$this->id."'] = new Array();\n";
			foreach ($this->defaults as $pval => $cvals) {
				if (!is_array($cvals)) {
					continue;
				}
				$js.="\ncassel_defaults['".$this->id."']['".$pval."'] = new Array();\n";
				foreach ($cvals as $childid => $childval) {
					$js.="cassel_defaults['".$this->id."']['".$pval
							."']['".$childid
							."'] = \"".$this->jsquote($childval)."\";\n";
				}
			}
		}
		// the cascade function
		if (!$js_cascade_fn_included) {
	$jsfn = "var DEFAULT_VAL = \"".$this->default_name."\";\n";
	$jsfn.=<<<JS_FUNC
	var casref = new Array();
	var cassel_defaults = new Array();
	function cascade(parentid, newval) {
		if (casref[parentid]) {
			if (!casref[parentid][newval]) {
				if (casref[parentid][DEFAULT_VAL]) {
					newval = DEFAULT_VAL;
				} else {
					return;
				}
			}
			for (childid in casref[parentid][newval]) {
				if (obj = document.getElementById(childid)) {
					options = casref[parentid][newval][childid];
					obj.length = 0;
					i = 0;
					for (idx in options) {
						obj.options[i++] = new Option(options[idx], idx);
					}
				}
				if (casref[childid]) {
					if (obj = document.getElementById(childid)) {
						cascade(childid, obj.options[obj.selectedIndex].text);
					}
				}
			}
			for (var pid in cassel_defaults) {
				if (pid == parentid) {
					// get the value of the newval in the parent
					pobj = document.getElementById(pid);
					newidx = pobj.options[pobj.selectedIndex].value;
					for (var cid in cassel_defaults[pid][newidx]) {
						if (obj = document.getElementById(cid)) {
							if (typeof(obj.options) != "undefined") {
								for (idx = 0; idx < obj.options.length; idx++) {
									if ((obj.options[idx].value == cassel_defaults[pid][newidx][cid]) || (obj.options[idx].text == cassel_defaults[pid][newidx][cid])) {
										obj.options[idx].selected = true;
										break;
									}
								}
							}
						}
					}
					break;
				}
			}
		}
	}
	function addLoadEvent(func) {
		var oldonload = window.onload;
		if (typeof window.onload != 'function') {
			window.onload = func;
		} else {
			window.onload = function() {
			oldonload();
			func();
		}
	}
}
JS_FUNC;
			$js = $jsfn."\n\n".$js;
			$js_cascade_fn_included = true;
		}
		if ($this->set_on_start) {
			$js .= "function cassel_".$this->id."_onload() {\n"
					."if (obj = document.getElementById(\"".$this->id."\")) {\n"
					."	curval = obj.options[obj.selectedIndex].text;\n"
					."cascade(\"".$this->id."\", curval);\n"
					."}\n"
					."}\n";
			$js .= "addLoadEvent(cassel_".$this->id."_onload);\n";
		}
		$js = "<script language=\"Javascript\">\n".$js."\n</script>";
		return $js;
	}
/*>>>*/
	function setoptions($opts=array()) {/*<<<*/
		$this->options = $opts;
	}
/*>>>*/
	function add_cascade($childid, $matchval, $newopts = array()) {/*<<<*/
		$this->cascades[$childid][$matchval] = $newopts;
	}
/*>>>*/
	function jsquote($str) {
		return str_replace("\"", "\\\"", str_replace("'", "\\\'", $str));
	}
}
?>
