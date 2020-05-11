<?php
if (!defined('APP_NAME')) {
    trigger_error('APP_NAME is not defined in: ' . __FILE__ . '.', E_USER_ERROR);
}

require APP_PATH . '/lib/3rdparty/Securimage/Securimage.php';

$fonts = array(
    'Vera.ttf',
    'VeraBd.ttf',
    'VeraSe.ttf',
    'VeraBI.ttf',
    'VeraIt.ttf',
    'VeraMoBI.ttf',
    'VeraMoIt.ttf',
    'VeraMoBd.ttf',
    'VeraMono.ttf',
    'VeraSeBd.ttf'
);

$k = array_rand($fonts, 1);
$font = $fonts[$k];

$img = new Securimage();

$img->arc_linethrough = FALSE;
$img->bgimg = APP_PATH . '/share/pixmaps/captcha/captcha-' . rand(1, 22) . '.jpg';
$img->code_length = mt_rand(5,7);
$img->draw_lines = FALSE;
$img->font_size = 22;
$img->image_bg_color = '#ff00ff';
$img->image_width = 250;
$img->image_height = 50;
$img->text_minimum_distance = 33;
$img->text_maximum_distance = 36;
$img->text_x_start = 10;
$img->ttf_file = APP_PATH . '/share/fonts/' . $font;

$img->text_color = '#000000';
$img->use_multi_text = TRUE;

$img->show();
