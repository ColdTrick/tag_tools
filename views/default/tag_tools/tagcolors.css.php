<?php

$hex2rgba = function($color, $opacity = false) {
 
	$default = 'rgb(0,0,0)';
 
	//Return default if no color provided
	if (empty($color)) {
          return $default;
	}
	
	//Sanitize $color if "#" is provided
	if ($color[0] == '#' ) {
		$color = substr( $color, 1 );
	}

	//Check if color has 6 or 3 characters and get values
	if (strlen($color) == 6) {
		$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
	} elseif ( strlen( $color ) == 3 ) {
		$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
	} else {
		return $default;
	}

	//Convert hexadec to rgb
	$rgb =  array_map('hexdec', $hex);

	//Check if opacity is set(rgba or rgb)
	if ($opacity){
		if (abs($opacity) > 1) {
			$opacity = 1.0;
		}
		$output = 'rgba('.implode(",",$rgb).','.$opacity.')';
	} else {
		$output = 'rgb('.implode(",",$rgb).')';
	}

	//Return rgb(a) color string
	return $output;
};

$tag_definitions = elgg_get_entities([
	'type' => 'object',
	'subtype' => \TagDefinition::SUBTYPE,
	'metadata_names' => ['bgcolor', 'textcolor'],
	'limit' => false,
	'batch' => true,
]);

$result = '';
foreach ($tag_definitions as $tag) {
	$name = elgg_get_friendly_title("tag-color-{$tag->title}");
	$result .= "> a.{$name} {";
	if ($tag->textcolor) {
		$color = $hex2rgba($tag->textcolor);
		$result .= "color: {$color};";
	}
	if ($tag->bgcolor) {
		$bordercolor = $hex2rgba($tag->bgcolor);
		$bgcolor = $hex2rgba($tag->bgcolor, 0.3);
		$result .= "border-color: {$bordercolor};";
		$result .= "background: {$bgcolor};";
	}
	$result .= '}';
}

if (empty($result)) {
	return;
}

?>
.elgg-tags {
	.elgg-tag {
		<?php echo $result; ?>
	}
}
	