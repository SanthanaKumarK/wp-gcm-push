<?php

/**
 * Core Gcm push settings class
 *
 * @author  SS4U Development Team <info@softsolutions4u.com>
 * @version 1.0.0
 */

namespace GcmPush\Controllers;

/**
 * Core Gcm push settings class
 *
 * @author  SS4U Development Team <info@softsolutions4u.com>
 * @version 1.0.0
 */
class GcmPushUserListTable extends \WP_List_Table
{
    /**
     * Default constructor
     */
    function __construct() {
        parent::__construct(array(
            'ajax' => false
        ));
    }

    /**
     * Get the value of the column
     * 
     * @param array  $item        Row data
     * @param string $column_name Column name
     * 
     * @return string Column string 
     */
    function column_default($item, $column_name){
        switch($column_name){
            case 'reg_id':
            case 'os':
            case 'created_at':
                return $item[$column_name];
            default:
                return print_r($item, true);
        }
    }

    /**
     * Parse column cb(check box)
     * 
     * @param array $item A singular item (one full row's worth of data)
     * 
     * @return string Text to be placed inside the column
     */
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            'bulk-delete',
            $item['ID']
        );
    }

    /**
     * Columns array
     * 
     * @return array An associative array containing column information
     */
    function get_columns(){
        $columns = array(
            'cb'         => '<input type="checkbox" />',
            'reg_id'     => 'Device Id',
            'os'         => 'Os',
            'created_at' => 'Registerd Date'
        );
        return $columns;
    }

    /**
     * Get the sortable columns list
     * 
     * @return array An associative array containing all the columns that should be sortable
     */
    function get_sortable_columns() {
        $sortable_columns = array(
            'reg_id'     => array('reg_id',false),     //true means it's already sorted
            'os'         => array('os',false),
            'created_at' => array('created_at',false)
        );
        return $sortable_columns;
    }

    /**
     * Bulk actions list
     * 
     * @return array An associative array containing all the bulk actions
     */
    function get_bulk_actions() {
        $actions = array(
            'delete'    => 'Delete'
        );
        return $actions;
    }

    /**
     * Perform the bulk action
     */
    function process_bulk_action() {
        if ('delete' === $this->current_action()) {
            $deleteIds = esc_sql($_REQUEST['bulk-delete']);
            foreach ($deleteIds as $id) {
              $this->deleteUser($id);
            }
        }
    }

    /** 
     * Prepare the data to show in table
     */
    function prepare_items() {

        $limit    = 20;
        $columns  = $this->get_columns();
        $hidden   = array();
        $sortable = $this->get_sortable_columns();
        
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        $this->process_bulk_action();
        
        $users       = $this->getUsers();        
        $currentPage = $this->get_pagenum();
        $totalItems  = count($users);
        $data        = array_slice($users, (($currentPage-1)*$limit), $limit);
        
        $this->items = $data;
        $this->set_pagination_args(array(
            'total_items' => $totalItems,
            'per_page'    => $limit,
            'total_pages' => ceil($totalItems/$limit)
        ));
    }

    /**
    * Retrieve customer’s data from the database
    *
    * @param int $limit Number of entries per page
    * @param int $page  Page number
    *
    * @return array Users array
    */
    public function getUsers($limit = 5, $page = 1)
    {
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}gcm_push_users";

        if (!empty($_REQUEST['orderby'])) {
            $sql .= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
            $sql .=!empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
        }

        $sql .= " LIMIT $limit";

        $sql .= ' OFFSET ' . ( $page - 1 ) * $limit;

        $result = $wpdb->get_results($sql, 'ARRAY_A');

        return $result;
    }

    /**
     * Delete the user from database
     * 
     * @param int $id User id
     */
    public function deleteUser($id)
    {
        global $wpdb;

        $wpdb->delete(
            "{$wpdb->prefix}gcm_push_users",
            array('ID' => $id),
            array('%d')
        );
    }
}