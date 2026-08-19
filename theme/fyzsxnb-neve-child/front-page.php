<?php
/**
 * Homepage controller (EN front page).
 *
 * UI V2 0.3.5: homepage presentation is controlled by front-page.php +
 * template-parts/home/* + inc/home.php config. Do not restore the legacy
 * page Custom HTML body — it is retired and archived.
 *
 * @package FYZSXNB_Neve_Child
 */

$locale = fyzsxnb_home_locale();
fyzsxnb_render_homepage( $locale );