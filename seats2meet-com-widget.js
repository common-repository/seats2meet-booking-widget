(function() {
    tinymce.create("tinymce.plugins.s2m_widget_plugin", {

        //url argument holds the absolute url of our plugin directory
        init : function(ed, url) {

            //add new button
            ed.addButton("s2m_widget_button", {
                title : "Seats2meet.com Widget",
                cmd : "s2m_widget_embed",
                image : "https://seats2meet.com/Content/Frontend/Style/Templates/S2M/Images/logo.png"
            });

            //button functionality.
            ed.addCommand("s2m_widget_embed", function() {
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		tb_show('Select Location(s)', ajaxurl + '?action=s2m_locations' );
            });

        },

        createControl : function(n, cm) {
            return null;
        },

        getInfo : function() {
            return {
                longname : "Seats2meet.com Widget",
                author : "Seats2meet.com",
                version : "1"
            };
        }
    });

    tinymce.PluginManager.add("s2m_widget_plugin", tinymce.plugins.s2m_widget_plugin);
})();