<?php
function loadView($view, $data = null)
{
    $CI = get_instance();
    return $CI->load->view($view, $data);
}