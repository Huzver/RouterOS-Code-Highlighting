<?php
/*
Plugin Name: RouterOS Code Highlighting
Plugin URI: http://gregory-gost.ru/
Description: Подсветка кода для MikroTik RouterOS / Code highlighting for MikroTik RouterOS
Version: 1.0.4
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
		// Строки поиска
		$search = array(
			'/(\/system|\/interface|wireless)[\ ]|(\:)/si', // Входное меню
			'/\b(add|set|get|find|put|print)\b/si', // Действия
			'/(default\-name|name|mtu|l2mtu|mac-address|arp|interface\-type|mode|ssid|frequency|band|channel\-width|scan-list|wireless\-protocol|vlan\-mode|vlan\-id|wds\-mode|wds\-default\-bridge|wds\-ignore\-ssid|bridge\-mode|default\-authentication|default\-forwarding|default\-ap\-tx\-limit|default\-client\-tx\-limit|hide\-ssid|security\-profile|compression)[\=]|(detail)/si', // Параметры
			'/([\[|\]])|(\;)/si', // Символы
		);
		// Замена входного меню
		$replace = array(
			'<span class="color-menu">\\0</span>', // Входное меню
			'<span class="color-action">\\0</span>', // Действия
			'<span class="color-param">\\0</span>', // Параметры
			'<span class="color-symbol">\\0</span>', // Символы
		);
		
		// Обрабатываем содержание ББ кода
		$content = str_replace("&#187;", chr(34), $content);
		$content = preg_replace($search, $replace, $content);
		return '<div class="roscode">'.do_shortcode($content, true).'</div>';
	}
}
add_shortcode( 'RC', 'roscode' );

function roscode_css(){
	// Регистрируем файл стилей в системе
	wp_register_style( 'rosch', plugins_url( 'style.css', __FILE__ ), array(), '104', 'all' );
	wp_enqueue_style( 'rosch' );
}
add_action( 'wp_enqueue_scripts', 'roscode_css' );
?>