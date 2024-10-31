<?php
$locations = get_transient( 's2m_locations' );
if ( !is_object( $locations ) ) {
    $locations = s2m_get_locations();
    set_transient('s2m_locations', $locations, 86400);
}
$locationsArray = array();
if (is_object($locations)) {
	foreach($locations->Results as $location) :
    	$locationsArray[$location->Name] = $location;
	endforeach;
}
//resort list
ksort($locationsArray);
?>


<div class="inside">
    <div id="s2m-locations-select">
	    <p>
		Search: <input type="search" onkeyup="s2m_filter_locations(jQuery(this).val());"/>
	    </p>
		<?php if(!empty($locationsArray)) { ?>
	    <fieldset>
		    <legend class="screen-reader-text">Post Formats</legend>
		    <?php foreach($locationsArray as $title => $location) : ?>
			<div class="locationRow" data-title="<?php echo $title; ?>">
			    <input type="checkbox" name="s2m_location[]" class="s2m_location" id="s2m-location-<?php echo $location->Id; ?>" value="<?php echo $location->Id; ?>" data-name="<?php echo $location->Name; ?>"> 
				<label for="s2m-location-<?php echo $location->Id; ?>" class=""><?php echo $location->Name; ?></label>
			</div>
		    <?php endforeach; ?>
	    </fieldset>
		<?php } else { ?>
		<p>
			<strong>No locations found</strong>
		</p>
		<?php } ?>
	    <p>
		<input name="select-locations" type="submit" class="button button-primary button-large" id="select-locations" value="Select Location(s)" onclick="s2m_create_shortcode();">
	    </p>
    </div>
</div>
<style>
    #s2m-locations-select fieldset {
	height: 265px;
	overflow: auto;
    }

	#s2m-locations-select div.locationRow {
		padding: 3px 0;
	}
</style>
<script>
    setTimeout(function(){
	jQuery(document).find('#TB_window').width( TB_WIDTH ).height( TB_HEIGHT ).css( 'margin-left', - TB_WIDTH / 2 );
    }, 10);

    function s2m_filter_locations(inputVal) {
		inputVal = inputVal.toLowerCase();

		if (inputVal !== '') {
			var $items = $('div.locationRow', '#s2m-locations-select');
			for (var i = 0; i < $items.length; i++) {
				var locationName = $('label', $items[i]).text();
				locationName = locationName.toLowerCase();
				if (locationName.indexOf(inputVal) !== -1) {
					$($items[i]).show();
				}
				else {
					$($items[i]).hide();
				}
			}
		}
		else {
			$('div.locationRow', '#s2m-locations-select').show();
		}

		//jQuery('#s2m-locations-select fieldset span').hide();
		//jQuery("#s2m-locations-select label:contains('"+inputVal+"')").parent().show();
    }
    function s2m_create_shortcode() {
	//get all selected inputs
	$ = jQuery;
	var locations = [], locationNames = [];
	$.each(jQuery("#s2m-locations-select fieldset input:checked"), function(i, val) {
	    locations.push($(this).val());
	    locationNames.push($(this).data('name'));
	});
	//insert shortcode
	if (locations.length == 1) {
	    tinymce.execCommand("mceInsertContent", 0, '[s2m-widget location="'+locations[0]+'" name="'+locationNames[0]+'"]');
	} else {
	    tinymce.execCommand("mceInsertContent", 0, '[s2m-widget location="'+locations.join(',')+'" name=""]');
	}
	//close modal
	tb_remove();
    }
</script>