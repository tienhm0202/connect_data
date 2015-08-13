<?php
/**
 * Created by PhpStorm.
 * User: Hoang Manh Tien
 * Date: 27/3/2015
 * Time: 4:34 PM
 */
function genFormCollection($id, $name, $onclick, $data_field, $identifier){
    $str = <<< EOF
<tr id={$id}>
    <td><i class="icon icon-remove" onclick="{$onclick}"></i></td>
    <td>{$name}</td>
    <input type="hidden" name="{$data_field}" value={$identifier} />
</tr>
EOF;

    return $str;
}