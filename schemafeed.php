<?php

/*
Plugin Name: SchemaFeed
Plugin URI: http://wordpress.org/extend/plugins/schemafeed/
Description: Promote and enhance your data and SEO strategy using the schema.org format.
Version: 0.6
Author: Kai Chan, Marcus Moxon
Author URI: http://schemafeed.com
*/

/*  
	Copyright 2011  Kai Chan  (email : kaichan1@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// initilize plugin

define( 'SCHEMAFEED', plugin_dir_path( __FILE__ ) );

require_once SCHEMAFEED.'start.php';

?>