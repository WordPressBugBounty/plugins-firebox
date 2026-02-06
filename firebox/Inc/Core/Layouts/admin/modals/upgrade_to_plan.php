<?php
/**
 * @package         FirePlugins Framework
 * @version         1.1.142
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2025 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}
$allowed_tags = [
	'a' => [ 'href' => true, 'target' => true ],
	'i' => [ 'class' => true ],
	'em' => [ 'class' => true ],
	'b' => true,
	'strong' => [ 'class' => true ],
    'span' => [ 'class' => true ]
];

$preSalesUrl = esc_url(\FPFramework\Base\Functions::getUTMURL(FPF_SUPPORT_URL . '?topic=Pre-sale Question', '', 'misc', 'contact'));
$upgradeProUrl = esc_url(\FPFramework\Base\Functions::getUTMURL(FPF_SITE_URL . 'docs/start/upgrade/', '', 'misc', 'upgrade-to-pro'));
?>
<div class="pro-only-body text-center">
    <div class="top-lock-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 64 64" aria-hidden="true">
            <!-- Group 1: Body (slightly larger) -->
            <g fill="currentColor">
                <rect x="17" y="29" rx="8" ry="8" width="30" height="26"/>
                <!-- Keyhole silhouette: circle + stem as a single path -->
                <path d="M32 37a3 3 0 1 1 0 6a3 3 0 1 1 0-6 M30.8 43h2.4v6h-2.4z" fill="#ffffff" opacity="0.95"/>
            </g>

            <!-- Group 2: Shackle (shorter) with transparent bounding box for hinge normalization -->
            <g fill="none">
                <rect x="22" y="20" width="20" height="9" fill="none"/>
                <path d="M22 29 V22 a10 10 0 0 1 20 0 V29" stroke="currentColor" stroke-width="5" stroke-linecap="round" vector-effect="non-scaling-stroke"/>
            </g>
        </svg>
    </div>

    <!-- This is shown when we click on a Pro only feature button -->
    <div class="po-feature">
        <h2><?php echo wp_kses(fpframework()->_('FPF_PRO_MODAL_IS_PRO_PLAN_FEATURE'), $allowed_tags); ?></h2>
        <p><?php echo wp_kses(fpframework()->_('FPF_PRO_MODAL_WERE_SORRY_PLAN'), $allowed_tags); ?></p>
    </div>

    <p class="cta">
        <a class="fpf-button upgrade large" data-href="<?php echo esc_url(FBOX_GO_PRO_URL); ?>" href="#" target="_blank">
            <svg class="fpf-upgrade-icon lock-closed" xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#e8eaed"><path d="M263.72-96Q234-96 213-117.15T192-168v-384q0-29.7 21.15-50.85Q234.3-624 264-624h24v-96q0-79.68 56.23-135.84 56.22-56.16 136-56.16Q560-912 616-855.84q56 56.16 56 135.84v96h24q29.7 0 50.85 21.15Q768-581.7 768-552v384q0 29.7-21.16 50.85Q725.68-96 695.96-96H263.72Zm.28-72h432v-384H264v384Zm216.21-120Q510-288 531-309.21t21-51Q552-390 530.79-411t-51-21Q450-432 429-410.79t-21 51Q408-330 429.21-309t51 21ZM360-624h240v-96q0-50-35-85t-85-35q-50 0-85 35t-35 85v96Zm-96 456v-384 384Z"/></svg>
            <svg class="fpf-upgrade-icon lock-open" xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#e8eaed"><path d="M264-624h336v-96q0-50-35-85t-85-35q-50 0-85 35t-35 85h-72q0-80 56.23-136 56.22-56 136-56Q560-912 616-855.84q56 56.16 56 135.84v96h24q29.7 0 50.85 21.15Q768-581.7 768-552v384q0 29.7-21.16 50.85Q725.68-96 695.96-96H263.72Q234-96 213-117.15T192-168v-384q0-29.7 21.15-50.85Q234.3-624 264-624Zm0 456h432v-384H264v384Zm216.21-120Q510-288 531-309.21t21-51Q552-390 530.79-411t-51-21Q450-432 429-410.79t-21 51Q408-330 429.21-309t51 21ZM264-168v-384 384Z"/></svg>
            <span><?php echo esc_html(fpframework()->_('FPF_UPGRADE_TO_PRO')); ?></span>
        </a>
    </p>
    <div class="pro-only-bonus"></div>

    <div class="pro-only-footer">
        <div><?php echo wp_kses(sprintf(fpframework()->_('FPF_PRO_MODAL_PRESALES_QUESTIONS'), $preSalesUrl), $allowed_tags); ?></div>
        <div><?php echo wp_kses(sprintf(fpframework()->_('FPF_PRO_MODAL_UNLOCK_PRO_FEATURES'), $upgradeProUrl), $allowed_tags); ?></div>
    </div>
</div>