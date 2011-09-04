<?php
# Copyright (c) 2011 Robert Munteanu (robert@lmn.ro)

# Linked custom fields for MantisBT is free software: 
# you can redistribute it and/or modify it under the terms of the GNU
# General Public License as published by the Free Software Foundation, 
# either version 2 of the License, or (at your option) any later version.
#
# Linked custom fields plugin for MantisBT is distributed in the hope 
# that it will be useful, but WITHOUT ANY WARRANTY; without even the 
# implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
# See the GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with Linked custom fields plugin for MantisBT.  
# If not, see <http://www.gnu.org/licenses/>.

class LinkedCustomFieldsPlugin extends MantisPlugin {
    
    public function register() {
        $this->name = plugin_lang_get("title");
        $this->description = plugin_lang_get("description");

        $this->version = "1.0";
        $this->requires = array(
			"MantisCore" => "1.2.6",
			"jQuery" => "1.3"
        );

        $this->author = "Robert Munteanu";
        $this->contact = "robert@lmn.ro";
    }
    
    public function hooks() {
        return array(
            'EVENT_MENU_MANAGE' => 'manage_custom_field_links'
        );
    }
    
    public function manage_custom_field_links( $p_is_admin ) {
        
        return array( '<a href="' . plugin_page( 'configure_custom_field_links' ) . '">' . plugin_lang_get( 'configure_custom_field_links' ) . '</a>', );
    }
    
    public function schema() {
        return array(
            array( 'CreateTableSQL', 
                array( plugin_table( 'data' ), "
                    custom_field_id    I NOTNULL,
                    custom_field_value C(255) NOTNULL DEFAULT \" '' \",
                    target_field_id    I NOTNULL,
                    target_field_values    C(255) NOTNULL DEFAULT \" '' \"
                ")
            )
        );
    }
}
?>