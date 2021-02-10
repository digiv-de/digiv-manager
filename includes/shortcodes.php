<?php

add_shortcode('user_meta', 'digiv_userMeta_shortcode');

/**
 * Handler for [user_meta key=""]
 * @param $atts array Shortcode Attribute.
 * @return string
 */
function digiv_userMeta_shortcode($atts): string
{
    $userId = get_current_user_id();
    if ($userId === 0) {
        return '';
    }

    return get_user_meta($userId, $atts['key'], true);
}
