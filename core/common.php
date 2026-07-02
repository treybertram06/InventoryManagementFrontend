<?php

// If this gets too large, it can be split up into more specific groups
function get_uri() {
    return $_SERVER['REQUEST_URI'];
}
function uri_is($value) {
    return get_uri() == $value;
}

function println($in) {
    echo "$in <br>";
}

function console_log($value) {
    echo '<script>console.log(' . json_encode($value) . ');</script>';
}

function log_info($value) {
    file_put_contents('php://stdout', "[INFO] $value" . PHP_EOL);
}

function log_error($value) {
    error_log("[ERROR] $value");
}
function draw_svg($svg, $width, $height = 0) {
    $height = $height ?: $width;
    echo str_replace('<svg', '<svg class="h-'. $height . ' w-' . $width . '"', $svg);
}

