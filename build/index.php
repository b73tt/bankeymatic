<!DOCTYPE html>
<html lang="en-US">
<head>
<meta charset="utf-8">
<meta name="viewport" content="initial-scale=1">
<title>BankeyMATIC</title>
<link rel="stylesheet" href="build.css">
<script defer src="d3.min.js"></script>
<script defer src="umd.js"></script>
<script defer src="constants.js"></script>
<script defer src="sankey.js"></script>
<script defer src="lz-string.min.js"></script>
<script defer src="sankeymatic.js"></script>
<script defer src="bankeymatic.js"></script>
</head>
<body>
<main>

<!-- MOVING NODES -->
<div class="diagram_main">
<div id="top_messages_container">
  <div id="info_messages"></div>
  <div id="issue_messages"></div>
</div>

<p id="chart">
<svg id="svg_scratch" height="600" width="600" xmlns="http://www.w3.org/2000/svg" class="hidden_under"></svg>
<svg id="sankey_svg" height="600" width="600" xmlns="http://www.w3.org/2000/svg"></svg>
</p>
<canvas id="png_preview" height="600" width="100%" style="background-color: transparent; display: none;"></canvas>
<p id="reset_moves_area" class="form_elements1">
Move Nodes by <em>dragging</em>. Double-click a Node to reset, or: <button type="button" id="reset_all_moved_nodes" onclick="resetMovesAndRender(); return false;">Reset all moved Nodes</button>
</p>
</div> <!-- diagram_main -->

<p id="replace_graph_warning" style="display: none;">
<em>This will <strong>erase</strong> your changes. Are you sure?</em><br />
<input type="hidden" id="demo_graph_chosen" value="" />
<button type="button" id="replace_graph_yes" onclick="replaceGraphConfirmed(); return false;">Yes, replace the diagram</button>
<button type="button" id="replace_graph_no" onclick="hideReplaceGraphWarning(); return false;">Cancel</button>
</p>
</div>

<div class="fullwidth">
<form id="skm_form" onsubmit="process_sankey(); return false;">

<div class="skm_grid">

<div class="center diagram_controls">

<h2 class="ui_head" onclick="togglePanel('input_options');">
<span id="input_options_indicator" class="indicator">&ndash;</span>
Inputs<span id="input_options_hint">:</span></h2>

<div id="input_options" class="text_center grid_sidebar_right">
<div>
    <textarea id="flows_in" title="Diagram Inputs" rows="29" cols="60" class="font_sans" onchange="process_sankey();" onkeyup="debounced_process_sankey();"></textarea>
</div>

<div class="separated_stack">

<div class="top_center_self"><button id="preview_graph" type="submit">Show &gt;</button></div>

<div class="bottom_center_self">
<button id="save_my_work_button" type="submit" class="loadsave_button"
 onclick="saveDiagramToFile(); return false;"
 title="Save the current diagram and settings to a local text file">Save my<br />work <strong>↘</strong></button>

<p>
<button
  onclick="loadDiagramFile(); return false;" 
id="load_diagram_button" 
 class="loadsave_button"
 title="Load a diagram definition from a text file"><strong>↖</strong> Load<br />from server</button>

</div>
</div>

<p class="form_elements3 grid_entire_row">
<strong>Arrange the diagram:</strong><br />
<span><input name="layout_order" id="layout_order_automatic" value="automatic" type="radio" onchange="process_sankey();" checked="checked" /><label for="layout_order_automatic" class="ropt">Automatically</label></span>
<span><input name="layout_order" id="layout_order_exact" value="exact" type="radio" onchange="process_sankey();" class="spaced_checkbox" /><label for="layout_order_exact" class="ropt">Using the exact input order</label></span>
</p>
</div>

</div> <!-- End LAYOUT -->


<div class="diagram_about">
<table id="messages" class="expandable_box">
<tr><td>
  <div id="messages_area">
    <h4>About this diagram</h4>
    <div id="totals_area"></div>
  </div>
  <div id="console_area" style="display: none;">
    <details><summary>Console</summary>
      <div id="console_lines"></div>
    </details>
  </div>
</td></tr>
<tr><td id="imbalances_area">
<div id="imbalance_control">
<span class="underline"><strong><em>When Total Inputs &ne; Total Outputs:</em></strong></span><br>
<p class="form_elements3">
  Attach incomplete flow groups to:<br>
<span><input name="layout_attachincompletesto" id="layout_attachto_leading" value="leading" type="radio" onchange="process_sankey();" /><label for="layout_attachto_leading" class="ropt">The leading edge of the Node</label></span><br>
<span><input name="layout_attachincompletesto" id="layout_attachto_trailing" value="trailing" type="radio" onchange="process_sankey();" /><label for="layout_attachto_trailing" class="ropt">The trailing edge of the Node</label></span><br>
<span><input name="layout_attachincompletesto" id="layout_attachto_nearest" value="nearest" type="radio" onchange="process_sankey();" checked="checked" /><label for="layout_attachto_nearest" class="ropt">The edge nearest to the flow group's center</label></span>
</p>
<p class="lastrow">
<input type="checkbox" id="meta_listimbalances" value="1" onchange="process_sankey();" checked="checked" />
<label class="ropt" for="meta_listimbalances">List all imbalanced Nodes</label>
</p>
<div id="imbalance_messages"></div>
</div>
</td></tr>
<tr><td>
<div id="conversion">
<strong>Conversion</strong>
<table>
<tr><td>Daily</td><td><input id="conversion1" onclick="this.select()" onkeyup="convert(1)"/></td></tr>
<tr><td>Weekly</td><td><input id="conversion7" onclick="this.select()" onkeyup="convert(7)"/></td></tr>
<tr><td>Fortnightly</td><td><input id="conversion14" onclick="this.select()" onkeyup="convert(14)"/></td></tr>
<tr><td>Monthly</td><td><input id="conversion30.5" onclick="this.select()" onkeyup="convert(30.5)"/></td></tr>
<tr><td>Quarterly</td><td><input id="conversion91.3125" onclick="this.select()" onkeyup="convert(91.3125)"/></td></tr>
<tr><td>Annually</td><td><input id="conversion365.25" onclick="this.select()" onkeyup="convert(365.25)"/></td></tr>

</table>
</div>
</td></tr>
</table>
</div> <!-- diagram_about -->


</div>
<!-- LABELS -->

<datalist id="tick100"><option value="100"></option></datalist>
<datalist id="tick400"><option value="400"></option></datalist>
<datalist id="tick-align">
<option value="-1">Before</option>
<option value="0">Centered</option>
<option value="1">After</option>
</datalist>
<datalist id="tick-stages">
<option value="2"></option><option value="3"></option>
<option value="4"></option><option value="5"></option>
<option value="6"></option><option value="7"></option>
<option value="8"></option><option value="9"></option>
<option value="10"></option><option value="11"></option>
<option value="12"></option><option value="13"></option>
<option value="14"></option><option value="15"></option>
</datalist>

<h2 class="ui_head" onclick="togglePanel('label_options');">
<span id="label_options_indicator" class="indicator">+</span>
Labels<span id="label_options_hint">...</span></h2>

<div id="label_options" class="form_chunk" style="display: none;">

<div class="vs b-pad indented">

<div class="hs align-baseline gap-medium">
<span><input type="checkbox" id="labelname_appears" value="1" onchange="process_sankey();" checked="checked">
<label for="labelname_appears" class="ropt">Show <strong>Names</strong></label></span>

<span class="no_wrap" onmouseover="revealVal('labelname_weight');" onmouseout="fadeVal('labelname_weight');"><label for="labelname_weight" class="sr-only">Style for Node Names</label>
<span class="smalllabel font_light">Light</span><span class="output_container"><input id="labelname_weight" class="slider_xsmall" type="range" min="100" max="700" step="300" value="400" list="tick400" onfocus="revealVal('labelname_weight');" onblur="fadeVal('labelname_weight');" oninput="updateOutput('labelname_weight'); process_sankey();" onchange="process_sankey();"><output id="labelname_weight_val" for="labelname_weight" class="fade-init output_6">-</output></span><span class="smalllabel font_bolder">Bold</span></span>
</div>

<div class="hs align-baseline gap-medium">
<span><input type="checkbox" id="labelvalue_appears" value="1" onchange="process_sankey();" checked="checked">
<label for="labelvalue_appears" class="ropt">Show <strong>Values</strong></label></span>

<span class="no_wrap" onmouseover="revealVal('labelvalue_weight');" onmouseout="fadeVal('labelvalue_weight');"><label for="labelvalue_weight" class="sr-only">Style for Node Values</label>
<span class="smalllabel font_light">Light</span><span class="output_container"><input id="labelvalue_weight" class="slider_xsmall" type="range" min="100" max="700" step="300" value="400" list="tick400" onfocus="revealVal('labelvalue_weight');" onblur="fadeVal('labelvalue_weight');" oninput="updateOutput('labelvalue_weight'); process_sankey();" onchange="process_sankey();"><output id="labelvalue_weight_val" for="labelvalue_weight" class="fade-init output_6">-</output></span><span class="smalllabel font_bolder">Bold</span></span>
</div>
</div>

<div class="hs gap-medium indented align-baseline b-pad">
<span>
<strong>Font:</strong>
<span class="no_wrap"><input name="labels_fontface" type="radio" id="sans_serif" value="sans-serif" checked="checked" onchange="process_sankey();" /><label for="sans_serif" class="ropt font_sans">sans</label></span>
<span class="no_wrap"><input name="labels_fontface" type="radio" id="serif" value="serif" onchange="process_sankey();" /><label for="serif" class="ropt font_serif">serif</label></span>
<span class="no_wrap"><input name="labels_fontface" type="radio" id="monospace" value="monospace" onchange="process_sankey();" /><label for="monospace" class="ropt font_mono">mono</label></span>
</span>
<span><label for="labels_color"><strong>Color:</strong></label> <input type="color" id="labels_color" size="7" maxlength="7" value="#000000" onchange="process_sankey();"></span>
</div>

<details open><summary><strong>Sizes</strong></summary>
<div class="vs gap-small align-center">
<div class="hs align-center justify-center gap-large b-divider">
<div class="vs align-center">
<label for="labelname_size"><strong>Base Size:</strong></label>
<input id="labelname_size" type="number" class="number_100s" min="6" step="0.5" value="16" onchange="process_sankey();"></div>

<div class="vs align-center text_center" onmouseover="revealVal('labels_relativesize');" onmouseout="fadeVal('labels_relativesize');">
<label for="labels_relativesize" title="Set relative size of Names and Values">Magnify:</label>
<div class="hs align-center gap-small no_wrap">
    <div class="align-center"><strong>Names</strong></div>
    <span class="output_container"><input id="labels_relativesize" class="slider_small" type="range" min="50" max="150" value="110" list="tick100" onfocus="revealVal('labels_relativesize');" onblur="fadeVal('labels_relativesize');" oninput="updateOutput('labels_relativesize'); process_sankey();" onchange="process_sankey();"><output id="labels_relativesize_val" for="labels_relativesize" class="fade-init output_12">-</output></span>
    <div class="align-center"><strong>Values</strong></div>
</div>
</div>
</div>

<div class="vs align-center justify-center text_center b-divider" onmouseover="revealVal('labels_magnify');" onmouseout="fadeVal('labels_magnify');">
    <label for="labels_magnify">Show larger labels for:</label>
    <div class="hs justify-center gap-small no_wrap">
        <div class="align-center"><strong>Small</strong><br />Amounts</div>
        <span class="output_container"><input id="labels_magnify" class="slider_medium" type="range" min="50" max="150" value="100" list="tick100" onfocus="revealVal('labels_magnify');" onblur="fadeVal('labels_magnify');" oninput="updateOutput('labels_magnify'); process_sankey();" onchange="process_sankey();"><output id="labels_magnify_val" for="labels_magnify" class="fade-init output_12">-</output></span>
        <div class="align-center"><strong>Large</strong><br />Amounts</div>
    </div>
</div>

<div class="hs gap-small" onmouseover="revealVal('labels_linespacing');" onmouseout="fadeVal('labels_linespacing');">
<label for="labels_linespacing" id="spacing_fld_label"><strong>Line Spacing:</strong></label>
<span class="nowrap">
<span class="smalllabel">Min</span>
<span class="output_container"><input id="labels_linespacing" class="slider_medium" type="range" min="0.0" max="1.0" step="0.05" value="0.2" onfocus="revealVal('labels_linespacing');" onblur="fadeVal('labels_linespacing');" oninput="updateOutput('labels_linespacing'); process_sankey();" onchange="process_sankey();"><output id="labels_linespacing_val" for="labels_linespacing" class="fade-init output_3">-</output></span>
<span class="smalllabel">Max</span>
</span>
</div>
<div class="text_center">Add line breaks inside labels using <code>\n</code></div>
</div>
</details><!-- End Sizes -->

<details><summary><strong>Placement</strong></summary>
<div>
<div class="vs align-center">
<div class="vs gap-small">
<fieldset class="simple">
<span class="no_wrap"><input name="labelposition_scheme" type="radio" id="label_scheme_auto" value="auto" onchange="process_sankey();" checked="checked" /><label for="label_scheme_auto" class="ropt"><strong>Automatic</strong></label></span>
<div class="hs align-center justify-center gap-small l-pad">
<div class="indented l-pad" style="max-width: 20em;">
When a Node has an <strong>empty side</strong>, place its label there.

<div class="vs align-center justify-center text_center" onmouseover="revealVal('labelposition_autoalign');" onmouseout="fadeVal('labelposition_autoalign');">
<label for="labelposition_autoalign" style="line-height: 2;">When neither side is empty, place labels:</label>
<div class="hs align-center justify-center gap-small">
<div class="align-center no_wrap"><strong>Before</strong><br />the Node</div>
<span class="output_container"><input id="labelposition_autoalign" class="slider_medium" type="range" min="-1" max="1" step="1" value="0" onfocus="revealVal('labelposition_autoalign');" onblur="fadeVal('labelposition_autoalign');" oninput="updateOutput('labelposition_autoalign'); checkRadio('label_scheme_auto'); process_sankey();" onchange="checkRadio('label_scheme_auto'); process_sankey();" list="tick-align"><output id="labelposition_autoalign_val" for="labelposition_autoalign" class="fade-init output_6">-</output></span>
<div class="align-center no_wrap"><strong>After</strong><br />the Node</div>
</div>
</div>

</div>
</div>
</fieldset>
<fieldset class="simple">
<span class="no_wrap"><input name="labelposition_scheme" type="radio" id="label_scheme_perstage" value="per_stage" onchange="process_sankey();" /><label for="label_scheme_perstage" class="ropt"><strong>Per Stage</strong></label></span>

<div class="hs align-center justify-center gap-small t-pad b-divider">
<span class="text_right">Place labels for<br /> the <strong>first stage</strong>:</span>
<div class="vs">
<span class="no_wrap"><input name="labelposition_first" type="radio" id="label_first_before" value="before" onchange="checkRadio('label_scheme_perstage'); process_sankey();" checked="checked" /><label for="label_first_before" class="ropt">Before the Nodes</label></span>

<span class="no_wrap"><input name="labelposition_first" type="radio" id="label_first_after" value="after" onchange="checkRadio('label_scheme_perstage'); process_sankey();" /><label for="label_first_after" class="ropt">After the Nodes</label></span>
</div>
</div>

<div class="vs align-center justify-center text_center" onmouseover="revealVal('labelposition_breakpoint');" onmouseout="fadeVal('labelposition_breakpoint');">
<label for="labelposition_breakpoint" style="line-height: 2;">Place labels on the <strong>opposite side</strong> starting at:</label>
<div class="hs align-center justify-center gap-small">
<span class="no_wrap">Stage <strong>2</strong></span><span class="output_container"><input id="labelposition_breakpoint" class="slider_medium" type="range" min="2" max="4" step="1" value="4" onfocus="revealVal('labelposition_breakpoint');" onblur="fadeVal('labelposition_breakpoint');" oninput="updateOutput('labelposition_breakpoint'); checkRadio('label_scheme_perstage'); process_sankey();" onchange="checkRadio('label_scheme_perstage'); process_sankey();" list="tick-stages"><output id="labelposition_breakpoint_val" for="labelposition_breakpoint" class="fade-init output_12">-</output></span><span>(Never)</span>
</div>
</div>
</fieldset>
</div>
</div>
</div>
</details> <!-- End Placement -->

<details><summary><strong>Arrange Names &amp; Values</strong></summary>
<div class="vs justify-center align-center gap-small">
<div class="hs justify-center align-center gap-small b-pad">

<div class="hs align-center gap-small">
<div class="vs gap-small">
<div class="hs align-center">
<input name="labelvalue_position" type="radio" id="labelvalue_after" value="after" onchange="process_sankey();" /><label for="labelvalue_after" class="ropt rrect arr-option">Examples:&nbsp;100</label>
</div>
<div class="hs align-center">
<input name="labelvalue_position" type="radio" id="labelvalue_below" value="below" onchange="process_sankey();" checked="checked" /><label for="labelvalue_below" class="ropt rrect arr-option">Examples<br />100</label>
</div>
</div>
<div class="vs gap-small">
<div class="hs align-center">
<input name="labelvalue_position" type="radio" id="labelvalue_before" value="before" onchange="process_sankey();" /><label for="labelvalue_before" class="ropt rrect arr-option">100&nbsp;Examples</label>
</div>
<div class="hs align-center">
<input name="labelvalue_position" type="radio" id="labelvalue_above" value="above" onchange="process_sankey();" /><label for="labelvalue_above" class="ropt rrect arr-option">100<br />Examples</label>
</div>
</div>
</div>
</div>

<div class="hs gap-medium space-around align-center bg-darker pad-medium" onmouseover="revealVal('labels_highlight');" onmouseout="fadeVal('labels_highlight');">
<label for="labels_highlight" class="highlight_fld_label"><strong>Highlights:</strong></label>
<div><span class="smalllabel">0</span><span class="output_container"><input id="labels_highlight" class="slider_small" type="range" min="0" max="0.9" step="0.05" value="0.75" onfocus="revealVal('labels_highlight');" onblur="fadeVal('labels_highlight');" oninput="updateOutput('labels_highlight'); process_sankey();" onchange="process_sankey();"><output id="labels_highlight_val" for="labels_highlight" class="fade-init output_3">-</output></span><span class="smalllabel highlight_fld_label">Max</span>
</div>
</div>

<span><input type="checkbox" id="labels_hide" value="1" onchange="process_sankey();"><label for="labels_hide" class="ropt">Temporarily Hide Labels</label></span>
</div>
</details> <!-- End Arrange Names & Values -->

<details><summary><strong>Number Format</strong></summary>
<div class="hs align-center gap-small space-around">
<div class="vs gap-small">
<span><label for="value_prefix">Prefix:</label> <input type="text" id="value_prefix" size="4" maxlength="10" onchange="process_sankey();"></span>
<span><label for="value_suffix">Suffix:</label> <input type="text" id="value_suffix" size="4" maxlength="10" onchange="process_sankey();"></span>
</div>

<div class="text_center">
<label for="value_format">Format:</label>
<select id="value_format" onchange="process_sankey();">
<option value=",." selected="selected">1,000,000.00</option>
<option value=".,">1.000.000,00</option>
<option value=" .">1 000 000.00</option>
<option value=" ,">1 000 000,00</option>
<option value="X.">1000000.00</option>
<option value="X,">1000000,00</option>
</select>
<br />
<span><input type="checkbox" id="labelvalue_fullprecision" value="1" onchange="process_sankey();" checked="checked">
<label for="labelvalue_fullprecision" class="ropt">Show trailing 0s <small>(best for currency)</small></label></span>
</div>
</div>
</details> <!-- End Number Format -->
</div> <!-- End LABELS -->

<!-- NODES -->

<h2 class="ui_head" onclick="togglePanel('node_options');">
<span id="node_options_indicator" class="indicator">&ndash;</span>
Nodes<span id="node_options_hint">:</span></h2>
<!-- tabindex="0" -->
<div id="node_options" class="form_chunk">
<p class="form_elements1">
<span class="no_wrap" onmouseover="revealVal('node_h');" onmouseout="fadeVal('node_h');">
<label for="node_h"><strong>Height:</strong></label>
.<span class="output_container"><input id="node_h" class="slider_small" type="range" min="0" max="100" step="0.5" value="50" onfocus="revealVal('node_h');" onblur="fadeVal('node_h');" oninput="updateOutput('node_h'); process_sankey();" onchange="process_sankey();"><output id="node_h_val" for="node_h" class="fade-init output_6">-</output></span>|</span>


<span class="no_wrap" onmouseover="revealVal('node_spacing');" onmouseout="fadeVal('node_spacing');">
<label for="node_spacing" class="spaced_label"><strong>Spacing:</strong></label>
<span class="smalllabel">0</span><span class="output_container"><input id="node_spacing" class="slider_small" type="range" min="0" max="100" step="0.5" value="75" onfocus="revealVal('node_spacing');" onblur="fadeVal('node_spacing');" oninput="updateOutput('node_spacing'); process_sankey();" onchange="process_sankey();"><output id="node_spacing_val" for="node_spacing" class="fade-init output_6">-</output></span><span class="smalllabel">Max</span></span>
</p>

<p class="form_elements2">
<span class="no_wrap"><label for="node_w"><strong>Width:</strong></label>
<input id="node_w" type="number" class="number_100s" min="0" value="12" step="1" onchange="process_sankey();"></span>

<span class="no_wrap"><label for="node_border" class="spaced_label"><strong>Border:</strong></label>
<input id="node_border" type="number" class="number_10s" min="0" value="0" step="1" onchange="process_sankey();"></span>

<span class="no_wrap" onmouseover="revealVal('node_opacity');" onmouseout="fadeVal('node_opacity');">
<label for="node_opacity" class="spaced_label"><strong>Opacity:</strong></label>
<span class="smalllabel">0</span><span class="output_container"><input id="node_opacity" class="slider_xxsmall" type="range" min="0" max="1" step="0.05" value="1.0" onfocus="revealVal('node_opacity');" onblur="fadeVal('node_opacity');" oninput="updateOutput('node_opacity'); process_sankey();" onchange="process_sankey();"><output id="node_opacity_val" for="node_opacity" class="fade-init output_3">-</output></span><span class="smalllabel">1</span></span>
</p>

<fieldset class="form_elements1">
<legend>Default Node Colors</legend>
<div class="fieldset_contents">
Use <input name="node_theme" type="radio" id="node_theme_none" value="none" onchange="process_sankey();" /><label for="node_theme_none" class="ropt">one color:</label>
<input type="color" id="node_color" title="Single Node Color" size="7" maxlength="7" value="#888888" onchange="checkRadio('node_theme_none'); process_sankey();"> or a Theme:<br />
<table id="color_themes">
<tr>
<td>
<span id="theme_a" class="theme_container">
<input name="node_theme" type="radio" id="theme_a_radio" value="a" onchange="process_sankey();" checked="checked" /><label id="theme_a_label" for="theme_a_radio" class="ropt"></label>
</span>
</td>
<td>
<span class="no_wrap">
<button id="theme_a_rotate_left" type="submit" title="Rotate theme colors left" onclick="nudgeColorTheme('a',1); return false;">&lt;</button>
<span id="theme_a_guide"></span><input type="hidden" id="themeoffset_a" value="6">
<button id="theme_a_rotate_right" type="submit" title="Rotate theme colors right" onclick="nudgeColorTheme('a',-1); return false;">&gt;</button>
</span>
</td>
</tr>
<tr>
<td>
<span id="theme_b" class="theme_container">
<input name="node_theme" type="radio" id="theme_b_radio" value="b" onchange="process_sankey();" /><label id="theme_b_label" for="theme_b_radio" class="ropt"></label>
</span>
</td>
<td>
<span class="no_wrap">
<button id="theme_b_rotate_left" type="submit" title="Rotate theme colors left" onclick="nudgeColorTheme('b',1); return false;">&lt;</button>
<span id="theme_b_guide"></span><input type="hidden" id="themeoffset_b" value="0">
<button id="theme_b_rotate_right" type="submit" title="Rotate theme colors right" onclick="nudgeColorTheme('b',-1); return false;">&gt;</button>
</span>
</td>
</tr>
<tr>
<td>
<span id="theme_c" class="theme_container">
<input name="node_theme" type="radio" id="theme_c_radio" value="c" onchange="process_sankey();" /><label id="theme_c_label" for="theme_c_radio" class="ropt"></label>
</span>
</td>
<td>
<span class="no_wrap">
<button id="theme_c_rotate_left" type="submit" title="Rotate theme colors left" onclick="nudgeColorTheme('c',1); return false;">&lt;</button>
<span id="theme_c_guide"></span><input type="hidden" id="themeoffset_c" value="0">
<button id="theme_c_rotate_right" type="submit" title="Rotate theme colors right" onclick="nudgeColorTheme('c',-1); return false;">&gt;</button>
</span>
</td>
</tr>
<tr>
<td>
<span id="theme_d" class="theme_container">
<input name="node_theme" type="radio" id="theme_d_radio" value="d" onchange="process_sankey();" /><label id="theme_d_label" for="theme_d_radio" class="ropt"></label>
</span>
</td>
<td>
<span class="no_wrap">
<button id="theme_d_rotate_left" type="submit" title="Rotate theme colors left" onclick="nudgeColorTheme('d',1); return false;">&lt;</button>
<span id="theme_d_guide"></span><input type="hidden" id="themeoffset_d" value="0">
<button id="theme_d_rotate_right" type="submit" title="Rotate theme colors right" onclick="nudgeColorTheme('d',-1); return false;">&gt;</button>
</span>
</td>
</tr>
</table>
</div>

</fieldset>
</div> <!-- End NODES -->

<!-- FLOWS -->

<h2 class="ui_head" onclick="togglePanel('flow_options');">
<span id="flow_options_indicator" class="indicator">&ndash;</span>
Flows<span id="flow_options_hint">:</span></h2>

<div id="flow_options" class="form_chunk">
<p class="form_elements1">
<span class="no_wrap" onmouseover="revealVal('flow_opacity');" onmouseout="fadeVal('flow_opacity');">
<label for="flow_opacity"><strong>Opacity:</strong></label>
<span class="smalllabel">0</span><span class="output_container"><input id="flow_opacity" class="slider_xsmall" type="range" min="0" max="1" step="0.05" value="0.45" onfocus="revealVal('flow_opacity');" onblur="fadeVal('flow_opacity');" oninput="updateOutput('flow_opacity'); process_sankey();" onchange="process_sankey();"><output id="flow_opacity_val" for="flow_opacity" class="fade-init output_3">-</output></span><span class="smalllabel">1</span></span>

<span class="no_wrap" onmouseover="revealVal('flow_curvature');" onmouseout="fadeVal('flow_curvature');">
<label for="flow_curvature" class="spaced_label"><strong>Curviness:</strong></label>&nbsp;
|<span class="output_container"><input id="flow_curvature" class="slider_small" type="range" min="0.1" max="0.9" step="0.04" value="0.5" onfocus="revealVal('flow_curvature');" onblur="fadeVal('flow_curvature');" oninput="updateOutput('flow_curvature'); process_sankey();" onchange="process_sankey();"><output id="flow_curvature_val" for="flow_curvature" class="fade-init output_3">-</output></span>(</span>
</p>

<fieldset class="form_elements2">
<legend>Default Flow Colors</legend>

<div class="fieldset_contents">

Use <input name="flow_inheritfrom" type="radio" id="flow_inherit_none" value="none" onchange="process_sankey();" /><label for="flow_inherit_none" class="ropt">one color:</label>

<input type="color" id="flow_color" title="Single Flow Color" size="7" maxlength="7" value="#999999" onchange="checkRadio('flow_inherit_none'); process_sankey();" /> or colors from:<br />
<span class="no_wrap"><input name="flow_inheritfrom" type="radio" id="flow_inherit_from_source" value="source" onchange="process_sankey();" /><label class="ropt" for="flow_inherit_from_source">each flow's Source</label></span>
<span class="no_wrap"><input name="flow_inheritfrom" type="radio" id="flow_inherit_from_target" value="target" onchange="process_sankey();" /><label class="ropt" for="flow_inherit_from_target">each flow's Target</label></span>
<br />
<span class="no_wrap"><input name="flow_inheritfrom" type="radio" id="flow_inherit_outside_in" value="outside-in" onchange="process_sankey();" checked="checked" /><label class="ropt" for="flow_inherit_outside_in">the outermost nodes (flowing in)</label></span>
</div>
</fieldset>
</div> <!-- End FLOWS -->
<!-- LAYOUT OPTIONS -->

<h2 class="ui_head" onclick="togglePanel('layout_options');" id="layout_options_section">
<span id="layout_options_indicator" class="indicator">+</span>
Layout Options<span id="layout_options_hint">...</span></h2>
<div id="layout_options" class="form_chunk" style="display: none;">
<div class="form_elements1 text_center">
<small>Note: these options only apply to diagrams<br /> with 3 or more columns of nodes:</small>

<table style="width: 100%;"><tr>
<td>
<input type="checkbox" id="layout_justifyorigins" value="1" onchange="process_sankey();"><br />
<label for="layout_justifyorigins" class="ropt">Place all<br /> flow <strong>origins</strong><br /> at the <strong>left</strong> edge</label>
</td>
<td class="text_right">
<input type="checkbox" id="layout_justifyends" value="1" onchange="process_sankey();"><br />
<label for="layout_justifyends" class="spaced_label ropt">Place all<br /> flow <strong>endpoints</strong><br /> at the <strong>right</strong> edge</label>
</td>
</tr></table>
</div>
<p class="form_elements2 text_center">
<input type="checkbox" id="layout_reversegraph" value="1" onchange="process_sankey();">
<label for="layout_reversegraph" class="ropt"><strong>Reverse the graph</strong> (flow right-to-left)</label>
</p>

<p class="form_elements1 text_center">
<strong>Diagram Scale</strong> = <span id="scale_figures"></span><br />
For fair comparisons <em>between</em> diagrams:<br />
1) Use the same units for each, and 2) Make their<br />
Diagram Scales match as closely as possible.
</p>

<h3 class="ui_head" onclick="togglePanel('debug_options');"><span id="debug_options_indicator" class="indicator">+</span>Tools for Debugging<span id="debug_options_hint">...</span></h3>
<div id="debug_options" class="form_chunk" style="display: none;">
<div class="form_elements1">
  <span class="no_wrap" onmouseover="revealVal('internal_iterations');" onmouseout="fadeVal('internal_iterations');">
    <label for="internal_iterations" class="spaced_label"><strong># of Layout Iterations:</strong></label>
    <span class="smalllabel">0</span>
    <span class="output_container"><input id="internal_iterations" class="slider_small" type="range" min="0" max="30" step="1" value="25" onfocus="revealVal('internal_iterations');" onblur="fadeVal('internal_iterations');" oninput="updateOutput('internal_iterations'); process_sankey();" onchange="process_sankey();"><output id="internal_iterations_val" for="internal_iterations" class="fade-init output_3">-</output></span>
    <span class="smalllabel">30</span>
    </span>
</div>
<div class="form_elements2">
<input type="checkbox" id="internal_revealshadows" value="1" onchange="process_sankey();" class="spaced_checkbox"><label for="internal_revealshadows" class="ropt">Reveal Shadow Nodes</label>
</div>

</div> <!-- End Debug options -->
</div>
<!-- SIZE & SPACING & BACKGROUND -->

<div class="diagram_size expandable_xbox">
<h2 class="ui_head" onclick="togglePanel('diagram_options');">
<span id="diagram_options_indicator" class="indicator">&ndash;</span>
Diagram Size &amp; Background<span id="diagram_options_hint">:</span></h2>

<div id="diagram_options" class="form_chunk">
<p class="form_elements1">
<span class="no_wrap"><label for="size_w" title="Diagram Width in pixels"><strong>Width:</strong></label>
<input id="size_w" type="number" class="number_10000s" min="40" value="600" step="2" onchange="process_sankey();"></span>
<span class="no_wrap"><label for="size_h" class="spaced_label" title="Diagram Height in pixels"><strong>Height:</strong></label>
<input id="size_h" type="number" class="number_10000s" min="40" value="600" step="2" onchange="process_sankey();"></span>

<label for="bg_color" class="spaced_label"><strong>Background Color:</strong></label>
<input type="color" id="bg_color" size="7" maxlength="7" value="#FFFFFF" onchange="process_sankey();">
<input type="checkbox" id="bg_transparent" value="1" onchange="process_sankey();" class="spaced_checkbox"><label for="bg_transparent" class="ropt">Transparent</label><br />
</p>

<p class="form_elements2">
<strong>Margins:</strong>
<span class="no_wrap"><label for="margin_l" class="spaced_label">Left</label>
<input id="margin_l" type="number" class="number_100s" min="0" value="12" step="1" onchange="process_sankey();">
<label for="margin_r">Right</label>
<input id="margin_r" type="number" class="number_100s" min="0" value="12" step="1" onchange="process_sankey();"></span>
<span class="no_wrap"><label for="margin_t" class="spaced_label">Top</label>
<input id="margin_t" type="number" class="number_100s" min="0" value="18" step="1" onchange="process_sankey();">
<label for="margin_b">Bot</label>
<input id="margin_b" type="number" class="number_100s" min="0" value="20" step="1" onchange="process_sankey();"></span>
</p>
</div>
</div>



</div> <!-- End skm_grid -->
</form> <!-- End SKM Form -->

</div>
</main>

</body>
</html>
