<?php
$price_text = $giftwrapping_cost == 0
    ? 'Free'
    : wc_price($giftwrapping_cost);
?>

<p class="gift-wrapping" style="clear: both; padding: 1em 0;">
    <input type="checkbox" id="gift_wrap_item" name="gift_wrap_item" value="yes" <?php checked($is_checked, 1, true); ?>>
    <label for="gift_wrap_item">
        <?php echo $giftwrapping_message; ?> <span class="gift_wrap_cost"><?php echo "($price_text)"; ?></span>
    </label>
</p>