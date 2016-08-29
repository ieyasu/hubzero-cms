<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */
// No direct access
defined('_HZEXEC_') or die();

// Get creator name
$creator = $this->pub->creator('name') . ' (' . $this->pub->creator('username') . ')';

// Version status
$status = $this->pub->getStatusName();
$class  = $this->pub->getStatusCss();

// Get block content
$blockcontent = $this->pub->_curationModel->parseBlock('edit');

?>
<style>
.vacheck-error:before {
	content: "\26A0";
	font-family: "Fontcons";
	color: #ff5923;
	font-size: 14pt;
	float: left;
	line-height:100%;
}
.vacheck-pass:before {
	content: "\f058";
	font-family: "Fontcons";
	color: #35B433;
	font-size: 14pt;
	float: left;
	line-height: 100%;
}
.vacheck-pass {
	float: left;
	cursor:pointer;
}
.vacheck-error {
	float: left;
	cursor:pointer;
}
.vacheck-wait {
	float: left;
	cursor:pointer;
}
.vacheck-wait:before {
	content: "\f021";
	font-family: "Fontcons";
	color: #61899E;
	font-size: 14pt;
	padding: 1px;
	float: left;
	line-height: 100%;
	-webkit-animation:spin 4s linear infinite;
	-moz-animation:spin 4s linear infinite;
	animation:spin 4s linear infinite;
}
.fancybox-inner pre {
	background: none;
}
</style>

<?php 
// Write title
echo \Components\Publications\Helpers\Html::showPubTitle( $this->pub, $this->title);

// Draw status bar
echo $this->pub->_curationModel->drawStatusBar();
?>
<div id="pub-body">
	<?php echo $blockcontent; ?>
</div>
<p class="rightfloat">
	<a href="<?php echo Route::url($this->pub->link('version')); ?>" class="public-page" rel="external" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_VIEW_PUB_PAGE'); ?>"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_VIEW_PUB_PAGE'); ?></a>
</p>

<script>
jQuery(document).ready(function($) {
	HUB.ProjectPublicationsDraft.initialize();

<?php if ($this->pub->get('_type')->alias == "compactmodels"): ?>
	var files = [];
	$('.identifier').show();
	$('.identifier').html('loading');
	// Load
	$.ajax({
		method: "POST",
		url: '/api/vacheck/getProjectFiles',
		data: { publication_id: <?php echo $this->pub->get('id'); ?> }
		})
		.success(function(data){
			var vaFiles = JSON.parse(data);
			console.log(vaFiles);
			$(vaFiles).each(function() {
					fid = (this.id);
					$('#file-'+fid).prepend('<a href="#" class="vacheck-wait">&nbsp;</a>');
					$('#file-'+fid).show();
				});
		});

	$.ajax({
		method: "POST",
		url: '/api/vacheck/checkfile',
		data: { publication_id: <?php echo $this->pub->get('id'); ?>}
	})
	.success(function(data){
		$('.vacheck-wait').hide();
		var vaCheckOutput = JSON.parse(data.files);
		$(vaCheckOutput).each(function() {
			fid = (this.id);
			if (typeof(this.vacheck) != "undefined")
			{
				vacheck = this.vacheck;
				if (this.vacheck)
				{
					$('#file-'+fid).prepend('<a id="vacheck-error" href="/api/vacheck/getErrors?publication_id=<?php echo $this->pub->get('id'); ?>&file_id='+fid+'" class="vacheck-error fancybox fancybox.ajax"></a>');
					$('#file-'+fid).show();
					$("a#vacheck-error").fancybox({
						autoSize: false,
						width: '700px'
					});
				}
			}

		});
	});
<?php endif; ?>
});
</script>
