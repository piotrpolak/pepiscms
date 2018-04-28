<link href="<?=module_resources_url()?>sqlconsole.css" rel="stylesheet" type="text/css" />

<?php $is_utilities_only_module = FALSE; ?>
<?php if ($this->input->getParam('layout') != 'popup'): ?>
    <?php
    $breadcrumb_array = array(module_url() => $title);

    if( ModuleRunner::isModuleDisplayedInMenu() )
    {
        $parent_module_name = ModuleRunner::getParentModuleName();
        if( $parent_module_name )
        {
            $breadcrumb_array = array_merge(array(module_url($parent_module_name) => $this->Module_model->getModuleLabel($parent_module_name, $this->lang->getCurrentLanguage())), $breadcrumb_array);
        }
    }
    else
    {
        // If module is displayed in UTILITIES and not in MENU then display a back link
        if( ModuleRunner::isModuleDisplayedInUtilities($this->modulerunner->getRunningModuleName()) )
        {
            $is_utilities_only_module = TRUE;
            $breadcrumb_array = array_merge(array(admin_url() . 'utilities' => $this->lang->line('label_utilities_and_settings')), $breadcrumb_array);
        }
    }
    ?>

    <?= display_breadcrumb($breadcrumb_array, module_icon_url()) ?>
<?php endif; ?>

<?php
$actions = array();
if( $is_utilities_only_module )
{
    $actions[] = array(
        'name' => $this->lang->line('global_button_back_to_utilities'),
        'link' => admin_url() . 'utilities',
        'icon' => 'pepiscms/theme/img/dialog/actions/back_16.png', // 'pepiscms/theme/img/dialog/actions/action_16.png', 'pepiscms/theme/img/dialog/actions/add_16.png'
        //'class' => ($this->input->getParam('layout') == 'popup' ? 'popup' : ''),
    );
}
?>
<?php if(count($actions)): ?>
    <?= display_action_bar($actions) ?>
<?php endif; ?>

<?= display_session_message() ?>

<?php if( !$query_error && !$query_success ): ?>
<?=display_warning($this->lang->line('sqlconsole_index_tip'))?>
<?php endif; ?>

<?php if( $query_error ): ?>
<?=display_error($this->lang->line('sqlconsole_query_error').':<br><br> '.$query_error)?>
<?php endif;?>

<?php if( $query_success ): ?>
<?=display_success($this->lang->line('sqlconsole_query_success')) ?>
<?php endif;?>

	<form method="post" action="<?=module_url()?>" style="margin-top: 20px;">
		<textarea name="sql_input" id="sql_input" rows="10" cols="140" style="font-family:Courier New, Courier, monospace; width: 100%;"><?=htmlentities($sql_input, ENT_QUOTES | ENT_IGNORE, "UTF-8")?></textarea>

        <div class="buttons">
			<div class="lFloated">
				<label for="query_separator"><?=$this->lang->line('sqlconsole_query_separator')?>:</label> <input type="text" name="query_separator" id="query_separator" value="<?=$query_separator?>" class="text" style="width: 20px" />
			</div>
			<button name="submit" class="filter_clear" id="clear"><img alt="icon" src="pepiscms/theme/img/dialog/buttons/filter_clear_16.png"><?=$this->lang->line('sqlconsole_clear')?></button>
			<input type="submit" name="execute" value="<?=$this->lang->line('sqlconsole_execute')?>" class="button save">
		</div>
	</form>

	<?php if( count( $result ) ): ?>
	<div class="table_wrapper" id="resultset">
		<table class="datagrid">
			<tr>
			<?php foreach( $result[0] as $key => $dontcare ): ?>
				<th><?=$key?></th>
			<?php endforeach; ?>
			</tr>
			<?php foreach( $result as $line ): ?>
			<tr>
			    <?php foreach( $line as $item ): ?>
				<td><?=htmlentities($item, ENT_QUOTES | ENT_IGNORE, "UTF-8")?></td>
			    <?php endforeach; ?>
			</tr>
			<?php endforeach; ?>
		</table>
	</div>

	<?php elseif($sql_input): ?>
	<div id="resultset"><?=display_notification($this->lang->line('sqlconsole_no_results'))?></div>
	<?php endif;?>


<?php if( count( $tables ) ): ?>
    <div id="tables_definitions">
        <div id="table_names">
            <nav class="file_tree">
                <a><?=$database_name?></a>
                <ul class="tables">
                    <?php foreach( $tables as $table => $fields ): ?>
                    <li class="has_items"><a href="#" title="<?=$table?>" class="table"><?=str_replace(' ', '_', character_limiter(str_replace('_', ' ', $table), 25, '...'))?></a>
                        <ul class="fields">
                            <?php foreach( $fields as $field => $field_definition ): ?>
                            <li><a href="#" title="<?=$table.'.'.$field?>" class="sql"><?=$field?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </div><!-- END table_names -->

        <div id="table_utilities">
            <div id="table_queries">
                <?php foreach( $tables as $table => $fields ): ?>
                <div class="table_queries" id="table_queries_<?=$table?>">
                    <h4><?=$this->lang->line('sqlconsole_query')?> <b><?=$table?></b></h4>
                    <ul>
                        <?php $keys = '('.implode(', ', array_keys($fields)).')'; ?>
                        <?php $values = '('.implode(', ', array_fill(0, count($keys), "'?'")).')'; ?>

                        <li><a href="#" title="SELECT * FROM <?=$table?>" class="sql replace focus">SELECT * FROM <?=$table?></a></li>
                        <li><a href="#" title="INSERT INTO <?=$table?> <?=$keys?> VALUES <?=$values?>" class="sql replace focus">INSERT INTO <?=$table?></a></li>
                        <li><a href="#" title="DELETE FROM <?=$table?>" class="sql replace focus">DELETE FROM <?=$table?></a></li>
                        <li><a href="#" title=" <?=$table?> " class="sql"><?=$table?></a></li>
                        <li><a href="#" title="WHERE " class="sql nl">WHERE</a></li>
                        <li><a href="#" title="ORDER BY " class="sql nl">ORDER BY</a></li>
                        <li><a href="#" title=" DESC " class="sql"> DESC </a></li>
                        <li><a href="#" title=" = ''" class="sql"> = (EQUALS) </a></li>
                        <li><a href="#" title=" > 0" class="sql"> > (GREATER) </a></li>
                        <li><a href="#" title=" < 0 " class="sql"> < (LESS) </a></li>
                        <li><a href="#" title=" LIKE '%%' " class="sql"> LIKE </a></li>
                    </ul>
                </div>
            <?php endforeach; ?>
            </div>
        </div><!-- END table_queries -->

        <?php if( count($query_history)): ?>
            <div id="query_history">
                <h4><?=$this->lang->line('sqlconsole_query_history')?></h4>
                <ul>
                    <?php foreach($query_history as $query ): ?>
                        <li><a href="#" title="<?=htmlentities($query, ENT_QUOTES | ENT_IGNORE, "UTF-8")?>" class="sql replace focus"><?=htmlentities(word_limiter($query, 10, '...'), ENT_QUOTES | ENT_IGNORE, "UTF-8")?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

    </div>
<?php endif;?>

<script type="text/javascript" src="<?=module_resources_url()?>sqlconsole.js?v=<?= PEPISCMS_VERSION ?>"></script>