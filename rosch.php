<?php
/*
Plugin Name: RouterOS Code Highlighting
Plugin URI: http://gregory-gost.ru/
Description: Подсветка кода для MikroTik RouterOS / Code highlighting for MikroTik RouterOS
Version: 1.0.6
Author: Gregory (Gost)
Author URI: http://gregory-gost.ru/
License: MIT

Copyright 2017 GregoryGost

Permission is hereby granted, free of charge, to any person obtaining a 
copy of this software and associated documentation files (the 
"Software"), to deal in the Software without restriction, including 
without limitation the rights to use, copy, modify, merge, publish, 
distribute, sublicense, and/or sell copies of the Software, and to 
permit persons to whom the Software is furnished to do so, subject to 
the following conditions: 

The above copyright notice and this permission notice shall be included 
in all copies or substantial portions of the Software. 

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS 
OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF 
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. 
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY 
CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, 
TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE 
SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

function roscode($atts, $content = null) {
	if (!$content) {
		return '';
	} else {
		// Определяем не пусты ли переменные
		if (get_option('input_menu') != "") {
			// Обрабатываем
			$inputTerm = trim($inputTerm);
			$inputTerm = preg_replace("/  +/"," ",$inputTerm); // Заменить все повторяющиеся пробелы на один
			$inputTerm = preg_quote(get_option('input_menu'));
			$inputTerm = str_replace(chr(32), chr(124), $inputTerm);
		} else {
			$inputTerm = 'system';
		}
		if (get_option('commands') != "") {
			// Обрабатываем
			$commandTerm = trim($commandTerm);
			$commandTerm = preg_replace("/  +/"," ",$commandTerm); // Заменить все повторяющиеся пробелы на один
			$commandTerm = preg_quote(get_option('commands'));
			$commandTerm = str_replace(chr(32), chr(124), $commandTerm);
		} else {
			$commandTerm = 'set';
		}
		if (get_option('specialcommands') != "") {
			// Обрабатываем
			$specCommandTerm = trim($specCommandTerm);
			$specCommandTerm = preg_replace("/  +/"," ",$specCommandTerm); // Заменить все повторяющиеся пробелы на один
			$specCommandTerm = preg_quote(get_option('specialcommands'));
			$specCommandTerm = str_replace(chr(32), chr(124), $specCommandTerm);
		} else {
			$specCommandTerm = 'detail';
		}
		if (get_option('parameters') != "") {
			// Обрабатываем
			$parameterTerm = trim($parameterTerm);
			$parameterTerm = preg_replace("/  +/"," ",$parameterTerm); // Заменить все повторяющиеся пробелы на один
			$parameterTerm = preg_quote(get_option('parameters'));
			$parameterTerm = str_replace(chr(32), chr(124), $parameterTerm);
		} else {
			$parameterTerm = 'default\-name';
		}
		if (get_option('symbols') != "") {
			// Обрабатываем
			$symbolTerm = trim($symbolTerm);
			$symbolTerm = preg_replace("/  +/"," ",$symbolTerm); // Заменить все повторяющиеся пробелы на один
			$symbolTerm = preg_quote(get_option('symbols'));
			$symbolTerm = str_replace(chr(32), chr(124), $symbolTerm);
		} else {
			$symbolTerm = '\[';
		}
		
		// Строки поиска
		$search = array(
			'/([\:])('.$commandTerm.')|[\/]('.$inputTerm.')[\s]|\b('.$inputTerm.')\b/ui', // Меню и под-меню
			'/\b('.$commandTerm.')\b/ui', // Команды
			'/\b('.$specCommandTerm.')\b/ui', // Специальные команды
			'/('.$parameterTerm.')[\=]/ui', // Параметры
			'/([\[|\]])|(\;)/si', // Символы
		);
		// Замена входного меню
		$replace = array(
			'<span class="color-menu">$0</span>', // Меню и под-меню
			'<span class="color-action">$0</span>', // Команды
			'<span class="color-param">$0</span>', // Специальные команды
			'<span class="color-param">$0</span>', // Параметры
			'<span class="color-symbol">$0</span>', // Символы
		);
		
		// Обрабатываем содержание ББ кода
		$content = str_replace("&#187;", chr(34), $content); // Кавычки при копировании
		$content = preg_replace($search, $replace, $content);
		return '<div class="roscode">'.do_shortcode($content, true).'</div>';
	}
}
add_shortcode( 'RC', 'roscode' );

// Регистрируем файл стилей в системе
function roscode_css(){
	wp_register_style( 'rosch', plugins_url( 'style.css', __FILE__ ), array(), '106', 'all' );
	wp_enqueue_style( 'rosch' );
}
add_action( 'wp_enqueue_scripts', 'roscode_css' );

// Добавление администраторского меню
$pageTitle = 'Подсветка синтаксиса терминала MikroTik RouterOS';
function add_rosch_menu() {
	global $pageTitle;
	$menuTitle = 'RoS Синтаксис';
	$capability = 'administrator';
	$menuSlug = __FILE__;
	$function = 'page_settings_generate';
	$iconUrl = plugins_url('icon.png', __FILE__);
	$position = 90;
	add_menu_page( $pageTitle, $menuTitle, $capability, $menuSlug, $function, $iconUrl, $position );
	add_action( 'admin_init', 'register_settings' );
}
// Регистрация настроек
function register_settings() {
	register_setting( 'rosch-settings-group', 'input_menu' );
	register_setting( 'rosch-settings-group', 'commands' );
	register_setting( 'rosch-settings-group', 'specialcommands' );
	register_setting( 'rosch-settings-group', 'parameters' );
	register_setting( 'rosch-settings-group', 'symbols' );
}
// Вывод страницы настроек
function page_settings_generate() {
	global $pageTitle;
	?>
	<div class="wrap">
		<h2><?php echo $pageTitle; ?></h2>
		<p>Все команды необходимо вводить через пробел</p>
		<form method="post" action="options.php">
			<?php settings_fields( 'rosch-settings-group' ); ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">Меню и под-меню<br /><span style="color:rgb(0,155,155);">Цвет в терминале</span></th>
					<td><textarea style="width:100%;min-height:150px;" name="input_menu"><?php echo get_option('input_menu'); ?></textarea></td>
				</tr>

				<tr valign="top">
					<th scope="row">Команды<br /><span style="color:rgb(155,0,155);">Цвет в терминале</span></th>
					<td><textarea style="width:100%;min-height:150px;" name="commands"><?php echo get_option('commands'); ?></textarea></td>
				</tr>
				
				<tr valign="top">
					<th scope="row">Специальные команды<br /><span style="color:rgb(0,155,0);">Цвет в терминале</span></th>
					<td><textarea style="width:100%;min-height:150px;" name="specialcommands"><?php echo get_option('specialcommands'); ?></textarea></td>
				</tr>

				<tr valign="top">
					<th scope="row">Параметры<br /><span style="color:rgb(0,155,0);">Цвет в терминале</span></th>
					<td><textarea style="width:100%;min-height:150px;" name="parameters"><?php echo get_option('parameters'); ?></textarea></td>
				</tr>
				
				<tr valign="top">
					<th scope="row">Символы<br /><span style="color:rgb(155,155,0);">Цвет в терминале</span></th>
					<td><textarea style="width:100%;min-height:150px;" name="symbols"><?php echo get_option('symbols'); ?></textarea></td>
				</tr>
			</table>

			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
	</div>
	<?php
}
add_action('admin_menu', 'add_rosch_menu');
?>