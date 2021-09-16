<?php

class LogsRowFormattingUtility
{
    public function _datagrid_row_callback($line)
    {
        if ($line->level == Logger::MESSAGE_LEVEL_INFO) {
            return DataGrid::ROW_COLOR_GREEN;
        }
        if ($line->level == Logger::MESSAGE_LEVEL_NOTICE) {
            return DataGrid::ROW_COLOR_YELLOW;
        }
        if ($line->level == Logger::MESSAGE_LEVEL_WARNING) {
            return DataGrid::ROW_COLOR_ORANGE;
        }
        if ($line->level == Logger::MESSAGE_LEVEL_ERROR) {
            return DataGrid::ROW_COLOR_RED;
        }

        return '';
    }
}