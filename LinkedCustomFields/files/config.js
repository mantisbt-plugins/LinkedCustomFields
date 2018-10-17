/**
 * Copyright (c) 2011 Robert Munteanu (robert@lmn.ro)
 * Copyright (c) 2018 Damien Regad
 *
 * Linked custom fields for MantisBT is free software:
 * you can redistribute it and/or modify it under the terms of the GNU
 * General Public License as published by the Free Software Foundation,
 * either version 2 of the License, or (at your option) any later version.
 *
 * Linked custom fields plugin for MantisBT is distributed in the hope
 * that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Linked custom fields plugin for MantisBT.
 * If not, see <http://www.gnu.org/licenses/>.
 */

"use strict";

/**
 * Namespace for global function and variables used in list_action.js
 */
var LCF = LCF || {};

/**
 * Return MantisBT REST API URL for given endpoint
 * @param {string} endpoint
 * @returns {string} REST API URL
 */
LCF.rest_api = function (endpoint) {
    // Using the full URL (through index.php) to avoid issues on sites
    // where URL rewriting is not working
    return "/mantis/api/rest/index.php/plugins/LinkedCustomFields/" + endpoint;
};

/**
 * Cache for custom field values retrieved via AJAX
 */
LCF.cachedValues = {};


/**
 * Retrieves and caches CF values via AJAX.
 * @param {int} targetFieldId
 * @returns {*}
 */
LCF.getCustomFieldOptions = function (targetFieldId) {
    if(!targetFieldId || LCF.cachedValues.hasOwnProperty(targetFieldId)) {
        return;
    }

    // Disable target CF values selects while AJAX is in progress
    $('#loading').toggle(true);
    $('.lcf_target')
        .css('cursor', 'progress')
        .prop('disabled', true);

    return $.getJSON(this.rest_api('values') + '/' + targetFieldId)
        .done(function (data) {
            LCF.cachedValues[targetFieldId] = data;
        })
        .fail(function() {
            console.error('Error occurred while retrieving data');
        });
};

/**
 * Retrieve CF data for given field id and update UI.
 * @param targetFieldId
 */
LCF.refreshTargetFieldOptions = function (targetFieldId) {

    /**
     * Populate selects with CF's values and enable them
     * @param targetFieldId
     */
    function updateUI (targetFieldId) {
        let srcFieldId = $('#custom_field_id').val();
        let tgtFieldValues = LCF.cachedValues[targetFieldId];

        // Add target CF's values to each select
        for (let srcField in LCF.cachedValues[srcFieldId]) {
            let control = $('#custom_field_linked_values_' + srcField);

            control.empty();
            for (let tgtField in tgtFieldValues) {
                control.append($('<option></option>', {
                    value: tgtField,
                    text: tgtFieldValues[tgtField]
                }));
            }
        }

        // Enable target CF values selects
        $('#loading').toggle(false);
        $('.lcf_target')
            .css('cursor', 'default')
            .prop('disabled', false);
    }

    // Hide target area when child CF is set to None
    // $('#link_area').toggleClass('hidden', targetFieldId == "");
    $('#link_area').toggle(targetFieldId !== "");

    if(targetFieldId !== "" && !LCF.cachedValues.hasOwnProperty(targetFieldId)) {
        $.when(this.getCustomFieldOptions(targetFieldId))
            .then(function() {
                updateUI(targetFieldId);
            });
    } else {
        updateUI(targetFieldId);
    }
};

jQuery(document).ready(function() {
    let targetField = $('#target_custom_field');

    // Retrieve CF data for parent and child custom fields in parallel
    $.when(
        LCF.getCustomFieldOptions($('#custom_field_id').val()),
        LCF.getCustomFieldOptions(targetField.val())
        ).done(function () {
            LCF.refreshTargetFieldOptions(targetField.val());
            // Remove no longer needed 'hidden' class, as we have set display prop by now
            $('#link_area').toggleClass('hidden', false);
        });

    targetField.change(function() {
        LCF.refreshTargetFieldOptions(targetField.val());
    });
});
