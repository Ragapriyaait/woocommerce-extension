<?php
/*
Plugin Name: Gravity Forms WPMktgEngine Extension
Description: This plugin requires the WPMKtgEngine or Genoo plugin installed before order to activate.
Version: 2.2.42
Requires PHP: 7.1
Author: Genoo LLC
*/
/*
    Copyright 2015  WPMKTENGINE, LLC  (web : http://www.genoo.com/)
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
register_activation_hook(__FILE__, function () {
    // Basic extension data
    global $wpdb;
    $fileFolder = basename(dirname(__FILE__));
    $file = basename(__FILE__);
    $filePlugin = $fileFolder . DIRECTORY_SEPARATOR . $file;
    // Activate?
    $activate = false;
    $isGenoo = false;
    // Get api / repo
    if (
        class_exists('\WPME\ApiFactory') &&
        class_exists('\WPME\RepositorySettingsFactory')
    ) {
        $activate = true;
        $repo = new \WPME\RepositorySettingsFactory();
        $api = new \WPME\ApiFactory($repo);
        if (class_exists('\Genoo\Api')) {
            $isGenoo = true;
        }
    } elseif (
        class_exists('\Genoo\Api') &&
        class_exists('\Genoo\RepositorySettings')
    ) {
        $activate = true;
        $repo = new \Genoo\RepositorySettings();
        $api = new \Genoo\Api($repo);
        $isGenoo = true;
    } elseif (
        class_exists('\WPMKTENGINE\Api') &&
        class_exists('\WPMKTENGINE\RepositorySettings')
    ) {
        $activate = true;
        $repo = new \WPMKTENGINE\RepositorySettings();
        $api = new \WPMKTENGINE\Api($repo);
    }
    // 1. First protectoin, no WPME or Genoo plugin
    if ($activate == false && $isGenoo == false) { ?>
  <div class="alert">
<p style="font-family:Segoe UI;font-size:14px;">This plugin requires the WPMKtgEngine or Genoo plugin installed  order to activate</p>
</div>
    <?php
    die();
    genoo_wpme_deactivate_plugin(
        $filePlugin,
        'This extension requires WPMktgEngine or Genoo plugin to work with.'
    );
    } else {// Make ACTIVATE calls if any?}
        //creating tables setting save
        $sql = "CREATE TABLE {$wpdb->prefix}gf_settings (
            id mediumint(8) unsigned not null auto_increment,
            form_id mediumint(8) unsigned not null,
            is_active tinyint(1),
            select_lead_folder varchar(255),
            select_leadtype  varchar(255),
            select_folder  varchar(255),
            select_email varchar(255),
            select_webinar  varchar(250),
            PRIMARY KEY  (id),
            UNIQUE KEY form_id (form_id)
                  ) $charset_collate;";
        gf_upgrade()->dbDelta($sql);}
});

/**
 * Plugin Updates
 */

include_once plugin_dir_path(__FILE__) . 'deploy/updater.php';
wpme_gravity_forms_updater_init(__FILE__);

add_action(
    'wpmktengine_init',
    function ($repositarySettings, $api, $cache) {
        // Use the Settings, Api or Cache to do things on load of WPME if you need to
        // For example, add custom settings to WPME screen
        add_filter(
            'wpmktengine_tools_extensions_widget',
            function ($array) {
                $array['Gravity Forms WPMktgEngine Extension'] =
                    '<span style="color:green">Active</span>' . $r;
                return $array;
            },
            10,
            1
        );
        add_filter(
            'wpmktengine_settings_sections',
            function ($sections) {
                $sections[] = [
                    'id' => 'Extension',
                    'title' => __('Extension', 'wpmktengine'),
                ];
                return $sections;
            },
            10,
            1
        );
        add_filter(
            'wpmktengine_settings_fields',
            function ($fields) {
                $fields['Extension'] = [
                    [
                        'name' => 'extension_cipher_key',
                        'id' => 'extension_cipher_key',
                        'label' => __('Cipher', 'wpmktengine'),
                        'type' => 'text',
                        'default' => '',
                        'attr' => [
                            'style' => 'display: block',
                        ], // Custom attributes, js etc.
                        'desc' => __('Description', 'wpmktengine'),
                    ],
                    [
                        'label' => __('Dropdown', 'wpmktengine'),
                        'name' => 'extension_dropdown_key',
                        'id' => 'extension_dropdown_key',
                        'type' => 'select',
                        'options' => [
                            0 => 'Select',
                        ],
                    ],
                ];
                return $fields;
            },
            10,
            1
        );
    },
    10,
    3
);

add_action('gform_after_submission', 'access_entry_via_field', 10, 2);
function access_entry_via_field($entry, $form)
{
    global $wpdb, $WPME_API;
    $id = isset($entry['form_id']) ? $entry['form_id'] : 0;
    if ($id != 0):
        $gf_addon_wpextenstion = $wpdb->prefix . 'gf_settings';
        $form_settings = $wpdb->get_row(
            "SELECT * from $gf_addon_wpextenstion WHERE form_id = $id"
        );
        $select_folder_id = isset($form_settings->select_folder)
            ? $form_settings->select_folder
            : '';
        $select_lead_id = isset($form_settings->select_leadtype)
            ? $form_settings->select_leadtype
            : '';
        $select_email_id = isset($form_settings->select_email)
            ? $form_settings->select_email
            : '';
        $select_webinar = isset($form_settings->select_webinar)
            ? $form_settings->select_webinar
            : '';
        if ($select_lead_id != ''):
            $values = [];
            $values['form_name'] = $form['title'];
            $values['client_ip_address'] = $entry['ip'];
            $values['lead_type_id'] = $select_lead_id;
            //$values['form_type'] = 'opt-in form';
            $values['page_url'] = $entry['source_url'];
            $values['form_type'] = 'GF';
            if (!empty($select_email_id)):
                $values['confirmation_email_id'] = $select_email_id;
            endif;
            if (!empty($select_webinar)):
                $values['webinar_id'] = $select_webinar;
            endif;
            foreach ($form['fields'] as $field):
                // echo $field['type'];
                if ($field['type'] == 'email'):
                    $values['email'] = $entry[$field['id']];
                endif;
                if ($field['type'] == 'phone' && !empty($entry[$field['id']])):
                    $values['phone'] = $entry[$field['id']];
                endif;
                if (
                    $field['type'] == 'website' &&
                    !empty($entry[$field['id']])
                ):
                    $values['web_site_url'] = $entry[$field['id']];
                endif;
                if ($field['type'] == 'address'):
                    $field_id = $field['id'];
                    $values['address1'] = $entry[$field_id . '.1'];
                    $values['address2'] = $entry[$field_id . '.2'];
                    $values['city'] = $entry[$field_id . '.3'];
                    $values['state'] = $entry[$field_id . '.4'];
                    $values['province'] = $entry[$field_id . '.4'];
                    $values['zip'] = $entry[$field_id . '.5'];
                    $values['country'] = $entry[$field_id . '.6'];
                endif;
                if ($field['type'] == 'consent'):
                    $values['c00gdprconsent'] =
                        $entry[$field['id'] . '.1'] != 1 ? '' : 1;
                    if (!empty($field->description)):
                        $values['c00gdprconsentmsg'] = $field->description;
                    endif;
                endif;
                if ($field['type'] == 'name'):
                    $field_id = $field['id'];
                    $values['first_name'] = $entry[$field_id . '.3'];
                    $values['last_name'] = $entry[$field_id . '.6'];
                endif;
                $all_default_types = [
                    'textarea',
                    'text',
                    'multiselect',
                    'checkbox',
                    'number',
                    'captcha',
                    'fileupload',
                    'list',
                    'product',
                    'quantity',
                    'creditcard',
                    'post_title',
                    'html',
                    'select',
                    'page',
                    'section',
                    'radio',
                    'post_category',
                    'post_image',
                    'post_tags',
                    'post_excerpt',
                    'post_custom_field',
                    'option',
                    'total',
                    'shipping',
                    'post_content',
                    'date',
                    'time',
                    'hidden',
                ];
                //check all default types which is not a premapped types
                if (
                    in_array($field['type'], $all_default_types) &&
                    !empty($entry[$field['id']]) &&
                    !empty($field->thirdPartyInput)
                ):
                    $firstindex = strstr($field->thirdPartyInput, 'c00');
                    $lastindex = strstr($field->thirdPartyInput, 'date');
                    if ($firstindex == true && $lastindex == true):
                        $date = date_create($entry[$field['id']]);
                        $date = date_format($date, 'Y-m-d');
                        $values[$field->thirdPartyInput] =
                            $date . 'T' . '00:00:00+00:00';
                    elseif ($firstindex == false && $lastindex == true):
                        $date = date_create($entry[$field['id']]);
                        $date = date_format($date, 'm/d/Y');
                        $values[$field->thirdPartyInput] = $date;
                    elseif (
                        $field['type'] == 'radio' &&
                        $field->thirdPartyInput == 'c00eudatasubject' &&
                        !empty($entry[$field['id']])
                    ):
                        $values['c00eudatasubject'] = '1';
                    elseif (!empty($entry[$field['id']])):
                        $values[$field->thirdPartyInput] = $entry[$field['id']];
                    endif;
                endif;
                if ($field['type'] == 'checkbox'):
                    $inputs = $field->get_entry_inputs();
                    foreach ($inputs as $inputsfields):
                        if (!empty($entry[$inputsfields['id']])):
                            $values[$field->thirdPartyInput] = '1';
                        endif;
                    endforeach;
                endif;
            endforeach;

            //changed callcustom api for leads submit
            if (method_exists($WPME_API, 'callCustom')):
                try {
                    $response = $WPME_API->callCustom(
                        '/leadformsubmit',
                        'POST',
                        $values
                    );
                    if ($WPME_API->http->getResponseCode() == 204):
                        // No values based on folderdid onchange! Ooops


                    elseif ($WPME_API->http->getResponseCode() == 200):
                    endif;
                } catch (Exception $e) {
                    if ($WPME_API->http->getResponseCode() == 404):


                        // Looks like leadfields not found
                    endif;
                }
            endif;
            $geno_ids = $response->genoo_id;
            setcookie('_gtld', $geno_ids, time() + 10 * 365 * 24 * 60 * 60);
        endif;
    endif;
}
add_action('wp_action_to_modify', function () {
    // Get WPME api object, same in both Genoo and WPME plugins
    global $WPME_API;
    // It's set on INIT, if it's not present, this hook runs too early and you
    if (!$WPME_API) {
        return;
    }
    // Do things
    // Get or save to settings repository
    $settings = $WPME_API->settingsRepo;
    // Value from custom setttings above
    $settingsCipher = $settings->getOption('extension_cipher_key', 'Extension');
    // Do something with settings value from custom settings?
    // Make api calls, that are baked into the plugin
    // 1. Get lead by email address
    try {
        $lead = $WPME_API->getLeadByEmail('lead@email.com');
    } catch (\Exception $e) {
    }
    // 2. Call custom API, newly created, etc.
    if (method_exists($WPME_API, 'callCustom')) {
        try {
            $product_id_external = 1;
            // Make a GET request, to Genoo / WPME api, for that rest endpoint
            $product = $WPME_API->callCustom(
                '/wpmeproductbyextid/' . $product_id_external,
                'GET',
                null
            );
            if ($WPME_API->http->getResponseCode() == 204) {
                // No product! Ooops
            } elseif ($WPME_API->http->getResponseCode() == 200) {
                // Good product in $product variable
            }
        } catch (Exception $e) {
            if ($WPME_API->http->getResponseCode() == 404) {
                // Looks like product not found
            }
        }
    }
    // 3. Api key?
    $apiKey = $WPME_API->key;
});

add_action('gform_loaded', ['GF__gravityform_Bootstrap', 'load'], 5);
class GF__gravityform_Bootstrap
{
    public static function load()
    {
        if (!method_exists('GFForms', 'include_addon_framework')) {
            return;
        }
        //include the class file
        require_once 'class-gravityformextension.php';
        GFAddOn::register('Gravityformextension');
    }
}

function gf_gravityform()
{
    return Gravityformextension::get_instance();
}
add_action(
    'gform_field_standard_settings',
    function ($position, $form_id) {
        // position -1 for adding third party(Genoo/WPMktgEngine Field:) as last

        if ($position == -1):

            global $WPME_API;
            //calling leadfields api for showing dropdown
            if (method_exists($WPME_API, 'callCustom')):
                try {
                    $customfields = $WPME_API->callCustom(
                        '/leadfields',
                        'GET',
                        null
                    );
                    if ($WPME_API->http->getResponseCode() == 204):
                        // No leadfields based on folderdid onchange! Ooops


                    elseif ($WPME_API->http->getResponseCode() == 200):
                        $customfieldsjson = $customfields;
                    endif;
                } catch (Exception $e) {
                }
            endif;
            // right after Admin Field Label
            // $pre_mapped_fields for should not show the premapped fields
            $pre_mapped_fields = [
                'First Name',
                'Last Name',
                'Email',
                'Address 1',
                'Address 2',
                'City',
                'State',
                'Postal Code',
                'Country',
                'Phone #',
                'Zip',
                'Province',
                'GDPR Consent',
                'GDPR Consent Text',
                'Web Site URL',
            ];
            ?>
    <div>
        <li class="thirdparty_input_setting field_setting">
         <label class="section_label" for="field_admin_label"><?php _e(
             'Genoo/WPMktgEngine Field:'
         ); ?></label>
         <select id="field_thirdparty_input" onchange="SetFieldProperty('thirdPartyInput', this.value);" class="fieldwidth-3" >
            <option value="">Do not map fields</option>
            
             <?php foreach ($customfieldsjson as $customfields):
                 //comparing labels with premapped labels in trim_custom_array
                 if (
                     !in_array(trim($customfields->label), $pre_mapped_fields)
                 ): ?>
                     <option value="<?php echo $customfields->key; ?>"> <?php echo trim(
    $customfields->label
); ?></option> <?php endif;
             endforeach; ?>
       </select>
            </li>
            </div>
              
                    <?php if (method_exists($WPME_API, 'callCustom')):
                        try {
                            $leadtypes_optional = $WPME_API->callCustom(
                                '/leadtypes',
                                'GET',
                                null
                            );
                            if ($WPME_API->http->getResponseCode() == 204):
                                // No leadfields based on folderdid onchange! Ooops


                            elseif ($WPME_API->http->getResponseCode() == 200):
                                $i = 0; ?>
                                      <div class="leadtypecheckbox" style="height:200px;overflow: auto";>
                                      <h1 class="editheader">Edit Label Here:</h1>
                                    <?php foreach (
                                        $leadtypes_optional
                                        as $leadtypes_optional_values
                                    ) { ?>
                        <li class="encrypt_setting_leadtypes field_setting">
        
                <input type="checkbox" id="field_encrypt_value<?php echo $i; ?>" name="field_encrypt_value<?php echo $i; ?>" data-id =<?php echo $i; ?> value="<?php echo $leadtypes_optional_values->id; ?>" onchange="SetFieldProperty('encryptField<?php echo $i; ?>', this.value);" />
                <label for="field_encrypt_value<?php echo $i; ?>" class="leadtype_value_label<?php echo $i; ?>" style="display:inline;">
                    <?php _e(
                        $leadtypes_optional_values->name,
                        'Gravity Forms WPMktgEngine Extension'
                    ); ?>
                    <?php gform_tooltip('form_field_encrypt_value'); ?>
                </label>  
                <input type="text" id="field_id_input_label_text" class="field_id_input_label_text<?php echo $i; ?>" value="<?php echo $leadtypes_optional_values->name; ?>" style="display: none;"/>

                <input type="text" id="field_id_input_label_text" class="field_id_input_value_text<?php echo $i; ?>" value="<?php echo $leadtypes_optional_values->id; ?>" style="display: none;"/>
            </li>
                     
                                   <?php $i++;} ?>
                   </div>
                   <div> <button type="button" class="leadtypeselected">submit</button></div>
                   <div> <button type="button" class="leadtypeupdate" style="display: none;">update</button></div>
                                   <?php
                            endif;
                        } catch (Exception $e) {
                        }
                    endif; ?>
           
           
           
         <?php
        endif; ?>
         

    <?php
    },
    10,
    2
);
// gform_editor_js function for restricting types to show Genoo/WPMktgEngine Field:
add_action('gform_editor_js', function () {
    //standard, advanced,post,price field types without premapped fields
    global $WPME_API;
    if (method_exists($WPME_API, 'callCustom')):
        try {
            $leadtypes_optional = $WPME_API->callCustom(
                '/leadtypes',
                'GET',
                null
            );
            $count = count($leadtypes_optional);
            if ($WPME_API->http->getResponseCode() == 204):
                // No leadfields based on folderdid onchange! Ooops


            elseif ($WPME_API->http->getResponseCode() == 200):
            endif;
        } catch (Exception $e) {
            //To DO
        }
    endif;

    $all_default_types = [
        'text',
        'textarea',
        'multiselect',
        'checkbox',
        'number',
        'captcha',
        'fileupload',
        'list',
        'product',
        'quantity',
        'creditcard',
        'post_title',
        'html',
        'select',
        'page',
        'section',
        'radio',
        'post_category',
        'post_image',
        'post_tags',
        'post_excerpt',
        'post_custom_field',
        'option',
        'total',
        'shipping',
        'post_content',
        'date',
        'time',
        'hidden',
    ];
    foreach ($all_default_types as $default_type): ?>
    <script type="text/javascript">
    
        var type = '<?php echo $default_type; ?>';
       
     
      fieldSettings[type] += ', .thirdparty_input_setting';
      fieldSettings[type] += ', .encrypt_setting_leadtypes';
      
    
      // Make sure our field gets populated with its saved value
    jQuery(document).on("gform_load_field_settings", function(event, field, form) {
        var leadtypescount = '<?php echo $count; ?>';
    
        var third_party_value = field['thirdPartyInput'];
        
            jQuery("#field_thirdparty_input").val(field["thirdPartyInput"]);

           for (i = 0; i < leadtypescount; i++) {

            
            jQuery("#field_encrypt_value"+i).prop( 'checked', ( rgar( field, 'encryptField'+i )) );

            
        }
                
            if(third_party_value!='leadtypes')
            {
             jQuery('.leadtypecheckbox').css('display','none');   
             jQuery('.leadtypeselected').css('display','none');   

            }

           
           
      });
     
        //binding to the load field settings event to initialize the checkbox

    </script>
   <?php endforeach;
});
//save while create the new form

function custom_logs()
{
    if (is_array($message)) {
        $message = json_encode($message);
    }
    $file = fopen('../dt.log', 'a');
    echo fwrite($file, "\n" . date('Y-m-d h:i:s') . ' :: ' . $message);
    fclose($file);
    exit();
}
add_action('plugins_loaded', 'myplugin_update_db_check');
add_action('gform_after_save_form', 'after_save_form', 10, 2);
function after_save_form($form, $is_new)
{
    global $wpdb, $WPME_API;
    $gf_form_table = $wpdb->prefix . 'gf_form';
    $gf_save_form_id = $wpdb->prefix . 'postmeta';
    $get_form_name = $wpdb->get_row(
        "SELECT * from $gf_form_table WHERE `id` = " . $form['id'] . ''
    );
    $genoo_form_id = get_post_meta($form['id'], $form['id'], true);
    $values = [];
    if ($is_new) {
        $values['form_name'] = $get_form_name->title;
        $values['form_id'] = '0';
        $values['form_type'] = 'GF';
    } else {
        $values['form_name'] = $get_form_name->title;
        $values['form_id'] = $genoo_form_id;
        $values['form_type'] = 'GF';
    }

    //changed callcustom api for Save Form
    if (method_exists($WPME_API, 'callCustom')):
        try {
            $response = $WPME_API->callCustom(
                '/saveExternalForm',
                'POST',
                $values
            );

            if ($WPME_API->http->getResponseCode() == 204):
                // No values based on form name,form id onchange! Ooops


            elseif ($WPME_API->http->getResponseCode() == 200):
                if ($genoo_form_id == $response->genoo_form_id):
                    update_post_meta(
                        $form['id'],
                        $form['id'],
                        $response->genoo_form_id
                    );
                    update_post_meta(
                        $form['id'],
                        'form_title',
                        $get_form_name->title
                    );
                else:
                    add_post_meta(
                        $form['id'],
                        $form['id'],
                        $response->genoo_form_id
                    );
                    add_post_meta(
                        $form['id'],
                        'form_title',
                        $get_form_name->title
                    );
                endif;
            endif;
        } catch (Exception $e) {
            if ($WPME_API->http->getResponseCode() == 404):


                // Looks like formname or form id not found
            endif;
        }
    endif;
}
add_filter(
    'gform_field_choice_markup_pre_render',
    function ($choice_markup, $choice) {
        if (rgar($choice, 'value') == 'First Choice') {
            return '';
        }

        return $choice_markup;
    },
    10,
    2
);
add_filter('gform_pre_render', 'populate_dropdown');
add_filter('gform_pre_validation', 'populate_dropdown');
add_filter('gform_pre_submission_filter', 'populate_dropdown');
add_filter('gform_admin_pre_render', 'populate_dropdown');

function populate_dropdown($form)
{
    global $WPME_API, $wpdb;

    // Make a GET request, to Genoo / WPME api, for that rest endpoint

    $leadtype_form_save = $wpdb->prefix . 'leadtype_form_save';

    // $inputs = array();

    $leaddetailsoptions = false;

    foreach ($form['fields'] as $field) {
        $i = 0;
        $choices = [];
        $choices[] = ['text' => 'Select a leadtype', 'value' => ''];
        $leadTypes = $wpdb->get_results(
            "select `label_name`,`label_value` from $leadtype_form_save where field_id=$field->id and form_id=$field->formId"
        );
        foreach ($leadTypes as $leadType) {
            $choices[] = [
                'text' => $leadType->label_name,
                'value' => $leadType->label_value,
            ];

            $i++;
        }
        if ($field->thirdPartyInput == 'leadtypes') {
            $leaddetailsoptions = true;
        } else {
            $leaddetailsoptions = false;
        }
    }

    if ($leaddetailsoptions) {
        $field['choices'] = $choices;
        //  $field["inputs"] = $inputs;
    }

    return $form;
}
//delete while click the delete permanantly
add_action('gform_before_delete_form', 'log_form_deleted');
function log_form_deleted($form_id)
{
    global $wpdb, $WPME_API;
    $values = [];

    $form_genoo_title = get_post_meta($form_id, 'form_title', true);
    $form_genoo_id = get_post_meta($form_id, $form_id, true);
    $values['form_name'] = $form_genoo_title;
    $values['form_id'] = $form_genoo_id;
    if (method_exists($WPME_API, 'callCustom')):
        try {
            $response = $WPME_API->callCustom(
                '/deleteGravityForm',
                'DELETE',
                $values
            );
            if ($WPME_API->http->getResponseCode() == 204):
                // No values based on form name,form id onchange! Ooops


            elseif ($WPME_API->http->getResponseCode() == 200):
                $delete = delete_post_meta($form_id, 'form_title', true);
                $deleteid = delete_post_meta($form_id, $form_id, true);
            endif;
        } catch (Exception $e) {
        }
    endif;
}

//update the hook for create new field in database addon table.

function wp_upe_upgrade_completed($upgrader_object, $options)
{
    // The path to our plugin's main file

    // Iterate through the plugins being updated and check if ours is there

    // Your action if it is your plugin
    custom_logs('testleads');
}
add_action('upgrader_process_complete', 'wp_upe_upgrade_completed', 10, 2);
add_action('admin_enqueue_scripts', 'adminEnqueueScripts', 10, 1);

add_action('wp_ajax_lead_type_option_submit', 'lead_type_option_submit');

function lead_type_option_submit()
{
    global $wpdb;

    $leadtype_form_save = $wpdb->prefix . 'leadtype_form_save';

    $leadtype_save_values = $_REQUEST['inservalues'];

    $field_id = $_REQUEST['field_id'];

    $form_id = $_REQUEST['form_id'];

    foreach ($leadtype_save_values as $leadtype_save_value) {
        $wpdb->delete($leadtype_form_save, [
            'form_id' => $form_id,
            'field_id' => $field_id,
            'label_value' => $leadtype_save_value['labelvalue'],
        ]);

        $wpdb->insert($leadtype_form_save, [
            'form_id' => $form_id,
            'field_id' => $field_id,
            'label_name' => $leadtype_save_value['label'],
            'label_value' => $leadtype_save_value['labelvalue'],
        ]);
    }
}

function adminEnqueueScripts($hook)
{
    // scripts
    wp_enqueue_script(
        'my_custom_script',
        plugin_dir_url(__FILE__) . 'includes/updatefile.js',
        [],
        '1.0'
    );
    wp_enqueue_style(
        'my_custom_style',
        plugin_dir_url(__FILE__) . 'includes/leadtype.css',
        [],
        '1.0'
    );
}

add_action('wp_head', 'myplugin_ajaxurl');
function myplugin_ajaxurl()
{
    echo '<script type="text/javascript">
                       var ajaxurl = "' .
        admin_url('admin-ajax.php') .
        '";
                     </script>';
}

require_once 'includes/api-functions.php';
?>
