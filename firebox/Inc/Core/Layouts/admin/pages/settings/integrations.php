<?php
/**
 * @package         FireBox
 * @version         3.1.5 Free
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2026 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

$integrations = (array) $this->data->get('integrations', []);
if (!$integrations)
{
	return;
}
?>
<div class="fb-integrations-settings">
	<div class="fb-integrations-settings-grid">
		<?php foreach ($integrations as $integration): ?>
			<?php
			$integration = is_array($integration) ? $integration : (array) $integration;
			$label = isset($integration['label']) ? $integration['label'] : '';
			$slug = isset($integration['slug']) ? $integration['slug'] : '';
			$connected = !empty($integration['connected']);
			$api_key = isset($integration['api_key']) ? (string) $integration['api_key'] : '';
			$docs_url = isset($integration['docs_url']) ? $integration['docs_url'] : '';
			$connection_type = isset($integration['connection_type']) ? (string) $integration['connection_type'] : 'api_key';
			$is_api_key = $connection_type === 'api_key';
			$is_locked = !empty($integration['locked']);
			$status_class = $is_locked ? 'is-locked' : ($connected ? 'is-connected' : 'is-disconnected');
			$status_label = isset($integration['status_label']) ? (string) $integration['status_label'] : firebox()->_($connected ? 'FB_INTEGRATION_CONNECTED' : 'FB_INTEGRATION_DISCONNECTED');
			$logo_url = isset($integration['logo_url']) ? $integration['logo_url'] : '';
			$plugin_message = isset($integration['plugin_message']) ? (string) $integration['plugin_message'] : '';
			$plugin_action_label = isset($integration['plugin_action_label']) ? (string) $integration['plugin_action_label'] : '';
			$plugin_action_url = isset($integration['plugin_action_url']) ? (string) $integration['plugin_action_url'] : '';
			$locked_message = isset($integration['locked_message']) ? (string) $integration['locked_message'] : '';
			$upgrade_badge_label = isset($integration['upgrade_badge_label']) ? (string) $integration['upgrade_badge_label'] : '';
			$upgrade_label = isset($integration['upgrade_label']) ? (string) $integration['upgrade_label'] : '';
			$upgrade_plan = isset($integration['upgrade_plan']) ? (string) $integration['upgrade_plan'] : 'Pro';
			$current_plan = isset($integration['current_plan']) ? (string) $integration['current_plan'] : 'free';
			/* translators: %s: Integration label. */
			$upgrade_feature = sprintf(__('%s Integration', 'firebox'), $label);
			$show_api_key_label = __('Show API Key', 'firebox');
			$hide_api_key_label = __('Hide API Key', 'firebox');
			?>
				<div class="fb-integration-card<?php echo $connected ? ' is-connected' : ''; ?>" data-integration="<?php echo esc_attr($slug); ?>" data-connected="<?php echo esc_attr($connected ? '1' : '0'); ?>">
					<div class="fb-integration-card-top">
						<div class="fb-integration-card-title-wrap">
							<?php if (!empty($logo_url)): ?>
								<img class="fb-integration-logo" src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($label); ?>" />
							<?php endif; ?>
							<div class="fb-integration-card-heading">
								<h5 class="fb-integration-card-title"><?php echo esc_html($label); ?></h5>
								<?php if ($is_locked): ?>
									<a
										class="fb-integration-upgrade-badge fpf-modal-opener"
										href="#"
										data-fpf-modal="#fireboxUpgradeToPlan"
										data-fpf-modal-item="<?php echo esc_attr($upgrade_feature); ?>"
										data-current-plan="<?php echo esc_attr($current_plan); ?>"
										data-upgrade-plan="<?php echo esc_attr($upgrade_plan); ?>"
										aria-label="<?php echo esc_attr($upgrade_badge_label); ?>"
									>
										<?php echo esc_html($upgrade_badge_label); ?>
									</a>
								<?php else: ?>
									<span class="fb-integration-status <?php echo esc_attr($status_class); ?>"><?php echo esc_html($status_label); ?></span>
								<?php endif; ?>
							</div>
						</div>
						<div class="fb-integration-card-actions">
							<?php if (!empty($docs_url)): ?>
								<a class="fb-integration-help-link fb-integration-help-link-compact" href="<?php echo esc_url($docs_url); ?>" target="_blank" aria-label="<?php echo esc_attr(fpframework()->_('FPF_WHERE_TO_FIND_API_KEY')); ?>">
									<span class="dashicons dashicons-editor-help" aria-hidden="true"></span>
								</a>
							<?php endif; ?>
						</div>
					</div>

					<div class="fb-integration-card-body">
						<?php if ($is_locked): ?>
							<div class="fb-integration-locked-wrapper">
								<?php if (!empty($locked_message)): ?>
									<div class="fb-integration-locked-message"><?php echo esc_html($locked_message); ?></div>
								<?php endif; ?>
								<?php if (!empty($upgrade_label)): ?>
									<a
										class="fpf-button upgrade fb-integration-locked-action fpf-modal-opener"
										href="#"
										data-fpf-modal="#fireboxUpgradeToPlan"
										data-fpf-modal-item="<?php echo esc_attr($upgrade_feature); ?>"
										data-current-plan="<?php echo esc_attr($current_plan); ?>"
										data-upgrade-plan="<?php echo esc_attr($upgrade_plan); ?>"
									>
										<?php echo esc_html($upgrade_label); ?>
									</a>
								<?php endif; ?>
							</div>
						<?php elseif ($is_api_key): ?>
							<div class="fb-integration-input-wrapper">
								<div class="fb-integration-input-row">
									<div class="fb-integration-input-control">
										<input type="password" class="fpf-field-item fpf-control-input-item text fb-integration-api-key<?php echo $connected ? ' is-readonly' : ''; ?>" autocomplete="off" value="<?php echo esc_attr($api_key); ?>" placeholder="<?php echo esc_attr(firebox()->_('FB_INTEGRATION_API_KEY')); ?>" aria-label="<?php echo esc_attr(firebox()->_('FB_INTEGRATION_API_KEY')); ?>" <?php echo $connected ? 'readonly="readonly"' : ''; ?> />
										<button
											type="button"
											class="fpf-button fb-integration-visibility-toggle"
											aria-label="<?php echo esc_attr($show_api_key_label); ?>"
											title="<?php echo esc_attr($show_api_key_label); ?>"
											aria-pressed="false"
											data-show-label="<?php echo esc_attr($show_api_key_label); ?>"
											data-hide-label="<?php echo esc_attr($hide_api_key_label); ?>"
										>
											<span class="dashicons dashicons-visibility" aria-hidden="true"></span>
										</button>
									</div>
									<button type="button" class="fpf-button primary fb-integration-connect<?php echo $connected ? ' hidden' : ''; ?>">
										<?php echo esc_html(firebox()->_('FB_INTEGRATION_CONNECT')); ?>
									</button>
									<button type="button" class="fpf-button fb-integration-disconnect<?php echo !$connected ? ' hidden' : ''; ?>">
										<?php echo esc_html(firebox()->_('FB_INTEGRATION_DISCONNECT')); ?>
									</button>
								</div>
							</div>
						<?php else: ?>
							<div class="fb-integration-plugin-wrapper">
								<?php if (!empty($plugin_message)): ?>
									<div class="fb-integration-plugin-message"><?php echo esc_html($plugin_message); ?></div>
								<?php endif; ?>
								<?php if (!empty($plugin_action_url) && !empty($plugin_action_label)): ?>
									<a class="fpf-button primary fb-integration-plugin-action" href="<?php echo esc_url($plugin_action_url); ?>">
										<?php echo esc_html($plugin_action_label); ?>
									</a>
								<?php endif; ?>
							</div>
						<?php endif; ?>

						<div class="fb-integration-message hidden"></div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
