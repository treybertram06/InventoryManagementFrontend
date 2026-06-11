<?php

function get_uri() {
    return $_SERVER['REQUEST_URI'];
}
function uri_is($value) {
    return get_uri() == $value;
}

function println($in) {
    echo "$in <br>";
}

