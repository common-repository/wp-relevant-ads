(function() {
    tinymce.PluginManager.add( 'wp_relevant_ads_shortcode', function( editor, url ) {
        editor.addButton( 'wp_relevant_ads_shortcode', {
            title : '(Relevant Ads) Insert Shortcode',
            icon: 'icon icon-billboard',
            onclick: function() {
                editor.selection.setContent('[wp_relevant_ads id="" category="" class="" css=""]');
            }
        });
    });
})();