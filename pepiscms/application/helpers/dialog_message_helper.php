<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * PepisCMS
 *
 * Simple content management system
 *
 * @package             PepisCMS
 * @author              Piotr Polak
 * @copyright           Copyright (c) 2007-2018, Piotr Polak
 * @license             See license.txt
 * @link                http://www.polak.ro/
 */

if (!function_exists('display_error')) {

    /**
     * Displays error box
     *
     * @param string $message
     * @return string
     */
    function display_error($message)
    {
        $o = '<div class="dialog_box error"><img src="pepiscms/theme/img/dialog/messages/error_32.png" alt="" />' . "\n";
        $o .= '<p>' . $message . '</p>' . "\n";
        $o .= '</div>' . "\n";
        $o .= '<div style="clear: both"></div>' . "\n";

        return $o;
    }

}

if (!function_exists('display_warning')) {

    /**
     * Displays warning box
     *
     * @param string $message
     * @return string
     */
    function display_warning($message)
    {
        $o = '<div class="dialog_box warning"><img src="pepiscms/theme/img/dialog/messages/warning_32.png" alt="" />' . "\n";
        $o .= '<p>' . $message . '</p>' . "\n";
        $o .= '</div>' . "\n";
        $o .= '<div style="clear: both"></div>' . "\n";

        return $o;
    }

}

if (!function_exists('display_notification')) {

    /**
     * Displays notification box
     *
     * @param string $message
     * @return string
     */
    function display_notification($message)
    {
        $o = '<div class="dialog_box notification"><img src="pepiscms/theme/img/dialog/messages/notification_32.png" alt="" />' . "\n";
        $o .= '<p>' . $message . '</p>' . "\n";
        $o .= '</div>' . "\n";
        $o .= '<div style="clear: both"></div>' . "\n";

        return $o;
    }

}

if (!function_exists('get_warning_begin')) {

    /**
     * Returns warning begin html code
     *
     * @return string
     */
    function get_warning_begin()
    {
        return '<div class="dialog_box warning"><img src="pepiscms/theme/img/dialog/messages/warning_32.png" alt="" />' . "\n" . '<p>';
    }

}

if (!function_exists('get_warning_end')) {

    /**
     * Returns warning end html code
     *
     * @return string
     */
    function get_warning_end()
    {
        return '</p>' . "\n" . '</div>' . "\n" . '<div style="clear: both"></div>';
    }

}

if (!function_exists('display_success')) {

    /**
     * Displays success box
     *
     * @param string $message
     * @return string
     */
    function display_success($message)
    {
        $o = '<div class="dialog_box success"><img src="pepiscms/theme/img/dialog/messages/success_32.png" alt="" />' . "\n";
        $o .= '<p>' . $message . '</p>' . "\n";
        $o .= '</div>' . "\n";
        //$o .= '<div style="clear: both"></div>'."\n";

        return $o;
    }

}

if (!function_exists('display_tip')) {

    /**
     * Displays tip box
     *
     * @param string $message
     * @return string
     */
    function display_tip($message)
    {
        $o = '<div class="dialog_box tip"><img src="pepiscms/theme/img/dialog/messages/tip_32.png" alt="" />' . "\n";
        $o .= '<p>' . $message . '</p>' . "\n";
        $o .= '</div>' . "\n";
        //$o .= '<div style="clear: both"></div>'."\n";

        return $o;
    }

}

if (!function_exists('display_breadcrumb')) {

    /**
     * Renders breadcrums HTML element
     *
     * @param array $breadcrumb_array
     * @param bool $icon
     * @return string
     */
    function display_breadcrumb($breadcrumb_array, $icon = FALSE)
    {
        $count = count($breadcrumb_array);

        $o = '';
        $o .= '<div class="breadcrumbs">' . "\n";
        $o .= '		<a href="' . admin_url() . '">' . CI_Controller::get_instance()->lang->line('global_breadcrumb_start') . '</a>' . "\n";

        $i = 1;
        foreach ($breadcrumb_array as $url => $label) {
            if ($i != $count) {
                $o .= '		<a href="' . $url . '">' . $label . '</a>' . "\n";
            } else {
                $o .= '		<a href="' . $url . '" class="active">' . $label . '</a>' . "\n";
            }

            $i++;
        }
        $o .= '</div>' . "\n";

        if ($icon) {
            $icon = '<img src="' . $icon . '" alt="' . $label . '">';
        }


        $html = '<div id="whereami">' . "\n";
        $html .= $icon . "\n";
        $html .= '<h1>' . $label . '</h1>' . "\n";
        $html .= $o . "\n";
        $html .= '</div>' . "\n";

        return $html;
    }

}

if (!function_exists('display_session_message')) {

    /**
     * Renders and prints session flash message
     *
     * @return string
     */
    function display_session_message()
    {
        if (!isset(CI_Controller::get_instance()->simplesessionmessage)) {
            CI_Controller::get_instance()->load->library('SimpleSessionMessage');
        }

        if (($message = CI_Controller::get_instance()->simplesessionmessage->getLocalizedMessage())) {
            return $message;
        }

        return '';
    }

}

if (!function_exists('display_steps')) {

    /**
     * Displays steps breadcrumbs
     *
     * @param string $steps
     * @param string $type dot_steps|circle_steps
     * @return string
     */
    function display_steps($steps, $type)
    {
        $html = '';
        $html .= '<nav class="steps ' . $type . '">' . "\n";
        $i = 0;

        $c_steps = count($steps);


        foreach ($steps as $step) {
            $css_class = '';

            ++$i;
            if ($i == $c_steps) {
                $css_class = 'finish';
            }

            if (isset($step['active']) && $step['active']) {
                $was_active = TRUE;
                $css_class .= ' active';
            }

            if (!isset($step['description'])) {
                $step['description'] = '';
            }


            $html .= '<li class="' . $css_class . '">' . "\n";
            $html .= '<span class="counter">' . $i . '</span>' . "\n";
            $html .= '<div>' . "\n";
            $html .= '<h5 title="' . $step['description'] . '">' . $step['name'] . '</h5>' . "\n";
            if ($step['description']) {
                $html .= '<p>' . $step['description'] . '</p>' . "\n";
            }
            $html .= '</div>' . "\n";
            $html .= '</li>' . "\n";
        }
        $html .= '</nav>' . "\n";

        return $html;
    }

}

if (!function_exists('display_steps_circle')) {

    /**
     * Displays steps breadcrumbs (circle)
     *
     * @param array $steps
     * @return string
     */
    function display_steps_circle($steps)
    {
        return display_steps($steps, 'circle_steps');
    }

}

if (!function_exists('display_steps_dot')) {

    /**
     * Displays steps breadcrumbs (dot)
     *
     * @param $steps
     * @return string
     */
    function display_steps_dot($steps)
    {
        return display_steps($steps, 'dot_steps');
    }

}

if (!function_exists('display_action_bar')) {

    /**
     * Allowed keys: title, name, icon, link
     *
     * @param array $actions
     * @return boolean|string
     */
    function display_action_bar($actions)
    {

        if (count($actions) == 0) {
            return FALSE;
        }

        $html = '<div class="action_bar">' . "\n";

        foreach ($actions as $action) {
            if (isset($action['icon']) && $action['icon']) {
                $html .= '<a href="' . $action['link'] . '" class="hasIcon' . (isset($action['class']) ? ' ' . $action['class'] : '') . '"' . (isset($action['title']) ? ' title="' . $action['title'] . '"' : '') . '><img src="' . $action['icon'] . '" alt="">' . $action['name'] . '</a>' . "\n";
            } else {
                $html .= '<a href="' . $action['link'] . '" class="hasIcon' . (isset($action['class']) ? ' ' . $action['class'] : '') . '"' . (isset($action['title']) ? ' title="' . $action['title'] . '"' : '') . '>' . $action['name'] . '</a>' . "\n";
            }
        }
        $html .= '</div>' . "\n";

        return $html;
    }

}

if (!function_exists('button_generic')) {

    /**
     * Helper that generates HTML button for generic (to be customized) action
     *
     * @param string $label
     * @param string $url
     * @param string $icon_path
     * @param string $extra_classes
     * @param string|bool $id
     * @return string
     */
    function button_generic($label, $url, $icon_path, $extra_classes = '', $id = FALSE)
    {
        return '<a href="' . $url . '" class="button ' . $extra_classes . '"' . ($id ? ' id="' . $id . '"' : '') . '><img src="' . $icon_path . '" alt="">' . $label . '</a>';
    }

}

if (!function_exists('button_cancel')) {

    /**
     * Helper that generates HTML button for cancel action
     *
     * @param string $url
     * @param string $extra_classes
     * @param string|bool $id
     * @param string|bool $label
     * @return string
     */
    function button_cancel($url = '#', $extra_classes = '', $id = FALSE, $label = FALSE)
    {
        $label = $label ? $label : CI_Controller::get_instance()->lang->line('global_button_cancel');
        return '<a href="' . $url . '" class="button cancel ' . $extra_classes . '"' . ($id ? ' id="' . $id . '"' : '') . '><img src="pepiscms/theme/img/dialog/buttons/cancel_16.png" alt="">' . $label . '</a>';
    }

}

if (!function_exists('button_back')) {

    /**
     * Helper that generates HTML button for back action
     *
     * @param string $url
     * @param string $extra_classes
     * @param string|bool $id
     * @param string|bool $label
     * @return string
     */
    function button_back($url = '#', $extra_classes = '', $id = FALSE, $label = FALSE)
    {
        $label = $label ? $label : CI_Controller::get_instance()->lang->line('global_button_back');
        return '<a href="' . $url . '" class="button back ' . $extra_classes . '"' . ($id ? ' id="' . $id . '"' : '') . '><img src="pepiscms/theme/img/dialog/buttons/back_16.png" alt="">' . $label . '</a>';
    }

}

if (!function_exists('button_next')) {

    /**
     * Helper that generates HTML button for next action
     *
     * @param string $url
     * @param string $extra_classes
     * @param string|bool $id
     * @param string|bool $label
     * @return string
     */
    function button_next($url = '#', $extra_classes = '', $id = FALSE, $label = FALSE)
    {
        $label = $label ? $label : CI_Controller::get_instance()->lang->line('global_button_next');
        return '<a href="' . $url . '" class="button next ' . $extra_classes . '"' . ($id ? ' id="' . $id . '"' : '') . '><img src="pepiscms/theme/img/dialog/buttons/next_16.png" alt="">' . $label . '</a>';
    }

}

if (!function_exists('button_previous')) {

    /**
     * Helper that generates HTML button for previous action
     *
     * @param string $url
     * @param string $extra_classes
     * @param string|bool $id
     * @param string|bool $label
     * @return string
     */
    function button_previous($url = '#', $extra_classes = '', $id = FALSE, $label = FALSE)
    {
        $label = $label ? $label : CI_Controller::get_instance()->lang->line('global_button_previous');
        return '<a href="' . $url . '" class="button previous ' . $extra_classes . '"' . ($id ? ' id="' . $id . '"' : '') . '><img src="pepiscms/theme/img/dialog/buttons/previous_16.png" alt="">' . $label . '</a>';
    }

}

if (!function_exists('button_apply')) {

    /**
     * Helper that generates HTML button for apply action
     *
     * @param string $extra_classes
     * @param string|bool $id
     * @param string|bool $label
     * @return string
     */
    function button_apply($extra_classes = '', $id = FALSE, $label = FALSE)
    {
        $label = $label ? $label : CI_Controller::get_instance()->lang->line('global_button_apply');
        return '<input type="submit" name="apply" value="' . $label . '" class="apply button ' . $extra_classes . '"' . ($id ? ' id="' . $id . '"' : '') . '>';
    }

}

if (!function_exists('button_save')) {

    /**
     * Helper that generates HTML button for save action
     *
     * @param string $extra_classes
     * @param string|bool $id
     * @param string|bool $label
     * @return string
     */
    function button_save($extra_classes = '', $id = FALSE, $label = FALSE)
    {
        $label = $label ? $label : CI_Controller::get_instance()->lang->line('global_button_save');
        return '<input type="submit" name="save" value="' . $label . '" class="save button ' . $extra_classes . '"' . ($id ? ' id="' . $id . '"' : '') . '>';
    }

}

if (!function_exists('button_print')) {

    /**
     * Helper that generates HTML button for print action
     *
     * @param string $printUrl
     * @param string|bool $label
     * @param string|bool $labelDownload
     * @param string|bool $css_class
     * @return string
     */
    function button_print($printUrl, $label = FALSE, $labelDownload = FALSE, $css_class = FALSE)
    {

        if (!$label) {
            $label = CI_Controller::get_instance()->lang->line('global_button_print');
        }
        if (!$labelDownload) {
            $labelDownload = CI_Controller::get_instance()->lang->line('global_button_download_as_pdf');
        }
        if (!$css_class) {
            $css_class = 'doPrint';
        }

        return '<a class="button popup noappend" href="' . $printUrl . '/print-1" title="' . $label . '"><img src="pepiscms/theme/img/dialog/buttons/print_16.png" alt="">' . $label . '</a> <a class="button" href="' . $printUrl . '" title="' . $labelDownload . '" target="_blank"><img src="pepiscms/theme/img/dialog/buttons/print_pdf_16.png" alt="">' . $labelDownload . '</a>';
    }

}

if (!function_exists('dashboard_box')) {

    /**
     * Displays dashboard box
     *
     * @param string $name
     * @param string $url
     * @param bool $icon_path
     * @param bool $description
     * @param bool $is_popup
     * @param bool $target
     * @return string
     */
    function dashboard_box($name, $url, $icon_path = FALSE, $description = FALSE, $is_popup = FALSE, $target = FALSE)
    {
        if (!$icon_path) {
            $icon_path = 'pepiscms/theme/module_32.png';
        }

        $html = '<li>
			<a href="' . $url . '"' . ($is_popup ? ' class="popup"' : '') . ($description ? ' title="' . $description . '"' : '') . ($target ? ' target="' . $target . '"' : '') . '>
				<span><img src="' . $icon_path . '" alt="" /></span>
				' . $name . '
			</a>
		</li>';

        return $html;
    }

}

if (!function_exists('display_confirmation_box')) {

    /**
     * Displays confirmation box
     *
     * @param string $message
     * @param string $explanation
     * @return string
     */
    function display_confirmation_box($message, $explanation = '')
    {
        $template_absolute_path = APPPATH . 'views/templates/dialog_confirmation_box.php';
        return get_instance()->load->theme($template_absolute_path, array('message' => $message, 'explanation' => $explanation));
    }

}

if (!function_exists('display_error_box')) {

    /**
     * Displays error box
     *
     * @param string $message
     * @param string $explanation
     * @return string
     */
    function display_error_box($message, $explanation = '')
    {
        $template_absolute_path = APPPATH . 'views/templates/dialog_error_box.php';
        return get_instance()->load->theme($template_absolute_path, array('message' => $message, 'explanation' => $explanation));
    }

}