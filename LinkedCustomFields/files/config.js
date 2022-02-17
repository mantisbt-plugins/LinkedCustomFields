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
LCF.mappingTarget = null;
LCF.mappings = {};

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
 * Retrieves and caches mappings
 * @param {int} sourceFieldId
 * @returns {*}
 */
LCF.getMappings = function (sourceFieldId) {
    if(!sourceFieldId || LCF.cachedValues.hasOwnProperty(sourceFieldId)) {
        return;
    }

    LCF.sourceFieldId = sourceFieldId;
    return $.getJSON(this.rest_api('mapping') + '/' + sourceFieldId)
        .done(function (data) {
            LCF.mappings = data;
        })
        .fail(function() {
            console.error('Error occurred while retrieving mappings');
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
        let srcFieldValues = LCF.cachedValues[srcFieldId];
        let tgtFieldValues = LCF.cachedValues[targetFieldId];

        // Add target CF's values to each select
        for (let srcField in srcFieldValues) {
            let control = $('#custom_field_linked_values_' + srcField);

            // Populate the select with target field values
            control.empty();
            for (let tgtField in tgtFieldValues) {
                control.append($('<option></option>', {
                    text: tgtFieldValues[tgtField]
                }));
            }

            // Select default values
            if (LCF.mappingTarget === targetFieldId) {
                control.val(LCF.mappings[srcFieldValues[srcField]]);
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

/**
 * Deselects all linked values.
 * @param id
 */
LCF.clearSelection = function (id) {
    let targetList = '#custom_field_linked_values_' + id;
    $(targetList + ' option:selected').prop("selected", false);
};

$(function() {
    let sourceFieldId = $('#custom_field_id').val();
    let targetField = $('#target_custom_field');

    LCF.mappingTarget = targetField.val();
    LCF.mappings = targetField.data('mappings');

    // Retrieve CF data for mappings, parent and child custom fields in parallel
    $.when(
        LCF.getCustomFieldOptions(sourceFieldId),
        LCF.getCustomFieldOptions(targetField.val())
        // LCF.getMappings(sourceFieldId)
        ).done(function () {
            LCF.refreshTargetFieldOptions(targetField.val());
            // Remove no longer needed 'hidden' class, as we have set display prop by now
            $('#link_area').toggleClass('hidden', false);
        });

    targetField.on('change', function () {
        LCF.refreshTargetFieldOptions(targetField.val());
    });

    $(".lcf_clear").on('click', function () {
       LCF.clearSelection($(this).data('id'));
    });
});
