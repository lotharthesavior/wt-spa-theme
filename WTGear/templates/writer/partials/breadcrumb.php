<?php
/**
 * Words Tree Writer Shortcode Breadcrumb
 *
 * @author Savio Resende <savio@savioresende.com.br>
 */

global $breadcrumb;

?>

<div class="wt-books-breadcrumb">
    <ul>
        <?php foreach ($breadcrumb as $breadcrumb_item) { ?>
            <li>
                <?php if( !empty($breadcrumb_item['link']) ){ ?>
                    <a href="<?php echo $breadcrumb_item['link']; ?>"><?php echo $breadcrumb_item['value']; ?></a>
                <?php } else { ?>
                    <?php echo $breadcrumb_item['value']; ?>
                <?php } ?>
            </li>
        <?php } ?>
    </ul>
    <div class="cleaner"></div>
</div>