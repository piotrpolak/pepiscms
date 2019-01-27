<?php if ($is_editor): ?>
    <script>
        self.resizeTo(1000, 700);
        self.moveTo((screen.width - 1000) / 2, (screen.height - 700) / 2);
    </script>
<?php endif; ?>

<script src="pepiscms/3rdparty/ajaxupload/ajaxupload.js?v=<?=PEPISCMS_VERSION?>"></script>
<script src="pepiscms/js/fileicon.js?v=<?=PEPISCMS_VERSION?>"></script>
<script src="pepiscms/js/ajaxfilemanager.js?v=<?=PEPISCMS_VERSION?>"></script>

<link href="pepiscms/3rdparty/jquery-ui/theme/smoothness/jquery-ui.custom.css" rel="stylesheet" type="text/css">
<script src="pepiscms/3rdparty/jquery-ui/jquery-ui.custom.min.js?v=<?=PEPISCMS_VERSION?>"></script>

	
<div id="whereami">
    <?php if (!$is_editor): ?>
        <img src="pepiscms/theme/img/ajaxfilemanager/ajaxfilemanager_32.png" alt="<?= $this->lang->line('filemanager_label') ?>" title="<?= $this->lang->line('filemanager_label') ?>">
        <h1><?= $this->lang->line('filemanager_label') ?></h1> 
    <?php endif; ?>
    <div class="path breadcrumbs"><?= $this->lang->line('filemanager_label_initializing') ?></div>
</div> 

<div id="disabled_javascript">
    <?= display_error($this->lang->line('filemanager_label_you_need_javascript_to_use_ajax_file_manager')) ?>
</div>

<div id="enabled_javascript" style="display: none">

    <div id="upload_file_progress" style="display: none;">
        <p><?= $this->lang->line('filemanager_label_uploading_file_this_can_take_up_to_several_minutes_please_dont_t_close_this_window') ?></p>
    </div>
    <div id="error_boxes" style="display: none;"></div>

    <form method="post" action="" id="filemanagerform">

        <div id="move_files_box" style="display: none;">

            <h3><?=$this->lang->line('filemanager_files_directories_to_move')?></h3>
            <div class="filelistContainer"></div>

            <div class="table_wrapper">
                <table class="datagrid" style="padding: 0px;">
                    <tr>
                        <td class="optionsname"><?= $this->lang->line('filemanager_label_new_location') ?></td>
                        <td>
                            <input type="text" name="move_files" id="move_files" value="" class="text">
                        </td>
                    </tr>
                </table>
                <script>
                    $(function() {
                        var source_url = '<?= admin_url() . 'ajaxfilemanager/getpathsjson' ?>';
                        $( "#move_files" ).autocomplete({
                            source: source_url
                        });
                    });
                </script>
            </div>
            <div class="buttons">
                <?= button_cancel('#', 'filemanager_cancelbutton') ?>
                <button name="move_files_button" value="<?= $this->lang->line('filemanager_button_move_files') ?>" id="move_files_commit_button" class="button"><img src="pepiscms/theme/img/ajaxfilemanager/folder_go_16.png" alt=""><?= $this->lang->line('filemanager_button_move_files') ?></button>
            </div>
        </div>
        <div id="delete_files_box" style="display: none;">
            <?= display_warning($this->lang->line('filemanager_dialog_ar_you_sure_to_delete_selected_files')) ?>

            <h3><?=$this->lang->line('filemanager_files_directories_to_delete')?></h3>
            <div class="filelistContainer"></div>
            <div class="buttons">
                <?= button_cancel('#', 'filemanager_cancelbutton') ?>
                <button value="<?= $this->lang->line('filemanager_button_delete_files') ?>" id="delete_files_commit_button" name="delete" class="button"><img src="pepiscms/theme/img/ajaxfilemanager/folder_delete_16.png" alt=""><?= $this->lang->line('global_button_delete') ?></button>
            </div>
        </div>		
        <div id="select_files_box" style="display: none;">
            <?= display_warning($this->lang->line('filemanager_dialog_select_files')) ?>
            <div class="buttons">
                <?= button_cancel('#', 'filemanager_cancelbutton') ?>
            </div>
        </div>

        <div id="filemanager">
            <?php if ($is_editor): ?><div class="no_matter_what_scrollable"><?php endif; ?>
            <div class="table_wrapper" style="margin-top: 0 !important;">
                <table id="filemanager_grid" class="datagrid">
                <thead>
                    <tr>
                    <th class="short"></th>
                    <th class="short_file_icon"></th>
                    <th class="longLong"><?= $this->lang->line('filemanager_cl_file_name') ?></th>
                    <th class="short"></th>
                    <th class="medium"><?= $this->lang->line('filemanager_cl_file_size') ?></th>
                    <!-- <th><?= $this->lang->line('filemanager_cl_file_created') ?></th>
                    <th><?= $this->lang->line('filemanager_cl_file_last_modified') ?></th> -->
                    </tr>
                </thead>
                <tbody></tbody>

                </table>
            </div>
            <?php if ($is_editor): ?></div><?php endif; ?>
            <div class="actionButtons">
                <button name="upload_file_button" value="<?= $this->lang->line('filemanager_button_upload_file') ?>" id="upload_file_button" class="button"><img src="pepiscms/theme/img/ajaxfilemanager/upload_16.png" alt=""><?= $this->lang->line('filemanager_button_upload_file') ?></button>
                <button name="delete_button" value="<?= $this->lang->line('filemanager_button_delete_files') ?>" id="delete_files_button" class="button"><img src="pepiscms/theme/img/ajaxfilemanager/folder_delete_16.png" alt=""><?= $this->lang->line('filemanager_button_delete_files') ?></button>
                <button name="move_files_button" value="<?= $this->lang->line('filemanager_button_move_files') ?>" id="move_files_button" class="button"><img src="pepiscms/theme/img/ajaxfilemanager/folder_go_16.png" alt=""><?= $this->lang->line('filemanager_button_move_files') ?></button>
                <button name="create_folder_button" value="<?= $this->lang->line('filemanager_button_create_folder') ?>" id="create_folder_button" class="button"><img src="pepiscms/theme/img/ajaxfilemanager/folder_add_16.png" alt=""><?= $this->lang->line('filemanager_button_create_folder') ?></button>
            </div>
        </div>

    </form>
</div>

<script>

	<?php foreach( $upload_allowed_types as &$ext) { $ext = '"'.$ext.'"'; } ?>
	var fmi = new FileManagerUI( {label_new_folder_name : "<?=$this->lang->line('filemanager_label_new_folder_name')?>", label_new_file_name_for : "<?=$this->lang->line('filemanager_label_new_file_name_for')?>", label_loading_list_of_files : "<?=$this->lang->line('filemanager_label_loading_list_of_files')?>"}, <?=$this->config->item('cms_intranet') ? 'true' : 'false' ?> );
	fmi.setAllowedExtensions( Array(<?=implode(', ', $upload_allowed_types);?>) );
	
	<?php if( $is_editor ): ?>
	<?php
		$pos = strpos( $_SERVER['REQUEST_URI'], '?' );
		$query_string = '';

		// Bypassing the GET parameters
		if ( $pos !== false )
		{
			$query_string = substr( $_SERVER['REQUEST_URI'], ++$pos );

			$pairs = explode( '&', $query_string );

			for ( $i = 0; $i < count( $pairs ); $i++ )
			{
				$param = explode( '=', $pairs[$i] );

				if ( isset( $param[1] ) )
				{
					$_GET[$param[0]] = urldecode( $param[1] );
				}
			}
			$query_string = '?' . $query_string;
		}
	?>	
	fmi.setBrowsePath('editorbrowse/<?=$query_string?>');
	fmi.onFileClick( function( url ) {

		if( window.opener.SetUrl )
		{
			window.opener.SetUrl( url ) ;
		}
		else if( window.opener.CKEDITOR )
		{
			var CKEditorFuncNum = <?=(isset($_GET['CKEditorFuncNum'])?$_GET['CKEditorFuncNum']:'NULL')?>;
			window.opener.CKEDITOR.tools.callFunction(CKEditorFuncNum, url);
		}
		window.close() ;
	});
	<?php endif; ?>



	$("body").ready( fmi.init );
</script>

<?php if( $is_editor ): ?>
	</div>
	</body>
	</html>
<?php endif; ?>
