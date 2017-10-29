/**
 *
 * This is a component for wp menu nav control
 *
 * @author Savio Resende <savio@savioresende.com.br>
 *
 */

var wt_wp_nav = new Vue({
    el: '#nav',
    data: {},
    methods: {

        start: function(){

            // bind link tags on the menu
            $('#nav ul li a').on('click', function(e){
                e.preventDefault();

                var current_element = $(this);

                var slug = current_element.attr('href');
                slug = slug
                    .replace(bloginfo.url,'')
                    .replace(new RegExp('/', 'g'), '');

                window.location.hash = '/' + slug;

            });

        }

    }
});

wt_wp_nav.start();