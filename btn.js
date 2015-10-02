(function() {
    //alert(putanjajs.putanja);
    var host = window.location.pathname.split( '/' );
    //alert(window.location.protocol+'//'+window.location.hostname+'/'+host[1]);
    tinymce.create('tinymce.plugins.btns', {
        init : function(ed) {

            ed.addButton('sm_share', {
                title : 'sm_share',
                image : window.location.protocol+"//"+window.location.hostname+"/"+host[1]+'/wp-content/plugins/social_media_share/includes/images.png',
                onclick: function() {
                    var width = jQuery(window).width(), H = jQuery(window).height(), W = (720 < width) ? 720 : width;
                    W = W - 80;
                    H = H - 84;
                    tb_show('Share post', '#TB_inline?width=' + W + '&height=' + H + '&inlineId=post-form');

                }
            });




        },

        createControl: function(n, cm) {
            return null;
        }
    });
    tinymce.PluginManager.add('post', tinymce.plugins.post);
    jQuery(function() {
        var form = jQuery('<div id="post-form"><table id="post-table" class="form-table">\
        <tr>\
            <th><label for="post-netw">Drustvena mreza:</label></th>\
            <td><select name="post-netw" id="post-netw">\
                    <option value="Twitter">Twitter</option>\
                    <option value="Instagram">Instagram</option>\
                    <option value="Facebook">Facebook</option>\
                </select><br />\
                <small>Izaberite sa koje drustvene mreze postavljate post.</small></td>\
        </tr>\
        <tr>\
            <th><label for="post-link">Unesite ID posta.</label></th>\
            <td><textarea name="post-link" id="post-link" style="width:100%;height:100px;"></textarea><br />\
                <small>Napomena: Za postove sa fejsbuka morate uneti celu URL adresu posta!</small></td>\
        </tr>\
    </table>\
    <p class="submit">\
        <input type="button" id="post-submit" class="button-primary" value="Insert post" name="submit" />\
    </p>\
</div>');

        var table = form.find('table');
        form.appendTo('body').hide();

        form.find('#post-submit').click(function() {

            var soc_net = jQuery('#post-netw').val();
            var content = jQuery('#post-link').val();
            var shortcode='';
            switch(soc_net) {
                case 'Twitter':
                    shortcode='[twitter tweet_id="'+content+'"]';
                    tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
                    break;
                case 'Instagram':
                    shortcode='[instagram inst_id="'+content+'"]';
                    tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
                    break;
                case 'Facebook':
                    shortcode='[facebook url="'+content+'"]';
                    tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
                    break;
                default : break;
            }

            tb_remove();
        });




    });

    tinymce.PluginManager.add( 'btns', tinymce.plugins.btns );
})();



