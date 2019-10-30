<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script src="dist_files/jquery.imgareaselect.js" type="text/javascript"></script>
<script src="dist_files/jquery.form.js"></script>
<link rel="stylesheet" href="imgareaselect.css">
<script src="image_functions.js"></script>

<?php

function imageUpload($box) {
	global $perms, $member;
?>

<div id="pic_upload_modal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3>Change Picture</h3>
			</div>
			<div class="modal-body">
			<form id="cropimage" method="post" enctype="multipart/form-data" action="change_pic.php">
				<strong>Upload Image:</strong> <br><br>
				<input type="file" name="pic" id="pic" />
				<input type="hidden" name="member_uid" id="member_uid" value="<? echo $member['member_uid'] ?>">
				<input type="hidden" name="hdn-x1-axis" id="hdn-x1-axis" value="" />
				<input type="hidden" name="hdn-y1-axis" id="hdn-y1-axis" value="" />
				<input type="hidden" name="hdn-x2-axis" value="" id="hdn-x2-axis" />
				<input type="hidden" name="hdn-y2-axis" value="" id="hdn-y2-axis" />
				<input type="hidden" name="hdn-thumb-width" id="hdn-thumb-width" value="" />
				<input type="hidden" name="hdn-thumb-height" id="hdn-thumb-height" value="" />
				<input type="hidden" name="action" value="" id="action" />
				<input type="hidden" name="image_name" value="" id="image_name" />
				<div id='preview-pic'></div>
				<div id="thumbs" style="padding:5px; width:600p"></div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" id="save_crop" class="btn btn-primary">Crop & Save</button>
			</div>
		</div>
	</div>
</div>
<?
}
