<?php

namespace Drupal\megamenu\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class MegaMenuSettingsForm.
 *
 * @package Drupal\megamenu\Form
 */
class MegaMenuSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'megamenu_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'megamenu.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $megamenu_service = \Drupal::service('megamenu');
    $megamenu_options = $this->config('megamenu.settings')->getRawData();

    // Container
    $form['megamenu'] = [
      '#type' => 'container',
    ];

    // Tabs
    $form['megamenu_tabs'] = [
      '#type' => 'details',
      '#title' => t('Mega Menu global setttings'),
      '#open' => TRUE,
      '#weight' => 99,
    ];

    // Responsive & Mobile
    $form = $this->buildResponsiveMobileOptions($form, $form_state, $megamenu_service);
    // Script Configuration
    $form = $this->buildScriptConfigurationOptions($form, $form_state, $megamenu_service);
    // Custom Styles
    $form = $this->buildCustomStylesOptions($form, $form_state, $megamenu_service);
    // Misc
    $form = $this->buildMiscOptions($form, $form_state, $megamenu_service);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Set megamenu options.
    if ($megamenu_options = $form_state->getValue('megamenu')) {
      ksort($megamenu_options);

      // Update javascript options.
      megamenu_set_js_options($megamenu_options);
    }

    parent::submitForm($form, $form_state);
  }

  /**
   * Responsive & Mobile
   */
  public function buildResponsiveMobileOptions(array $form, FormStateInterface $form_state, $megamenu_service) {
    $form['responsive_mobile_options'] = [
      '#type' => 'details',
      '#title' => t('Responsive & mobile'),
      '#group' => 'megamenu_tabs',
      '#open' => TRUE,
    ];
    $form['responsive_mobile_options']['retractor_display_strategy'] = [
      '#type' => 'radios',
      '#title' => t('Retractor display strategy'),
      '#description' => t('Choose when to display retractors. Note that using Touch Detection may result in the Close button appearing on desktop browsers that support touch events.'),
      '#options' => [
        'responsive'	=> t('Responsive (display below responsive breakpoint)'),
        'mobile'	=> t('Mobile (use megamenu_is_mobile() mobile device detection)'),
        'touch'	=> t('Touch Detection (display when browser supports touch events)'),
      ],
      '#default_value' => $megamenu_service->getOption('retractor_display_strategy'),
      '#parents' => ['megamenu', 'retractor_display_strategy'],
    ];
    $form['responsive_mobile_options']['responsive_breakpoint'] = [
      '#type' => 'textfield',
      '#title' => t('Responsive breakpoint'),
      '#description' => t('Represents the viewport width in pixels at which the menu will collapse to mobile menu (do not include units). Default value is 959.'),
      '#default_value' => $megamenu_service->getOption('responsive_breakpoint'),
      '#parents' => ['megamenu', 'responsive_breakpoint'],
    ];
    return $form;
  }

  /**
   * Script Configuration
   */
  public function buildScriptConfigurationOptions(array $form, FormStateInterface $form_state, $megamenu_service) {
    $form['script_config'] = [
      '#type' => 'details',
      '#title' => t('Script configuration'),
      '#group' => 'megamenu_tabs',
    ];
    $form['script_config']['remove_conflicts'] = [
      '#type' => 'checkbox',
      '#title' => t('Remove JS conflicts'),
      '#description' => t('Use this option to disable any event bindings added with jQuery unbind() or off() before Mega Menu script runs. Disable this option if you want to bind your own events, or have other scripts act on the menu.'),
      '#default_value' => $megamenu_service->getOption('remove_conflicts'),
      '#parents' => ['megamenu', 'remove_conflicts'],
    ];
    $form['script_config']['reposition_on_load'] = [
      '#type' => 'checkbox',
      '#title' => t('Reposition submenus on window.load'),
      '#description' => t('Use this option to reposition submenus after other assets have loaded. The option is useful if using @font-face fonts in the menu.'),
      '#default_value' => $megamenu_service->getOption('reposition_on_load'),
      '#parents' => ['megamenu', 'reposition_on_load'],
    ];
    $form['script_config']['touch_off_close'] = [
      '#type' => 'checkbox',
      '#title' => t('Touch-off close'),
      '#description' => t('Use this option to close all submenus when the user clicks or touches off of the menu. If you disable this option, make sure you leave your users with another way to close the submenu.'),
      '#default_value' => $megamenu_service->getOption('touch_off_close'),
      '#parents' => ['megamenu', 'touch_off_close'],
    ];
    $form['script_config']['intent_delay'] = [
      '#type' => 'textfield',
      '#title' => t('Hover intent delay'),
      '#description' => t('Use this option to set time to wait until closing the submenu after hover-out in milliseconds.'),
      '#default_value' => $megamenu_service->getOption('intent_delay'),
      '#parents' => ['megamenu', 'intent_delay'],
    ];
    $form['script_config']['intent_interval'] = [
      '#type' => 'textfield',
      '#title' => t('Hover intent interval'),
      '#description' => t('Use this option to set polling interval for mouse comparisons in milliseconds.'),
      '#default_value' => $megamenu_service->getOption('intent_interval'),
      '#parents' => ['megamenu', 'intent_interval'],
    ];
    $form['script_config']['intent_threshold'] = [
      '#type' => 'textfield',
      '#title' => t('Hover intent threshold'),
      '#description' => t('Use this option to set maximum number of pixels over the target that the mouse can move since the last poll to be considered an intentional hover.'),
      '#default_value' => $megamenu_service->getOption('intent_threshold'),
      '#parents' => ['megamenu', 'intent_threshold'],
    ];
    $form['script_config']['scrollto_offset'] = [
      '#type' => 'textfield',
      '#title' => t('ScrollTo offset'),
      '#description' => t('Use this option to set offset to leave when scrolling in pixels.'),
      '#default_value' => $megamenu_service->getOption('scrollto_offset'),
      '#parents' => ['megamenu', 'scrollto_offset'],
    ];
    $form['script_config']['scrollto_duration'] = [
      '#type' => 'textfield',
      '#title' => t('ScrollTo duration'),
      '#description' => t('Use this option to set the scroll animation duration in milliseconds. (The actual speed will be determined by the distance that needs to be traveled.)'),
      '#default_value' => $megamenu_service->getOption('scrollto_duration'),
      '#parents' => ['megamenu', 'scrollto_duration'],
    ];
    $form['script_config']['collapse_after_scroll'] = [
      '#type' => 'checkbox',
      '#title' => t('Collapse menu after scroll to (mobile)'),
      '#description' => t('Use this option to collapse the menu after the scroll completes, when an item with enabled scrollto is clicked on mobile.'),
      '#default_value' => $megamenu_service->getOption('collapse_after_scroll'),
      '#parents' => ['megamenu', 'collapse_after_scroll'],
    ];
    return $form;
  }

  /**
   * Custom Styles
   */
  public function buildCustomStylesOptions(array $form, FormStateInterface $form_state, $megamenu_service) {
    $form['custom_styles_options'] = [
      '#type' => 'details',
      '#title' => t('Custom styles'),
      '#group' => 'megamenu_tabs',
    ];
    $form['custom_styles_options']['custom_styles'] = [
      '#type' => 'textarea',
      '#title' => t('Custom CSS styles (experimental)'),
      '#description' => t('Use this field to set styles to apply custom styles.'),
      '#default_value' => $megamenu_service->getOption('custom_styles'),
      '#parents' => ['megamenu', 'custom_styles'],
    ];
    $form['custom_styles_options']['custom_styles_mobile'] = [
      '#type' => 'textarea',
      '#title' => t('Custom CSS styles - mobile (experimental)'),
      '#description' => t('Use this field to set styles to apply below the responsive breakpoint only.'),
      '#default_value' => $megamenu_service->getOption('custom_styles_mobile'),
      '#parents' => ['megamenu', 'custom_styles_mobile'],
    ];
    $form['custom_styles_options']['custom_styles_desktop'] = [
      '#type' => 'textarea',
      '#title' => t('Custom CSS styles - desktop (experimental)'),
      '#description' => t('Use this field to set styles to apply above the responsive breakpoint only.'),
      '#default_value' => $megamenu_service->getOption('custom_styles_desktop'),
      '#parents' => ['megamenu', 'custom_styles_desktop'],
    ];
    return $form;
  }

  /**
   * Misc
   */
  public function buildMiscOptions(array $form, FormStateInterface $form_state, $megamenu_service) {
    $form['misc'] = [
      '#type' => 'details',
      '#title' => t('Miscellaneous'),
      '#group' => 'megamenu_tabs',
    ];
    $form['misc']['accessible'] = [
      '#type' => 'checkbox',
      '#title' => t('Enable accessibility features'),
      '#description' => t('Use this option to allow users to tab through the menu and highlight focused elements.'),
      '#default_value' => $megamenu_service->getOption('accessible'),
      '#parents' => ['megamenu', 'accessible'],
    ];
    $form['misc']['fontawesome_load'] = [
      '#type' => 'checkbox',
      '#title' => t('Load FontAwesome library'),
      '#description' => t('You can disable the option if you are already loading FontAwesome elsewhere.'),
      '#default_value' => $megamenu_service->getOption('fontawesome_load'),
      '#parents' => ['megamenu', 'fontawesome_load'],
    ];
    $form['misc']['fontawesome_use_cdn'] = [
      '#type' => 'checkbox',
      '#title' => t('FontAwesome CDN'),
      '#description' => t('Use this option to load CDN version of FontAwesome.'),
      '#default_value' => $megamenu_service->getOption('fontawesome_use_cdn'),
      '#parents' => ['megamenu', 'fontawesome_use_cdn'],
    ];
    $form['misc']['disable_megamenu_css'] = [
      '#type' => 'checkbox',
      '#title' => t('Disable core CSS styles'),
      '#description' => t('Use this option to disable loading of core CSS styles. Don\'t check unless you include CSS styles elsewhere.'),
      '#default_value' => $megamenu_service->getOption('disable_megamenu_css'),
      '#parents' => ['megamenu', 'disable_megamenu_css'],
    ];
    return $form;
  }

}
