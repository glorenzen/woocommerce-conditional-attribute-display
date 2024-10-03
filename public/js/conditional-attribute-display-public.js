jQuery(document).ready(function($) {
    function checkAttributes() {
        const localLogo = $('select[name="attribute_pa_' + cad_settings.local_logo_slug + '"]').val();
        if (localLogo === cad_settings.local_logo_value) {
            $('select[name="attribute_pa_' + cad_settings.location_slug + '"]').closest('tr').show();
            $('select[name="attribute_pa_' + cad_settings.location_slug + '"]').prop('disabled', false);
        } else {
            $('select[name="attribute_pa_' + cad_settings.location_slug + '"]').closest('tr').hide();
            $('select[name="attribute_pa_' + cad_settings.location_slug + '"]').prop('disabled', true);
        }
    }

    // Initial check
    checkAttributes();

    // Check on change
    $('select[name="attribute_pa_' + cad_settings.local_logo_slug + '"]').change(function() {
        checkAttributes();
    });

    // Prevent hidden attributes from being sent to the cart
    $('form.cart').submit(function(event) {
        let preventSubmit = false;

        $('select[name="attribute_pa_' + cad_settings.location_slug + '"]').each(function() {
            if ($(this).closest('tr').is(':hidden')) {
                $(this).prop('disabled', true);
            } else {
                $(this).prop('disabled', false);
                if ($(this).val() === 'none') {
                    preventSubmit = true;
                }
            }
        });

        if (preventSubmit) {
            event.preventDefault();
            alert('Please select a valid location.');
        }
    });
});